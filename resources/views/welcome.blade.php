<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FINUS | PUSDAI Jabar</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" href="/favicon.ico?v=20" sizes="any">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
</head>
<body class="m-0 min-h-screen overflow-x-hidden bg-white">
    <header class="relative z-40 flex h-[62px] w-full items-center justify-between bg-[linear-gradient(to_right,_#0FB442_0%,_#1AAF48_39%,_#118635_75%,_#004716_100%)] px-4 sm:px-5">
        <img src="{{ asset('assets/images/FINUS_Welcome.png') }}" alt="FINUS PUSDAI" class="h-[48px] w-auto object-contain">
        <nav class="flex items-center gap-3 font-serif font-bold text-[#6f8f79] sm:gap-3">
            <div class="relative group">
                <button class="h-6 rounded-md bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)] px-4 text-[16px] leading-6 shadow-sm transition hover:bg-white">Login</button>
                <div class="invisible absolute right-0 top-full z-50 pt-2 opacity-0 transition-all duration-200 group-hover:visible group-hover:opacity-100">
                    <div class="w-[145px] overflow-hidden rounded-lg bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)] py-1 shadow-xl">
                        <a href="#" onclick="openWithCode(event, '{{ route('login.admin') }}', 'admin')" class="block px-4 py-2 text-center hover:bg-white">Operator</a>
                        <a href="#" onclick="openWithCode(event, '{{ route('login.staff') }}', 'staff')" class="block px-4 py-2 text-center hover:bg-white">Pegawai</a>
                        <a href="{{ route('login.jamaah') }}" class="block px-4 py-2 text-center hover:bg-white">Jamaah</a>
                    </div>
                </div>
            </div>
            <div class="relative group hidden sm:block">
                <button class="h-6 rounded-md bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)] px-3 text-[16px] leading-6 shadow-sm transition hover:bg-white">About Us</button>
                <div class="invisible absolute right-0 top-full z-50 pt-2 opacity-0 transition-all duration-200 group-hover:visible group-hover:opacity-100">
                    <div class="w-[145px] overflow-hidden rounded-lg bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)] py-1 shadow-xl">
                        <a href="https://pusdai.or.id/Lahirnya_Sebuah_Gagasan" class="block px-4 py-2 text-center hover:bg-white">Sambutan</a>
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white">Visi &amp; Misi</a>
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white">Struktur</a>
                    </div>
                </div>
            </div>
            <div class="relative group">
                <button aria-label="Buka menu" class="flex h-6 w-10 items-center justify-center rounded-md bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)] text-[#171717] shadow-sm transition hover:bg-white">
                    <span class="text-[23px] leading-none">☰</span>
                </button>
                <div class="invisible absolute right-0 top-full z-50 pt-2 opacity-0 transition-all duration-200 group-hover:visible group-hover:opacity-100">
                    <div class="w-[145px] overflow-hidden rounded-lg bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)] py-1 shadow-xl">
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white sm:hidden">About Us</a>
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white">Features</a>
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white">Contacts</a>
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white">Location</a>
                        <a href="#" class="block px-4 py-2 text-center hover:bg-white">FAQ</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="relative flex min-h-[346px] items-start justify-center overflow-hidden bg-cover bg-center px-4 pt-[42px] text-center sm:pt-[43px]" style="height: calc(100vh - 62px); background-image: linear-gradient(rgba(255,255,255,.35), rgba(255,255,255,.35)), url('{{ asset('assets/images/bg-welcome.png') }}');">
        <div class="relative z-10 text-[#005621] drop-shadow-[0_1px_0_rgba(255,255,255,.4)]">
            <h1 class="font-['Lobster',cursive] text-[52px] font-bold leading-none sm:text-[62px]">Selamat Datang</h1>
            <h2 class="mt-[40px] font-serif text-[24px] font-bold leading-tight sm:text-[30px]">Website Masjid PUSDAI Jawa Barat</h2>
        </div>
    </main>
    <div id="codeModal" class="fixed inset-0 hidden bg-black/60 flex items-center justify-center z-50">
        <div id="modalBox" class="w-[350px] rounded-2xl bg-[linear-gradient(to_right,_#065F22_0%,_#11B745_25%,_#1AE559_50%,_#17AB45_75%,_#077B2B_100%)] p-8 text-center shadow-2xl">
            <h2 class="text-white text-xl font-bold mb-4">Enter Access Code</h2>
            <div class="relative">
                <input type="password" id="accessCodeInput" class="w-full rounded-xl border-2 border-white bg-[#7EFF87] p-3 text-center font-bold text-[#065F22] outline-none" placeholder="Enter code">
                <img id="togglePassword" src="{{ asset('assets/images/ShowPassword.png') }}" class="absolute right-4 top-1/2 -translate-y-1/2 h-6 w-6 cursor-pointer select-none" alt="toggle password">
            </div>
            <p id="attemptInfo" class="text-white mt-2 text-sm"></p>
            <p id="loadingInfo" class="text-white mt-2 text-sm hidden">Verifying<span id="dots">.</span></p>
            <p id="grantedMessage" class="hidden opacity-0 text-green-300 font-bold text-lg mt-3 transition-all duration-500"><span class="inline-block mr-2">✔</span> Access Granted!</p>
            <div class="mt-6 flex gap-3">
                <button id="verifyBtn" onclick="checkCode()" class="flex w-1/2 items-center justify-center gap-2 rounded-xl bg-[#7EFF87] py-2 font-bold text-[#065F22] transition hover:bg-[#5EEA69]">
                    <span id="verifyText">Verify</span>
                    <span id="verifySpinner" class="hidden animate-spin h-5 w-5 border-2 border-[#1A237E] border-t-transparent rounded-full"></span>
                </button>
                <button onclick="closeModal()" class="w-1/2 bg-red-500 text-white rounded-xl py-2 hover:bg-red-600 transition">Cancel</button>
            </div>
        </div>
    </div>
    <script>
    function openWithCode(event, url, type) {
        event.preventDefault();
        window.currentType = type;
        document.getElementById("accessCodeInput").value = ""
        document.getElementById("attemptInfo").innerText = ""
        document.getElementById("codeModal").classList.remove("hidden");
    }
    function closeModal(){
        document.getElementById("codeModal").classList.add("hidden");
    }
    async function checkCode(){
        const verifyBtn = document.getElementById("verifyBtn");
        if (verifyBtn.disabled) return;
        const code = document.getElementById("accessCodeInput").value.trim();
        const attemptInfo = document.getElementById("attemptInfo");
        const input = document.getElementById("accessCodeInput");
        const verifyText = document.getElementById("verifyText");
        const spinner = document.getElementById("verifySpinner");
        const granted = document.getElementById("grantedMessage");
        // reset pesan sebelumnya
        attemptInfo.innerText = "";
        if (!code) {
            attemptInfo.innerText = "Access code wajib diisi.";
            input.focus();
            return;
        }
        if (granted) {
            // sembunyikan granted message kalau sempat muncul sebelumnya
            granted.classList.add("hidden");
            granted.classList.add("opacity-0");
        }
        input.disabled = true;
        verifyBtn.disabled = true;
        verifyText.innerText = "Verifying...";
        spinner.classList.remove("hidden");
        // Batasi request agar browser tidak menunggu terlalu lama.
        // Ini juga mencegah pengguna menekan tombol verifikasi berulang kali.
        const controller = new AbortController();
        const requestTimeout = setTimeout(() => controller.abort(), 12000);

        try {
            const res = await fetch("{{ route('verify.code') }}", {
                method: "POST",
                credentials: "same-origin",
                signal: controller.signal,
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    type: window.currentType,
                    code: code
                })
            });

            clearTimeout(requestTimeout);

            const contentType = res.headers.get('content-type') || '';
            let data = null;
            if (contentType.includes('application/json')) {
                data = await res.json();
            } else {
                const text = await res.text().catch(()=>"");
                console.error("Non-JSON response:", res.status, text);
                attemptInfo.innerText = `Server returned non-JSON response (${res.status}). Refresh and try again.`;
                return;
            }

            // kalau server return 4xx/5xx, beri pesan juga
            if (!res.ok) {
                if (res.status === 419) {
                    attemptInfo.innerText = "Session expired (419). Refresh halaman lalu coba lagi.";
                    return;
                }
                if (res.status === 422 && data?.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    const firstMsg = firstKey ? (data.errors[firstKey]?.[0] || "") : "";
                    attemptInfo.innerText = firstMsg || data.message || `Validasi gagal (${res.status}).`;
                    return;
                }
                attemptInfo.innerText = data?.message || `Server error (${res.status}). Try again.`;
                return;
            }

            spinner.classList.add("hidden");
            if (data.status === "success") {
                // show granted (tidak mengganggu layout karena awalnya .hidden .opacity-0)
                if (granted) {
                    granted.classList.remove("hidden");
                    // biar fade in terlihat
                    setTimeout(()=> granted.classList.remove("opacity-0"), 10);
                }
                verifyText.innerText = "Redirecting...";
                spinner.classList.remove("hidden");

                setTimeout(()=> {
                    window.location.href = data.redirect;
                }, 1100);
                return;
            }
            if (data.status === "misconfigured") {
                attemptInfo.innerText = data.message || "Access code belum dikonfigurasi.";
                verifyText.innerText = "Verify";
                input.disabled = false;
                verifyBtn.disabled = false;
                input.focus();
                return;
            }
            if (data.status === "already_logged_in") {
                const currentRole = data.current_role;
                const requestedType = data.requested_type || window.currentType;
                const desiredRole = requestedType === 'admin' ? 'admin' : 'pegawai';

                // Kalau role sama, langsung arahkan ke dashboard
                if (data.redirect && currentRole && currentRole === desiredRole) {
                    attemptInfo.innerText = "Kamu sudah login. Mengarahkan ke dashboard...";
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 300);
                    return;
                }

                // Kalau role beda (mis: masih login sebagai orangtua tapi mau login operator), minta logout dulu
                const label = requestedType === 'admin' ? 'Operator' : 'Pegawai';
                attemptInfo.innerText = `Kamu masih login sebagai ${currentRole || 'user'}.`;

                const confirmLogout = confirm(`Kamu masih login sebagai ${currentRole || 'user'}. Logout dulu untuk login ${label}?`);
                if (confirmLogout && data.logout_url) {
                    try {
                        await fetch(data.logout_url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                        });
                    } catch (e) {
                        console.error('Logout failed', e);
                    }
                    // Setelah logout, reload agar session bersih (user bisa klik Login | App lagi)
                    window.location.reload();
                    return;
                }

                // Kalau user tidak mau logout, arahkan saja ke dashboard yg sedang aktif (jika ada)
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 300);
                    return;
                }

                verifyText.innerText = "Verify";
                input.disabled = false;
                verifyBtn.disabled = false;
                input.focus();
                return;
            }
            if (data.status === "error") {
                attemptInfo.innerText = `Wrong code (${data.attempts}/3)`;

                verifyText.innerText = "Verify";
                input.disabled = false;
                verifyBtn.disabled = false;
                input.value = "";
                input.focus();
                return;
            }
            if (data.status === "locked" || data.status === "cooldown") {
                verifyText.innerText = "Verify";
                input.disabled = true;
                startTimer(data.remaining);
                verifyBtn.disabled = false;
                return;
            }
            attemptInfo.innerText = "Unexpected server response.";
            verifyText.innerText = "Verify";
            input.disabled = false;
            verifyBtn.disabled = false;
        } catch (err) {
            clearTimeout(requestTimeout);
            console.error("checkCode error:", err);

            if (err.name === "AbortError") {
                attemptInfo.innerText = "Proses terlalu lama. Silakan coba lagi.";
            } else {
                attemptInfo.innerText = "Network error. Check your connection and try again.";
            }

            verifyText.innerText = "Verify";
            input.disabled = false;
            verifyBtn.disabled = false;
            spinner.classList.add("hidden");
        }
    }
    function startTimer(seconds){
        const attemptInfo = document.getElementById("attemptInfo")
        const input = document.getElementById("accessCodeInput")
        const interval = setInterval(()=>{
            attemptInfo.innerText = `Too many attempts! Try again in ${seconds}s`
            seconds--
            if(seconds < 0){
                clearInterval(interval);
                input.disabled = false
                attemptInfo.innerText = "You can try again ✅"
            }
        },1000)
    }
    document.getElementById("accessCodeInput")?.addEventListener("keypress", e=>{
        if(e.key==="Enter") checkCode()
    })
    // Toggle show/hide password
    // Toggle show/hide password using img src
    const pwInput = document.getElementById("accessCodeInput");
    const togglePw = document.getElementById("togglePassword");

    togglePw.addEventListener("click", () => {
        if (pwInput.type === "password") {
            pwInput.type = "text";
            togglePw.src = "{{ asset('assets/images/HidePassword.png') }}"; // mata dicoret
        } else {
            pwInput.type = "password";
            togglePw.src = "{{ asset('assets/images/ShowPassword.png') }}"; // mata biasa
        }
    });
    </script>
</body>
</html>