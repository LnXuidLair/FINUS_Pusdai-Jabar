// FINUS: realtime unread badge + optional Web Push per authenticated account.
import * as Beams from '@pusher/push-notifications-web';

const root = document.getElementById('finus-notification-config');
if (root) {
    const config = root.dataset;
    const userId = Number(config.userId);
    const portal = config.portal;
    const accountId = `finus-${portal}-${userId}`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const list = document.querySelector('[data-finus-notification-items]');
    let feedRequestId = 0;
    let currentCounts = null;

    function applyCounts(counts) {
        if (!counts) return;
        currentCounts = counts;
        document.querySelectorAll('[data-finus-badge]').forEach((badge) => {
            const count = Math.max(0, Number(counts[badge.dataset.finusBadge] || 0));
            badge.hidden = count === 0;
            badge.textContent = count > 99 ? '99+' : String(count);
        });
    }

    function validSameOriginUrl(value) {
        if (!value) return null;
        try {
            const url = new URL(value, window.location.origin);
            return url.origin === window.location.origin && ['http:', 'https:'].includes(url.protocol) ? url.href : null;
        } catch (_) { return null; }
    }

    function renderLatest(items) {
        if (!list) return;
        list.replaceChildren();
        if (!Array.isArray(items) || !items.length) {
            const empty = document.createElement('p');
            empty.className = 'finus-bell-empty';
            empty.textContent = 'Belum ada notifikasi.';
            list.append(empty);
            return;
        }
        items.forEach((item) => {
            const entry = document.createElement('a');
            entry.className = 'finus-bell-item' + (!item.read_at ? ' is-unread' : '');
            entry.href = validSameOriginUrl(item.url) || document.querySelector('.finus-bell-menu-footer')?.href || '#';
            const title = document.createElement('strong');
            title.textContent = item.title || 'Notifikasi FINUS';
            const message = document.createElement('small');
            message.textContent = item.message || '';
            entry.append(title, message);
            list.append(entry);
        });
    }

    async function refreshFeed() {
        const requestId = ++feedRequestId;
        try {
            const response = await fetch(config.feed, {
                credentials: 'same-origin',
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            });
            if (response.status === 401 || response.status === 403) return;
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            if (requestId !== feedRequestId) return;
            applyCounts(data.counts);
            renderLatest(data.items);
        } catch (error) {
            if (requestId !== feedRequestId) return;
            console.warn('FINUS: belum dapat memuat notifikasi', error);
            if (list) list.textContent = 'Notifikasi belum dapat dimuat.';
        }
    }


    // Pusher Channels accelerates updates; polling is a resilience fallback.
    if (config.pusherKey && config.pusherCluster && window.Pusher) {
        try {
            const channelClient = new window.Pusher(config.pusherKey, {
                cluster: config.pusherCluster,
                forceTLS: true,
                channelAuthorization: {
                    endpoint: config.channelAuth,
                    transport: 'ajax',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest'},
                },
            });
            channelClient.subscribe(`private-finus.user.${userId}`)
                .bind('finus.notification', () => refreshFeed());
        } catch (error) { console.warn('FINUS: Channels tidak terhubung, polling tetap aktif', error); }
    }
    refreshFeed();
    window.setInterval(() => { if (document.visibilityState === 'visible') refreshFeed(); }, 20000);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') refreshFeed();
    });
    document.querySelector('[data-toggle="dropdown"].finus-topbar-bell')
        ?.addEventListener('click', () => { refreshFeed(); refreshPushState(); });

    // Device status comes from Beams registration, not ephemeral button text.
    const pushToggles = [...document.querySelectorAll('[data-finus-push-toggle]')];
    const pushLabels = [...document.querySelectorAll('[data-finus-push-label]')];
    const status = document.querySelector('[data-finus-push-status]');
    let client = null;
    let busy = false;
    const deviceSupported = () => Boolean(config.beamsInstance && window.isSecureContext &&
        'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window);
    const getClient = () => client ||= new Beams.Client({ instanceId: config.beamsInstance });

    function showState(state, message) {
        if (status) status.textContent = message;
        const stateNames = {
            on: 'Aktif', off: 'Nonaktif', other: 'Akun lain',
            busy: 'Memproses…', unsupported: 'Tak tersedia', denied: 'Diblokir',
        };
        pushLabels.forEach((label) => { label.textContent = stateNames[state] || 'Nonaktif'; });
        pushToggles.forEach((toggle) => {
            // Switch menampilkan status nyata; jangan optimistis saat Beams masih memproses.
            if (state !== 'busy') {
                toggle.setAttribute('aria-checked', String(state === 'on'));
            }
            const actionLabel = state === 'on' ? 'Nonaktifkan' : 'Aktifkan';
            const label = `${actionLabel} notifikasi perangkat untuk akun ini pada browser ini`;
            toggle.setAttribute('aria-label', label);
            toggle.title = message;
            toggle.disabled = busy || state === 'busy' || state === 'unsupported' || state === 'denied';
            toggle.setAttribute('aria-busy', String(busy || state === 'busy'));
        });
    }

    async function refreshPushState() {
        if (!status && !pushToggles.length) return;
        if (!deviceSupported()) {
            showState('unsupported', 'Notifikasi perangkat tidak didukung di browser atau koneksi ini.');
            return;
        }
        if (Notification.permission === 'denied') {
            showState('denied', 'Izin notifikasi diblokir. Ubah ke Izinkan melalui pengaturan situs browser.');
            return;
        }
        if (busy) return;
        try {
            // getUserId() is read-only and does not request notification permission.
            const current = await getClient().getUserId();
            if (current === accountId) {
                showState('on', 'Aktif untuk akun ini pada browser ini.');
            } else if (current) {
                showState('other', 'Browser ini terhubung ke akun FINUS lain. Nyalakan switch untuk memindahkannya.');
            } else {
                showState('off', 'Belum aktif pada browser ini.');
            }
        } catch (error) {
            console.warn('FINUS: status Beams gagal diperiksa', error);
            showState('off', 'Belum bisa memeriksa status perangkat. Coba nyalakan switch.');
        }
    }

    async function handlePushToggle(event) {
        // Bootstrap dropdown tidak boleh menutup saat switch ditekan.
        event.stopPropagation();
        if (busy || !deviceSupported() || Notification.permission === 'denied') return;
        busy = true;
        showState('busy', 'Memperbarui pengaturan notifikasi perangkat…');
        try {
            const beams = getClient();
            const current = await beams.getUserId();
            if (current === accountId) {
                await beams.stop();
            } else {
                if (current && !window.confirm('Perangkat ini tertaut ke akun FINUS lain. Alihkan notifikasi ke akun ini?')) return;
                if (current) await beams.stop();
                await beams.start(); // User gesture; browser may request permission here.
                if ((await beams.getUserId()) !== accountId) {
                    await beams.setUserId(accountId, new Beams.TokenProvider({
                        url: config.beamsAuth,
                        credentials: 'same-origin',
                    }));
                }
                try { await beams.removeDeviceInterest('hello'); } catch (_) { /* test-only interest */ }
            }
        } catch (error) {
            console.error('FINUS: pengaturan notifikasi perangkat gagal', error);
            if (status) status.textContent = 'Gagal memperbarui notifikasi. Periksa izin browser dan koneksi.';
        } finally {
            busy = false;
            await refreshPushState();
        }
    }

    pushToggles.forEach((toggle) => toggle.addEventListener('click', handlePushToggle));

    if (status || pushToggles.length) {
        refreshPushState();
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') refreshPushState();
        });
    }

    // Best-effort detach on explicit logout without removing another guard's identity.
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !/\/logout(?:\/|$)/.test(new URL(form.action).pathname)) return;
        if (!deviceSupported() || Notification.permission !== 'granted') return;
        event.preventDefault();
        const proceed = () => HTMLFormElement.prototype.submit.call(form);
        Promise.race([
            getClient().getUserId().then(async (current) => {
                if (current === accountId) await getClient().stop();
            }),
            new Promise((resolve) => window.setTimeout(resolve, 1800)),
        ]).then(proceed, proceed);
    }, true);
}