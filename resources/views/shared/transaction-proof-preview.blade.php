{{-- Pratinjau bukti transaksi: akses file melalui route Laravel yang sudah memeriksa hak akses. --}}
<style>
body.finus-tproof-open { overflow: hidden !important; }
.finus-tproof { position: fixed; inset: 0; z-index: 1600; display: none; align-items: center; justify-content: center; padding: 18px; }
.finus-tproof.is-open { display: flex; }
.finus-tproof__backdrop { position: absolute; inset: 0; background: rgba(3,20,9,.72); backdrop-filter: blur(5px); }
.finus-tproof__dialog { position: relative; z-index: 1; width: min(870px, 100%); max-height: 92vh; display: flex; flex-direction: column; overflow: hidden; background: #fff; border-radius: 19px; border: 1px solid #dae9de; box-shadow: 0 28px 80px rgba(0,0,0,.3); }
.finus-tproof__head { padding: 16px 19px; display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; background: #fbfefb; border-bottom: 1px solid #e1eae4; }
.finus-tproof__head h3 { margin: 0; color: #123d20; font-size: 18px; font-weight: 800; }
.finus-tproof__head p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
.finus-tproof__close { flex: none; display: inline-flex; align-items: center; justify-content: center; width: 37px; height: 37px; padding: 0; border: 1px solid #dce7df; border-radius: 10px; background: #f5faf6; color: #385244; cursor: pointer; font-size: 24px; }
.finus-tproof__body { display: flex; align-items: center; justify-content: center; min-height: 240px; padding: 15px; background: #f3f7f4; overflow: auto; }
.finus-tproof__image { display: block; max-width: 100%; max-height: calc(92vh - 120px); object-fit: contain; border-radius: 9px; background: #fff; }
.finus-tproof__pdf { width: 100%; min-height: min(72vh, 760px); border: none; border-radius: 8px; background: #fff; }
.finus-tproof__status { color: #64748b; font-size: 13px; font-weight: 650; text-align: center; line-height: 1.7; }
.finus-tproof__error { color: #b91c1c; }
.finus-tproof [hidden] { display: none !important; }
html[data-finus-theme="dark"] .finus-tproof__dialog { background: #111a15; border-color: #293d31; }
html[data-finus-theme="dark"] .finus-tproof__head { background: #17251d; border-color: #293d31; }
html[data-finus-theme="dark"] .finus-tproof__head h3 { color: #f1f6f3; }
html[data-finus-theme="dark"] .finus-tproof__head p { color: #9eaea4; }
html[data-finus-theme="dark"] .finus-tproof__body { background: #0d1611; }
html[data-finus-theme="dark"] .finus-tproof__close { background: #18271e; color: #dce7e0; border-color: #31493a; }
@media (max-width: 767px) { .finus-tproof { padding: 9px; } .finus-tproof__dialog { max-height: 95vh; } .finus-tproof__head { padding: 12px; } .finus-tproof__pdf { min-height: 65vh; } }
</style>
<div id="finusTransactionProofModal" class="finus-tproof" aria-hidden="true">
    <div class="finus-tproof__backdrop" data-tproof-close></div>
    <div class="finus-tproof__dialog" role="dialog" aria-modal="true" aria-labelledby="finusTproofTitle">
        <div class="finus-tproof__head">
            <div>
                <h3 id="finusTproofTitle">Bukti Pembayaran</h3>
                <p>Pratinjau gambar atau PDF tanpa meninggalkan halaman.</p>
            </div>
            <button type="button" class="finus-tproof__close" data-tproof-close aria-label="Tutup pratinjau">&times;</button>
        </div>
        <div class="finus-tproof__body">
            <div id="finusTproofLoading" class="finus-tproof__status" role="status">Memuat bukti...</div>
            <img id="finusTproofImage" class="finus-tproof__image" alt="Bukti pembayaran" hidden>
            <iframe id="finusTproofPdf" class="finus-tproof__pdf" title="Dokumen bukti pembayaran PDF" hidden></iframe>
            <div id="finusTproofError" class="finus-tproof__status finus-tproof__error" role="alert" hidden></div>
        </div>
    </div>
</div>
<script>
(() => {
    const modal = document.getElementById('finusTransactionProofModal');
    if (!modal) return;
    const image = document.getElementById('finusTproofImage');
    const pdf = document.getElementById('finusTproofPdf');
    const loading = document.getElementById('finusTproofLoading');
    const error = document.getElementById('finusTproofError');
    const heading = document.getElementById('finusTproofTitle');
    const closeButton = modal.querySelector('.finus-tproof__close');
    let controller = null;
    let previewUrl = null;
    let lastTrigger = null;
    let requestSerial = 0;

    const resetMedia = () => {
        image.hidden = true;
        image.removeAttribute('src');
        pdf.hidden = true;
        pdf.removeAttribute('src');
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
    };
    const closeModal = () => {
        requestSerial++;
        controller?.abort();
        controller = null;
        resetMedia();
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('finus-tproof-open');
        lastTrigger?.focus?.();
    };
    const openModal = async trigger => {
        const url = trigger.dataset.proofUrl;
        if (!url) return;
        requestSerial++;
        const serial = requestSerial;
        controller?.abort();
        controller = new AbortController();
        resetMedia();
        lastTrigger = trigger;
        heading.textContent = trigger.dataset.proofTitle || 'Bukti Pembayaran';
        loading.hidden = false;
        error.hidden = true;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('finus-tproof-open');
        closeButton?.focus();
        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'image/png,image/jpeg,application/pdf,*/*' },
                signal: controller.signal,
            });
            if (!response.ok) throw new Error(response.status === 404
                ? 'Berkas bukti tidak ditemukan pada penyimpanan. Periksa apakah file lama masih tersedia.'
                : 'Bukti gagal dimuat (HTTP ' + response.status + '). Periksa akses dan penyimpanan R2.');
            const type = (response.headers.get('content-type') || '').split(';')[0].trim().toLowerCase();
            const isImage = ['image/jpeg','image/png','image/jpg'].includes(type);
            const isPdf = type === 'application/pdf';
            if (!isImage && !isPdf) throw new Error('Format pratinjau tidak didukung. Bukti harus JPG, PNG, atau PDF.');
            const blob = await response.blob();
            if (serial !== requestSerial) return;
            previewUrl = URL.createObjectURL(blob);
            if (isPdf) {
                pdf.src = previewUrl;
                pdf.hidden = false;
            } else {
                image.src = previewUrl;
                image.hidden = false;
            }
            loading.hidden = true;
        } catch (err) {
            if (serial !== requestSerial || err.name === 'AbortError') return;
            resetMedia();
            loading.hidden = true;
            error.textContent = err.message || 'Bukti tidak dapat ditampilkan.';
            error.hidden = false;
        }
    };
    document.addEventListener('click', event => {
        const trigger = event.target.closest('[data-transaction-proof]');
        if (trigger) { event.preventDefault(); openModal(trigger); return; }
        if (event.target.closest('[data-tproof-close]')) closeModal();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
})();
</script>
