@auth
<div id="logout-confirm-modal"
     onclick="if (event.target === this) closeLogoutModal()"
     class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 backdrop-blur-xs p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="logout-modal-title">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center space-y-4">
        <div class="w-14 h-14 rounded-full bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto shadow-xs">
            <svg class="w-7 h-7 stroke-current fill-none" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </div>

        <div class="space-y-1.5">
            <h3 id="logout-modal-title" class="text-lg font-extrabold text-slate-900 tracking-tight">Konfirmasi Keluar</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Apakah Anda yakin ingin keluar dari akun <strong class="text-slate-700">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Anda' }}</strong>? Sesi Anda akan diakhiri.
            </p>
        </div>

        <div class="flex items-center gap-2.5 pt-1">
            <button type="button"
                    onclick="closeLogoutModal()"
                    class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer">
                Batal
            </button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 stroke-current fill-none" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Ya, Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    window.openLogoutModal = function () {
        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeLogoutModal = function () {
        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            window.closeLogoutModal();
        }
    });
</script>
@endauth
