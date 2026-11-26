@php
    $inMockup = $inMockup ?? false;
@endphp

<div class="bg-[#F9F7F2] rounded-[32px] p-5 sm:p-7 shadow-xl border border-gray-200/70 w-full relative overflow-hidden transition-all" id="status-container">

    <!-- Icon Sukses Besar & Judul -->
    <div class="text-center pt-2 mb-5">
        <div class="w-20 h-20 rounded-full bg-[#1D4939] flex items-center justify-center text-white mx-auto shadow-md">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight mt-4">
            Pesanan terkirim!
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
            Kode pesanan {{ $order->order_code }} <span class="text-gray-300 mx-1.5">|</span> Meja {{ $order->table_number }}
        </p>
    </div>

    <!-- Responsive Layout: Stacked on Mobile/Mockup, Side-by-side or clean spacing on Desktop -->
    <div class="{{ $inMockup ? 'space-y-3.5' : 'grid grid-cols-1 md:grid-cols-2 gap-4 mb-4' }}">
        
        <!-- Card 1: Status Pesanan -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-gray-900">Status pesanan</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                        {{ $order->status === 'menunggu' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                        {{ $order->status === 'diproses' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                        {{ $order->status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full 
                            {{ $order->status === 'menunggu' ? 'bg-amber-500 animate-pulse' : '' }}
                            {{ $order->status === 'diproses' ? 'bg-blue-500 animate-pulse' : '' }}
                            {{ $order->status === 'selesai' ? 'bg-emerald-500' : '' }}"></span>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <!-- Stepper Timeline -->
                <div class="flex items-center justify-between px-2 sm:px-3 relative my-4">
                    <!-- Step 1: Menunggu -->
                    <div class="flex flex-col items-center relative z-10">
                        <div class="w-7 h-7 rounded-full bg-[#1D4939] text-white flex items-center justify-center text-xs shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-[#1D4939] mt-2 text-center">Menunggu</span>
                    </div>

                    <!-- Line 1 -->
                    <div class="h-0.5 flex-1 mx-1.5 -mt-5 {{ in_array($order->status, ['diproses', 'selesai']) ? 'bg-[#1D4939]' : 'bg-gray-200' }} transition-colors duration-300"></div>

                    <!-- Step 2: Diproses -->
                    <div class="flex flex-col items-center relative z-10">
                        @if(in_array($order->status, ['diproses', 'selesai']))
                            <div class="w-7 h-7 rounded-full bg-[#1D4939] text-white flex items-center justify-center text-xs shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-[#1D4939] mt-2 text-center">Diproses</span>
                        @else
                            <div class="w-7 h-7 rounded-full border-2 border-gray-300 bg-white"></div>
                            <span class="text-xs text-gray-400 font-medium mt-2 text-center">Diproses</span>
                        @endif
                    </div>

                    <!-- Line 2 -->
                    <div class="h-0.5 flex-1 mx-1.5 -mt-5 {{ $order->status === 'selesai' ? 'bg-[#1D4939]' : 'bg-gray-200' }} transition-colors duration-300"></div>

                    <!-- Step 3: Selesai -->
                    <div class="flex flex-col items-center relative z-10">
                        @if($order->status === 'selesai')
                            <div class="w-7 h-7 rounded-full bg-[#1D4939] text-white flex items-center justify-center text-xs shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-[#1D4939] mt-2 text-center">Selesai</span>
                        @else
                            <div class="w-7 h-7 rounded-full border-2 border-gray-300 bg-white"></div>
                            <span class="text-xs text-gray-400 font-medium mt-2 text-center">Selesai</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Testing status control helper -->
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                <span>Uji Status (Demo):</span>
                <div class="flex items-center gap-1.5">
                    <form action="{{ route('orders.update-status', ['order_code' => $order->order_code]) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="menunggu">
                        <button type="submit" class="px-2 py-0.5 rounded text-[10px] {{ $order->status === 'menunggu' ? 'bg-[#1D4939] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Menunggu</button>
                    </form>
                    <form action="{{ route('orders.update-status', ['order_code' => $order->order_code]) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="diproses">
                        <button type="submit" class="px-2 py-0.5 rounded text-[10px] {{ $order->status === 'diproses' ? 'bg-[#1D4939] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Diproses</button>
                    </form>
                    <form action="{{ route('orders.update-status', ['order_code' => $order->order_code]) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="px-2 py-0.5 rounded text-[10px] {{ $order->status === 'selesai' ? 'bg-[#1D4939] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Selesai</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 2: Ringkasan -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-900 mb-3">Ringkasan</h2>

                <div class="space-y-2 mb-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-xs text-gray-700">
                            <span class="font-medium">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                @if($order->customer_name || $order->notes)
                    <div class="text-[11px] text-gray-500 py-2 border-t border-gray-100 space-y-0.5">
                        @if($order->customer_name)
                            <div><span class="text-gray-400">Pemesan:</span> {{ $order->customer_name }}</div>
                        @endif
                        @if($order->notes)
                            <div><span class="text-gray-400">Catatan:</span> {{ $order->notes }}</div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                <span class="text-sm font-bold text-gray-900">Total</span>
                <span class="text-base sm:text-lg font-bold text-[#1D4939]">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

    </div>

    <!-- Notice Card: Silakan bayar di kasir -->
    <div class="bg-[#FCF4DF] rounded-2xl p-3.5 flex items-start gap-2.5 text-xs text-[#8D5B28] leading-relaxed mb-6">
        <div class="mt-0.5 shrink-0 text-[#8D5B28]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>
        <span>Silakan bayar di kasir. Pesanan diproses setelah kasir menerimanya.</span>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-2.5 sm:space-y-0 sm:flex sm:items-center sm:gap-3">
        <a href="{{ route('cart') }}" 
           class="w-full sm:flex-1 py-3.5 rounded-2xl bg-[#1D4939] hover:bg-[#143529] text-white font-semibold text-sm shadow-md transition text-center block cursor-pointer active:scale-[0.99]">
            Pesan Lagi
        </a>
        <a href="{{ route('orders.history', ['table' => $order->table_number]) }}" 
           class="w-full sm:flex-1 py-3.5 rounded-2xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-semibold text-sm shadow-2xs transition text-center block cursor-pointer active:scale-[0.99]">
            Lihat Riwayat Pesanan
        </a>
    </div>

</div>

@push('scripts')
<script>
    // Live status polling every 4 seconds
    const orderCode = "{{ $order->order_code }}";
    let currentStatus = "{{ $order->status }}";

    function checkLiveStatus() {
        fetch(`/api/pesanan/${orderCode}/status`)
            .then(res => res.json())
            .then(data => {
                if (data.status && data.status !== currentStatus) {
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
