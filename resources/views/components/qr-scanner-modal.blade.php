<div id="qr-scanner-modal"
     onclick="if (event.target === this) window.closeQrScannerModal()"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-xs p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="qr-scanner-modal-title">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-line flex flex-col">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-line bg-cream-50">
            <div class="flex items-center gap-2.5">
                <div class="size-8 rounded-xl bg-brand-900 text-cream flex items-center justify-center shrink-0">
                    <x-lucide.qr-code class="size-4" />
                </div>
                <div>
                    <h3 id="qr-scanner-modal-title" class="text-sm font-bold text-ink leading-tight">Scan QR Meja</h3>
                    <p class="text-[11px] text-muted leading-tight">Arahkan kamera ke QR Code yang ada di meja</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="button"
                        id="qr-switch-camera-btn"
                        onclick="window.switchQrScannerCamera()"
                        class="h-8 px-2.5 rounded-lg border border-line bg-white hover:bg-brand-100 text-[11px] font-semibold text-ink transition cursor-pointer"
                        title="Ganti Kamera Depan / Belakang">
                    Ganti Kamera
                </button>
                <button type="button"
                        onclick="window.closeQrScannerModal()"
                        class="size-8 rounded-full bg-white border border-line text-ink hover:bg-line/40 flex items-center justify-center transition cursor-pointer"
                        aria-label="Tutup Pemindai QR">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Camera Viewport --}}
        <div class="p-4 space-y-3">
            <div class="relative aspect-square w-full max-h-[320px] rounded-2xl overflow-hidden bg-[#0e2a1f] border border-line flex items-center justify-center">
                <video id="qr-scanner-video"
                       autoplay
                       playsinline
                       muted
                       class="w-full h-full object-cover"></video>
                <canvas id="qr-scanner-canvas" class="hidden"></canvas>

                {{-- Viewfinder Guides --}}
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-8">
                    <div class="relative w-full h-full max-w-[210px] max-h-[210px]">
                        <span class="absolute top-0 left-0 size-7 border-t-4 border-l-4 border-cream rounded-tl-xl"></span>
                        <span class="absolute top-0 right-0 size-7 border-t-4 border-r-4 border-cream rounded-tr-xl"></span>
                        <span class="absolute bottom-0 left-0 size-7 border-b-4 border-l-4 border-cream rounded-bl-xl"></span>
                        <span class="absolute bottom-0 right-0 size-7 border-b-4 border-r-4 border-cream rounded-br-xl"></span>
                        <div class="absolute inset-x-3 top-1/2 h-0.5 bg-emerald-400/80 shadow-[0_0_12px_rgba(52,211,153,0.9)] animate-pulse"></div>
                    </div>
                </div>

                {{-- Camera Loading / Blocked Overlay --}}
                <div id="qr-scanner-placeholder"
                     class="absolute inset-0 bg-[#0e2a1f]/90 text-white flex flex-col items-center justify-center p-6 text-center gap-2">
                    <x-lucide.qr-code class="size-10 text-cream/85" />
                    <p id="qr-scanner-placeholder-text" class="text-xs text-[#D7E5DC] leading-relaxed">
                        Menyiapkan akses kamera perangkat...
                    </p>
                    <button type="button"
                            id="qr-retry-camera-btn"
                            onclick="window.startQrScannerCamera()"
                            class="hidden mt-1 px-3.5 py-1.5 rounded-xl bg-cream text-brand-900 text-xs font-bold hover:bg-[#efe6ca] transition cursor-pointer">
                        Coba Buka Kamera Lagi
                    </button>
                </div>
            </div>

            {{-- Live Status Banner --}}
            <div id="qr-scanner-status"
                 class="rounded-xl bg-cream-50 border border-line px-3.5 py-2.5 text-xs font-medium text-muted text-center">
                Izinkan akses kamera untuk memindai QR meja secara langsung.
            </div>

            {{-- Fallback Photo Upload (Opens Native Camera App on Mobile) & Manual Input --}}
            <div class="pt-1 border-t border-line/70 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-medium text-muted">Atau unggah / ambil foto QR:</span>
                    <input id="qr-scanner-file-input"
                           type="file"
                           accept="image/*"
                           capture="environment"
                           class="hidden"
                           onchange="window.handleQrFileSelected(event)">
                    <button type="button"
                            onclick="document.getElementById('qr-scanner-file-input').click()"
                            class="px-3 py-1.5 rounded-xl bg-brand-100 hover:bg-brand-900 hover:text-white text-brand-900 text-[11px] font-semibold transition cursor-pointer">
                        Ambil Foto / Pilih QR
                    </button>
                </div>

                <form onsubmit="window.handleManualQrSubmit(event)" class="flex items-center gap-2">
                    <input type="text"
                           id="qr-manual-input"
                           placeholder="Atau ketik nomor meja (mis. 05) / kode QR..."
                           class="flex-1 h-9 rounded-xl border border-line bg-cream-50 px-3 text-xs text-ink placeholder:text-[#9AA59E] focus:border-brand-900 focus:outline-hidden">
                    <button type="submit"
                            class="h-9 px-3.5 rounded-xl bg-brand-900 hover:bg-brand-700 text-white text-xs font-semibold transition cursor-pointer shrink-0">
                        Pakai
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@once
<script>
    (() => {
        const RESOLVE_URL = @json(route('customer.qr.resolve'));
        let activeStream = null;
        let scanFrameId = null;
        let currentFacingMode = 'environment';
        let isResolvingScan = false;
        let barcodeDetector = null;
        let jsQrLoader = null;

        function setStatus(message, tone = 'neutral') {
            const box = document.getElementById('qr-scanner-status');
            if (!box) return;
            box.textContent = message;
            box.className = 'rounded-xl px-3.5 py-2.5 text-xs font-medium text-center border';
            if (tone === 'success') {
                box.classList.add('bg-ok-bg', 'text-brand-900', 'border-brand-900/20');
            } else if (tone === 'error') {
                box.classList.add('bg-danger-bg', 'text-danger', 'border-danger/20');
            } else {
                box.classList.add('bg-cream-50', 'text-muted', 'border-line');
            }
        }

        function setPlaceholder(visible, text = '', showRetry = false) {
            const overlay = document.getElementById('qr-scanner-placeholder');
            const label = document.getElementById('qr-scanner-placeholder-text');
            const retry = document.getElementById('qr-retry-camera-btn');
            if (!overlay) return;
            if (visible) {
                overlay.classList.remove('hidden');
                if (label && text) label.textContent = text;
                if (retry) retry.classList.toggle('hidden', !showRetry);
            } else {
                overlay.classList.add('hidden');
            }
        }

        function stopCamera() {
            if (scanFrameId !== null) {
                cancelAnimationFrame(scanFrameId);
                scanFrameId = null;
            }
            if (activeStream) {
                activeStream.getTracks().forEach((track) => track.stop());
                activeStream = null;
            }
            const video = document.getElementById('qr-scanner-video');
            if (video) {
                video.srcObject = null;
            }
        }

        function loadJsQr() {
            if (window.jsQR) {
                return Promise.resolve(window.jsQR);
            }
            if (!jsQrLoader) {
                jsQrLoader = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
                    script.async = true;
                    script.onload = () => resolve(window.jsQR);
                    script.onerror = () => reject(new Error('Gagal memuat pembaca QR.'));
                    document.head.appendChild(script);
                });
            }
            return jsQrLoader;
        }

        async function resolveQrPayload(rawValue) {
            if (!rawValue || isResolvingScan) return;
            isResolvingScan = true;
            setStatus('Memverifikasi QR Meja...', 'neutral');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch(RESOLVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ qr_data: String(rawValue).trim() }),
                });

                const data = await res.json();
                if (!res.ok || !data.success || !data.redirect_url) {
                    setStatus(data.message || 'QR Code tidak valid.', 'error');
                    setTimeout(() => {
                        isResolvingScan = false;
                    }, 1500);
                    return;
                }

                setStatus(data.message || 'Meja terdeteksi! Mengalihkan ke menu...', 'success');
                stopCamera();
                window.location.href = data.redirect_url;
            } catch (err) {
                setStatus('Gagal menghubungi server. Periksa koneksi Anda.', 'error');
                setTimeout(() => {
                    isResolvingScan = false;
                }, 1500);
            }
        }

        async function detectFromSource(source, width, height) {
            if ('BarcodeDetector' in window) {
                try {
                    if (!barcodeDetector) {
                        barcodeDetector = new window.BarcodeDetector({ formats: ['qr_code'] });
                    }
                    const codes = await barcodeDetector.detect(source);
                    if (codes && codes.length > 0 && codes[0].rawValue) {
                        return codes[0].rawValue;
                    }
                } catch (_) {
                    // Fallback to jsQR below
                }
            }

            if (window.jsQR && width > 0 && height > 0) {
                const canvas = document.getElementById('qr-scanner-canvas');
                if (!canvas) return null;
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                if (!ctx) return null;
                ctx.drawImage(source, 0, 0, width, height);
                const imgData = ctx.getImageData(0, 0, width, height);
                const decoded = window.jsQR(imgData.data, imgData.width, imgData.height, { inversionAttempts: 'dontInvert' });
                if (decoded && decoded.data) {
                    return decoded.data;
                }
            }

            return null;
        }

        function scanLoop() {
            const modal = document.getElementById('qr-scanner-modal');
            const video = document.getElementById('qr-scanner-video');
            if (!modal || modal.classList.contains('hidden') || !video) {
                return;
            }

            if (video.readyState >= 2 && video.videoWidth > 0 && !isResolvingScan) {
                detectFromSource(video, video.videoWidth, video.videoHeight)
                    .then((value) => {
                        if (value) {
                            resolveQrPayload(value);
                        }
                    })
                    .catch(() => {});
            }

            scanFrameId = requestAnimationFrame(scanLoop);
        }

        window.startQrScannerCamera = async function () {
            stopCamera();
            isResolvingScan = false;

            if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                setPlaceholder(
                    true,
                    'Kamera langsung membutuhkan koneksi HTTPS. Gunakan tombol "Ambil Foto / Pilih QR" atau ketik nomor meja di bawah.',
                    false
                );
                setStatus('Akses kamera langsung butuh HTTPS. Gunakan Ambil Foto QR atau input meja.', 'error');
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                setPlaceholder(
                    true,
                    'Browser ini belum mendukung kamera langsung. Gunakan tombol "Ambil Foto / Pilih QR" di bawah.',
                    false
                );
                setStatus('Kamera langsung tidak tersedia pada browser ini.', 'error');
                return;
            }

            setPlaceholder(true, 'Membuka kamera perangkat...', false);
            setStatus('Menunggu izin kamera...', 'neutral');

            try {
                try {
                    activeStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: currentFacingMode } },
                        audio: false,
                    });
                } catch (_) {
                    activeStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                }

                const video = document.getElementById('qr-scanner-video');
                if (!video) return;
                video.srcObject = activeStream;
                await video.play();
                setPlaceholder(false);
                setStatus('Kamera aktif — arahkan ke QR Code meja.', 'neutral');

                if (!('BarcodeDetector' in window)) {
                    loadJsQr().catch(() => {});
                }

                scanLoop();
            } catch (err) {
                const denied = err && (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError');
                setPlaceholder(
                    true,
                    denied
                        ? 'Izin kamera ditolak. Aktifkan izin kamera pada browser lalu coba lagi, atau gunakan foto QR di bawah.'
                        : 'Kamera tidak dapat diakses saat ini. Anda tetap bisa foto/pilih gambar QR atau ketik nomor meja di bawah.',
                    true
                );
                setStatus(denied ? 'Izin kamera ditolak oleh browser.' : 'Gagal membuka kamera perangkat.', 'error');
            }
        };

        window.switchQrScannerCamera = function () {
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            window.startQrScannerCamera();
        };

        window.openQrScannerModal = function () {
            const modal = document.getElementById('qr-scanner-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            window.startQrScannerCamera();
        };

        window.closeQrScannerModal = function () {
            stopCamera();
            const modal = document.getElementById('qr-scanner-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        window.handleQrFileSelected = async function (event) {
            const file = event.target?.files?.[0];
            if (!file) return;

            setStatus('Membaca QR dari foto...', 'neutral');
            if (!('BarcodeDetector' in window)) {
                try {
                    await loadJsQr();
                } catch (_) {}
            }

            const img = new Image();
            const objectUrl = URL.createObjectURL(file);
            img.onload = async () => {
                try {
                    const value = await detectFromSource(img, img.naturalWidth, img.naturalHeight);
                    if (value) {
                        await resolveQrPayload(value);
                    } else {
                        setStatus('QR Code tidak terbaca pada foto ini. Pastikan foto jelas dan tidak buram.', 'error');
                    }
                } finally {
                    URL.revokeObjectURL(objectUrl);
                    event.target.value = '';
                }
            };
            img.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                setStatus('File gambar tidak dapat dibaca.', 'error');
            };
            img.src = objectUrl;
        };

        window.handleManualQrSubmit = function (event) {
            event.preventDefault();
            const input = document.getElementById('qr-manual-input');
            const val = input?.value?.trim();
            if (!val) {
                setStatus('Masukkan nomor meja atau kode QR terlebih dahulu.', 'error');
                return;
            }
            resolveQrPayload(val);
        };

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                window.closeQrScannerModal();
            }
        });
    })();
</script>
@endonce
