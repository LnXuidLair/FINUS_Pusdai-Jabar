/**
 * FINUS - kapitalisasi awal kalimat untuk input naratif.
 *
 * Hanya menyentuh field yang benar-benar diedit pengguna, tanpa mengubah
 * data lama saat halaman Edit dibuka. Tidak melakukan request ke server.
 *
 * Opt-in tambahan untuk field baru: data-capitalize-sentences
 * Opt-out khusus: data-capitalize="off"
 */
(() => {
    'use strict';

    const narrativeNames = new Set([
        'alamat', 'alamat_masjid', 'address',
        'alasan', 'alasan_izin', 'alasan_sakit',
        'catatan', 'catatan_verifikasi', 'catatan_admin',
        'keterangan', 'deskripsi', 'description',
        'dasar_hukum', 'dasar_regulasi', 'dasar_aturan',
        'sumber', 'sumber_harga', 'kriteria',
        'judul', 'lokasi',
    ]);

    // Keanggotaan WeakSet tidak bertahan saat reload: data historis aman.
    const editedFields = new WeakSet();

    function isNarrativeField(field) {
        if (!(field instanceof HTMLElement)) return false;
        if (!field.matches('textarea, input[type="text"], input:not([type])')) return false;
        if (field.disabled || field.readOnly || field.isContentEditable) return false;
        if (field.closest('[data-capitalize="off"]')) return false;
        if (field.matches('[data-capitalize="off"], [data-no-capitalize]')) return false;

        // Beri kesempatan untuk memilih kolom secara eksplisit.
        if (field.hasAttribute('data-capitalize-sentences')) return true;

        const name = (field.getAttribute('name') || '')
            .toLowerCase()
            .replace(/\[[^\]]*\]/g, '')
            .split('.').pop();
        return narrativeNames.has(name);
    }

    function applyKeyboardHint(field) {
        if (isNarrativeField(field) && !field.hasAttribute('autocapitalize')) {
            field.setAttribute('autocapitalize', 'sentences');
        }
    }

    function capitalizeSentences(text) {
        if (!text) return text;
        // Tidak menurunkan huruf kapital yang sudah ada: PUSDAI, DKM, BAZNAS aman.
        // Hanya karakter pertama kalimat, setelah tanda baca + spasi atau baris baru.
        return text.replace(
            /(^\s*|[.!?][)"'”’\]]*\s+)(["'“‘(\[]*)(\p{Ll})/gmu,
            (_match, prefix, quotes, letter) => prefix + quotes + letter.toLocaleUpperCase('id-ID')
        );
    }

    function normalizeIfEdited(field) {
        if (!isNarrativeField(field) || !editedFields.has(field)) return;
        if (field.matches(':disabled') || field.readOnly) return;

        const normalized = capitalizeSentences(field.value);
        if (normalized === field.value) return;
        field.value = normalized;
        // Supaya handler validasi atau state UI yang mendengarkan input ikut tersinkron.
        field.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function initializeHints(root = document) {
        root.querySelectorAll('textarea, input[type="text"], input:not([type])')
            .forEach(applyKeyboardHint);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initializeHints(), { once: true });
    } else {
        initializeHints();
    }

    // Delegasi event juga berlaku untuk form popup/field dinamis.
    document.addEventListener('focusin', event => applyKeyboardHint(event.target), true);

    for (const type of ['input', 'change', 'compositionend']) {
        document.addEventListener(type, event => {
            if (event.isTrusted && isNarrativeField(event.target)) {
                editedFields.add(event.target);
            }
        }, true);
    }

    document.addEventListener('focusout', event => {
        normalizeIfEdited(event.target);
    }, true);

    // Cadangan jika formulir dikirim saat field masih dalam fokus.
    document.addEventListener('submit', event => {
        if (!(event.target instanceof HTMLFormElement)) return;
        for (const field of event.target.elements) normalizeIfEdited(field);
    }, true);
})();
