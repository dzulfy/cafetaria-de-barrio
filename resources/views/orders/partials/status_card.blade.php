@php
    $inMockup = $inMockup ?? false;
    $cleanTable = trim((string) preg_replace('/^Meja\s+/i', '', (string) ($order->table_number ?? '05')));
    $cleanTable = $cleanTable !== '' ? $cleanTable : '05';
    $orderTotal = $order->total_price ?: $order->total_amount ?: 0;
    $isPaid = $order->payment_status === 'paid';
    $midtransClientKey = $midtransClientKey ?? config('services.midtrans.client_key');
    $midtransSnapJsUrl = $midtransSnapJsUrl ?? app(\App\Services\MidtransService::class)->snapJsUrl();

    $headline = match (true) {
        $order->status === 'dibatalkan' => 'Pesanan dibatalkan',
        ! $isPaid => 'Menunggu Pembayaran',
        $order->status === 'diproses' => 'Pesanan sedang diproses',
        $order->status === 'selesai' => 'Pesanan sudah siap!',
        default => 'Pembayaran Berhasil!',
    };

    $paymentLabel = match ($order->payment_status) {
        'paid' => 'Lunas',
        'pending' => 'Menunggu Konfirmasi',
        'expired' => 'Kedaluwarsa',
        'failed' => 'Gagal',
        default => 'Belum Dibayar',
    };

    $paymentBadgeClass = match ($order->payment_status) {
        'paid' => 'bg-ok-bg text-brand-900',
        'pending' => 'bg-blue-50 text-blue-700',
        'expired', 'failed' => 'bg-danger-bg text-danger',
        default => 'bg-warn-bg text-warn-fg',
    };
@endphp

