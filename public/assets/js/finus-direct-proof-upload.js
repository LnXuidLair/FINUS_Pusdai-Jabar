/* FINUS: private R2 direct proof uploads (5 MiB). No database writes until original form succeeds. */
(() => {
  'use strict';
  const MAX = 5 * 1024 * 1024;
  const mimeTypes = { jpg: 'image/jpeg', jpeg: 'image/jpeg', png: 'image/png', pdf: 'application/pdf' };
  const endpoint = '/transaction-proof/upload-url';
  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

  document.addEventListener('submit', event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.finusProofDirect !== '1' ||
        form.dataset.finusDirectReady === '1' || form.dataset.finusDirectPending === '1') return;
    const files = [...form.querySelectorAll('input[type="file"][data-finus-proof-purpose]')]
      .filter(input => !input.disabled && input.files && input.files.length);
    if (!files.length) return;
    // Capture BEFORE ordinary submit handlers so loading buttons are not locked prematurely.
    event.preventDefault();
    event.stopPropagation();
    const trigger = event.submitter || form.querySelector('button[type="submit"]');
    const original = trigger?.innerHTML;
    form.dataset.finusDirectPending = '1';
    if (trigger) { trigger.disabled = true; trigger.textContent = 'Mengunggah ke Cloudflare R2...'; }

    (async () => {
      const staged = [];
      for (const input of files) {
        const file = input.files[0];
        const ext = file.name.split('.').pop().toLowerCase();
        const mime = mimeTypes[ext];
        if (!mime || !file.size || file.size > MAX) {
          throw new Error('Bukti harus berupa JPG, PNG, atau PDF berukuran maksimal 5 MB.');
        }
        // All upload requests to Laravel contain metadata only, avoiding Vercel's 4.5 MB limit.
        const response = await fetch(endpoint, {
          method: 'POST', credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
          body: JSON.stringify({folder: input.dataset.finusProofPurpose,
            filename: file.name, content_type: mime, size: file.size})
        });
        if (!response.ok) {
          const data = await response.json().catch(() => ({}));
          throw new Error(data.message || 'Gagal menyiapkan upload R2. Periksa koneksi/login dan coba lagi.');
        }
        const auth = await response.json();
        // PUT is sent directly to private Cloudflare R2, not to a Vercel Function.
        let put;
        try {
          put = await fetch(auth.url, {method: 'PUT', headers: {'Content-Type': auth.content_type}, body: file});
        } catch (cause) {
          throw new Error('Upload langsung ke R2 gagal. Periksa kebijakan CORS bucket (AllowedOrigins dan PUT) serta koneksi internet.');
        }
        if (!put.ok) throw new Error('Cloudflare R2 menolak upload (HTTP ' + put.status + ').');
        staged.push({input, token: auth.token});
      }
      for (const {input, token} of staged) {
        // Token is encrypted and checked against the authenticated user and R2 object on the server.
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = input.name + '_upload_token';
        hidden.value = token;
        form.append(hidden);
        input.disabled = true;
        input.required = false;
      }
      form.dataset.finusDirectReady = '1';
      delete form.dataset.finusDirectPending;
      if (trigger) { trigger.disabled = false; trigger.innerHTML = original; }
      if (typeof form.requestSubmit === 'function') form.requestSubmit(trigger || undefined);
      else form.submit();
    })().catch(error => {
      delete form.dataset.finusDirectPending;
      if (trigger) { trigger.disabled = false; trigger.innerHTML = original; }
      form.dispatchEvent(new CustomEvent('finus-proof-upload-error', {detail: error.message}));
      // Plain error message when a page does not have its own error toast.
      if (!form.hasAttribute('data-finus-own-proof-error')) window.alert(error.message);
    });
  }, true);
})();