<div class="{{ $inMockup ? 'px-5 pt-8 pb-6' : 'max-w-3xl mx-auto w-full px-5 md:px-8 pt-8 md:pt-12 pb-8' }} flex flex-col flex-1 min-h-screen bg-cream-50" id="status-container">
    {{-- Icon Status Besar & Judul --}}
    <div class="text-center mb-6 animate-fade-up">
        <div class="w-20 h-20 rounded-full {{ $order->status === 'dibatalkan' ? 'bg-danger' : ($isPaid ? 'bg-brand-900' : 'bg-amber-500') }} flex items-center justify-center text-white mx-auto shadow-md animate-pop">
            @if ($order->status === 'dibatalkan')
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            @elseif (! $isPaid)
                <x-lucide.wallet class="w-10 h-10" />
            @else
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M5 13l4 4L19 7" />
                </svg>
            @endif
        </div>
        <h1 class="text-[22px] sm:text-2xl font-bold text-ink tracking-tight mt-4">
            {{ $headline }}
        </h1>
        <p class="text-xs sm:text-sm text-muted font-medium mt-1.5">
            Kode pesanan {{ $order->order_code }} <span class="mx-1">|</span> Meja {{ $cleanTable }}
        </p>

        @if (! $isPaid && $order->status !== 'dibatalkan')
            <p class="text-xs text-muted mt-1.5 max-w-md mx-auto">
                Pesanan terkirim! Selesaikan pembayaran via Midtrans agar kasir dapat memproses pesanan.
            </p>
        @endif

        @if (! in_array($order->status, ['selesai', 'dibatalkan'], true))
            <div class="mt-2.5 inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-[11px] font-semibold text-brand-900">
                <span class="size-2 rounded-full bg-brand-900 animate-pulse"></span>
                <span>Status diperbarui otomatis</span>
            </div>
        @endif
    </div>

    <div class="space-y-4 flex-1">
        {{-- Kartu Pembayaran Midtrans --}}
        @if ($order->status !== 'dibatalkan')
            @if (! $isPaid)
                <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-brand-900/20 shadow-sm space-y-4 animate-fade-up">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-ink">Pembayaran Midtrans</h2>
                            <p class="text-xs text-muted mt-0.5">Pilih QRIS, GoPay, Virtual Account, atau metode lainnya.</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $paymentBadgeClass }}">
                            {{ $paymentLabel }}
                        </span>
                    </div>

                    <div id="midtrans-feedback" class="hidden rounded-xl px-3.5 py-2.5 text-xs font-medium"></div>

                    @if (! $inMockup)
                        <div class="flex flex-col sm:flex-row gap-2.5">
                            <button type="button"
                                    id="btn-midtrans-pay"
                                    onclick="openMidtransSnap()"
                                    class="flex-1 h-12 rounded-xl bg-brand-900 hover:bg-brand-700 text-white font-bold text-sm shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                                <x-lucide.wallet class="w-4 h-4 text-cream" />
                                <span>Bayar Sekarang via Midtrans</span>
                            </button>

                            <button type="button"
                                    id="btn-midtrans-verify"
                                    onclick="verifyMidtransPayment(false)"
                                    class="h-12 px-4 rounded-xl bg-cream hover:bg-[#efe6ca] border border-line text-brand-900 font-semibold text-xs sm:text-sm transition-all active:scale-[0.98] flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Cek Status Pembayaran</span>
                            </button>
                        </div>

                        @if ($order->snap_redirect_url)
                            <div class="text-center sm:text-left">
                                <a href="{{ $order->snap_redirect_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-[11px] font-semibold text-brand-900 hover:underline">
                                    Atau buka halaman pembayaran Midtrans di tab baru &rarr;
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="bg-ok-bg border border-brand-900/15 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-fade-up">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-0.5 text-[11px] font-bold text-brand-900">
                            <span>Pembayaran Lunas (Midtrans)</span>
                        </div>
                        <p class="text-xs sm:text-[13px] font-medium text-brand-900 leading-relaxed">
                            Pembayaran berhasil! Pesanan diteruskan ke kasir untuk segera diproses.
                        </p>
                    </div>
                    <div class="text-left sm:text-right text-[11px] text-brand-900/80 shrink-0 space-y-0.5">
                        @if ($order->payment_method)
                            <div>Metode: <strong class="uppercase text-brand-900">{{ $order->payment_method }}</strong></div>
                        @endif
                        @if ($order->paid_at)
                            <div>Waktu: <strong class="text-brand-900">{{ $order->paid_at->format('d M Y, H.i') }}</strong></div>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        <div class="{{ $inMockup ? 'space-y-4' : 'grid grid-cols-1 md:grid-cols-2 gap-4 items-stretch' }}">
            {{-- Card 1: Status Pesanan --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-line shadow-2xs flex flex-col justify-between animate-fade-up">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[13px] sm:text-sm font-bold text-ink">Status pesanan</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                            {{ $order->status === 'menunggu' ? 'bg-warn-bg text-warn-fg' : '' }}
                            {{ $order->status === 'diproses' ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $order->status === 'selesai' ? 'bg-ok-bg text-brand-900' : '' }}
                            {{ $order->status === 'dibatalkan' ? 'bg-danger-bg text-danger' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    {{-- Stepper Timeline --}}
                    <div class="flex items-center justify-between px-3 relative my-4">
                        {{-- Step 1: Menunggu --}}
                        <div class="flex flex-col items-center relative z-10">
                            <div class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center text-xs shadow-2xs {{ $order->status === 'menunggu' ? 'animate-pulse-ring' : '' }}">
                                <x-lucide.check class="w-4 h-4" />
                            </div>
                            <span class="text-[11px] font-semibold text-brand-900 mt-1.5 text-center">Menunggu</span>
                        </div>

                        <div class="h-0.5 flex-1 mx-2 -mt-5 {{ in_array($order->status, ['diproses', 'selesai'], true) ? 'bg-brand-900' : 'bg-line' }} transition-colors duration-300"></div>

                        {{-- Step 2: Diproses --}}
                        <div class="flex flex-col items-center relative z-10">
                            @if (in_array($order->status, ['diproses', 'selesai'], true))
                                <div class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center text-xs shadow-2xs {{ $order->status === 'diproses' ? 'animate-pulse-ring' : '' }}">
                                    <x-lucide.check class="w-4 h-4" />
                                </div>
                                <span class="text-[11px] font-semibold text-brand-900 mt-1.5 text-center">Diproses</span>
                            @else
                                <div class="w-7 h-7 rounded-full border-2 border-line bg-white"></div>
                                <span class="text-[11px] text-muted font-medium mt-1.5 text-center">Diproses</span>
                            @endif
                        </div>

                        <div class="h-0.5 flex-1 mx-2 -mt-5 {{ $order->status === 'selesai' ? 'bg-brand-900' : 'bg-line' }} transition-colors duration-300"></div>

                        {{-- Step 3: Selesai --}}
                        <div class="flex flex-col items-center relative z-10">
                            @if ($order->status === 'selesai')
                                <div class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center text-xs shadow-2xs">
                                    <x-lucide.check class="w-4 h-4" />
                                </div>
                                <span class="text-[11px] font-semibold text-brand-900 mt-1.5 text-center">Selesai</span>
                            @else
                                <div class="w-7 h-7 rounded-full border-2 border-line bg-white"></div>
                                <span class="text-[11px] text-muted font-medium mt-1.5 text-center">Selesai</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Ringkasan --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-line shadow-2xs flex flex-col justify-between animate-fade-up" style="animation-delay: 80ms;">
                <div>
                    <h2 class="text-[13px] sm:text-sm font-bold text-ink mb-3">Ringkasan</h2>

                    <div class="space-y-2 mb-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between text-xs text-ink">
                                <span class="font-medium">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                <span class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($order->customer_name || $order->notes || $order->customer_notes)
                        <div class="text-[11px] text-muted py-2 border-t border-line space-y-0.5">
                            @if ($order->customer_name)
                                <div>Pemesan: {{ $order->customer_name }}</div>
                            @endif
                            @if ($order->notes || $order->customer_notes)
                                <div>Catatan: {{ $order->notes ?? $order->customer_notes }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="border-t border-line pt-3 flex items-center justify-between">
                    <span class="text-sm font-bold text-ink">Total</span>
                    <span class="text-[17px] font-extrabold text-brand-900">
                        Rp {{ number_format($orderTotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Action Buttons --}}
    <div class="pt-6 {{ $inMockup ? 'space-y-2.5' : 'flex flex-col sm:flex-row gap-2.5 sm:gap-3' }} mt-auto">
        <a href="{{ route('customer.menu', ['table' => $cleanTable]) }}"
           class="w-full {{ $inMockup ? '' : 'sm:flex-1' }} h-12 rounded-xl bg-brand-900 hover:bg-brand-700 text-white font-semibold text-sm shadow-md transition-all hover:-translate-y-0.5 active:scale-[0.98] flex items-center justify-center">
            Pesan Lagi
        </a>
        <a href="{{ route('orders.history', ['table' => $cleanTable]) }}"
           class="w-full {{ $inMockup ? '' : 'sm:flex-1' }} h-12 rounded-xl bg-white border border-line hover:bg-gray-50 text-ink font-semibold text-sm shadow-2xs transition-all hover:-translate-y-0.5 active:scale-[0.98] flex items-center justify-center">
            Lihat Riwayat Pesanan
        </a>
    </div>
</div>

@if (! $inMockup)
@push('scripts')
<script src="{{ $midtransSnapJsUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
<script>
    const orderCode = @json($order->order_code);
    const currentStatus = @json($order->status);
    const currentPaymentStatus = @json($order->payment_status);
    const initialSnapRedirectUrl = @json($order->snap_redirect_url);
    let activeSnapToken = @json($order->snap_token);

    function showMidtransFeedback(message, isError = false) {
        const box = document.getElementById('midtrans-feedback');
        if (!box) return;
        box.textContent = message;
        box.classList.remove('hidden', 'bg-danger-bg', 'text-danger', 'bg-brand-100', 'text-brand-900');
        box.classList.add(isError ? 'bg-danger-bg' : 'bg-brand-100', isError ? 'text-danger' : 'text-brand-900');
    }

    async function fetchSnapToken(forceNew = false) {
        const res = await fetch(`/api/midtrans/orders/${orderCode}/token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ force_new: forceNew }),
        });

        const data = await res.json();
        if (!res.ok || !data.success) {
            throw new Error(data.message || 'Gagal menyiapkan pembayaran Midtrans.');
        }

        activeSnapToken = data.snap_token;
        return data;
    }

    async function openMidtransSnap() {
        const btn = document.getElementById('btn-midtrans-pay');
        if (btn) btn.disabled = true;

        try {
            const shouldForceNew = !activeSnapToken || currentPaymentStatus === 'failed' || currentPaymentStatus === 'expired';
            let redirectUrl = initialSnapRedirectUrl;

            if (shouldForceNew) {
                const tokenData = await fetchSnapToken(currentPaymentStatus === 'failed' || currentPaymentStatus === 'expired');
                redirectUrl = tokenData.redirect_url || redirectUrl;
            }

            if (window.snap && activeSnapToken) {
                window.snap.pay(activeSnapToken, {
                    onSuccess: function () {
                        showMidtransFeedback('Pembayaran berhasil! Memverifikasi status dengan Midtrans...');
                        verifyMidtransPayment(true);
                    },
                    onPending: function () {
                        showMidtransFeedback('Pembayaran menunggu penyelesaian. Memeriksa status...');
                        verifyMidtransPayment(true);
                    },
                    onError: function () {
                        showMidtransFeedback('Pembayaran gagal diproses. Silakan coba lagi.', true);
                    },
                    onClose: function () {
                        verifyMidtransPayment(false);
                    },
                });
            } else if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                showMidtransFeedback('Midtrans Snap tidak dapat dimuat. Silakan coba lagi.', true);
            }
        } catch (err) {
            showMidtransFeedback(err.message || 'Terjadi kesalahan saat membuka Midtrans.', true);
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    async function verifyMidtransPayment(silent = false) {
        const btn = document.getElementById('btn-midtrans-verify');
        if (btn && !silent) btn.disabled = true;

        try {
            const res = await fetch(`/api/midtrans/orders/${orderCode}/verify`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });
            const data = await res.json();

            if (data.paid || (data.payment_status && data.payment_status !== currentPaymentStatus)) {
                location.reload();
                return;
            }

            if (!silent && data.message) {
                showMidtransFeedback(data.message, !data.success);
            }
        } catch (err) {
            if (!silent) {
                showMidtransFeedback('Gagal memeriksa status pembayaran ke Midtrans.', true);
            }
        } finally {
            if (btn && !silent) btn.disabled = false;
        }
    }

    function checkLiveStatus() {
        fetch(`/api/pesanan/${orderCode}/status`)
            .then(res => res.json())
            .then(data => {
                if ((data.status && data.status !== currentStatus) || (data.payment_status && data.payment_status !== currentPaymentStatus)) {
                    location.reload();
                }
            })
            .catch(() => {});
    }

    if (currentStatus !== 'selesai' && currentStatus !== 'dibatalkan') {
        setInterval(checkLiveStatus, 4000);
    }
</script>
@endpush
@endif
