@extends('layouts.admin')

@section('title', 'Meja & QR Code')

@section('content')
<div class="max-w-7xl mx-auto">
    @if($errors->any())
        <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm flex items-start justify-between gap-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" onclick="this.parentElement.remove()" class="text-xs font-bold cursor-pointer shrink-0">×</button>
        </div>
    @endif

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1B2B24] tracking-tight">Meja & QR Code</h2>
            <p class="text-xs sm:text-[13px] text-[#66776D] mt-1">
                Buat QR code untuk pemesanan mandiri di setiap meja
            </p>
        </div>
        <div>
            <button type="button"
                    onclick="document.getElementById('modal-tambah-meja').classList.remove('hidden')"
                    class="bg-[#184233] hover:bg-[#224E3D] text-white px-5 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold inline-flex items-center gap-2 shadow-sm transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Meja</span>
            </button>
        </div>
    </div>

    <!-- Grid Kartu Meja (Responsive: Mobile 1-col, Tablet 2-col, Desktop 4-col) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
        @forelse($tables as $table)
            <div class="bg-white border border-[#EBE6D8] rounded-[20px] p-4 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                <!-- Top Section: QR Box -->
                <div>
                    <div class="bg-[#FAF3E5] rounded-[16px] h-[148px] flex items-center justify-center p-4 mb-3.5 relative overflow-hidden group">
                        <!-- Crisp QR Code Icon / Vector SVG (matches Figma A7 Meja & QR Code) -->
                        <div class="w-20 h-20 text-[#2D3748] flex items-center justify-center">
                            <svg class="w-16 h-16" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <!-- Top-Left Finder Box -->
                                <rect x="8" y="8" width="16" height="16" rx="4" />
                                <rect x="13" y="13" width="6" height="6" rx="1.5" fill="currentColor" />
                                <!-- Top-Right Finder Box -->
                                <rect x="40" y="8" width="16" height="16" rx="4" />
                                <rect x="45" y="13" width="6" height="6" rx="1.5" fill="currentColor" />
                                <!-- Bottom-Left Finder Box -->
                                <rect x="8" y="40" width="16" height="16" rx="4" />
                                <rect x="13" y="45" width="6" height="6" rx="1.5" fill="currentColor" />
                                <!-- QR Pattern Elements -->
                                <path d="M40 40h4v6h-4z" fill="currentColor" />
                                <path d="M48 40h6v4h-6z" fill="currentColor" />
                                <path d="M44 50h10v4h-10z" fill="currentColor" />
                            </svg>
                        </div>

                        <!-- Overlay Quick Scan Link on hover -->
                        <a href="{{ $table->qr_url }}" target="_blank" class="absolute inset-0 bg-[#184233]/85 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition p-2 text-center text-xs font-semibold rounded-[16px]">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Buka Meja</span>
                        </a>
                    </div>

                    <!-- Table Name & Status Badge -->
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h3 class="font-bold text-[15px] text-[#1B2B24] truncate">{{ $table->name }}</h3>

                        <!-- Status Toggle Form -->
                        <form action="{{ route('admin.tables.toggle', $table->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    title="Klik untuk ubah status"
                                    class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold transition cursor-pointer
                                    {{ $table->status === 'aktif' ? 'bg-[#E3F2E9] text-[#1E7446] hover:bg-[#d0ebd9]' : 'bg-[#FCE8E6] text-[#C53929] hover:bg-[#fad3cf]' }}">
                                {{ ucfirst($table->status) }}
                            </button>
                        </form>
                    </div>

                    <!-- Token Text -->
                    <p class="text-[11px] text-[#8C988F] font-normal mb-3">
                        Token: <span class="font-mono text-gray-600">{{ $table->token }}</span>
                    </p>
                </div>

                <!-- Action Buttons Row (Unduh, Cetak, Edit, Hapus) -->
                <div class="pt-2.5 border-t border-gray-100/80 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('admin.tables.download-qr', $table->id) }}"
                           class="bg-white border border-[#E6E0CC] hover:bg-gray-50 text-[#1B2B24] py-1.5 px-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition text-center shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-[#1B2B24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh</span>
                        </a>

                        <a href="{{ route('admin.tables.print', $table->id) }}"
                           target="_blank"
                           class="bg-white border border-[#E6E0CC] hover:bg-gray-50 text-[#1B2B24] py-1.5 px-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition text-center shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-[#1B2B24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                onclick="openEditTableModal(this)"
                                data-update-url="{{ route('admin.tables.update', $table) }}"
                                data-number="{{ $table->table_number }}"
                                data-name="{{ $table->name }}"
                                data-status="{{ $table->status }}"
                                data-token="{{ $table->token }}"
                                class="bg-[#EBF2EE] border border-[#CFE2D6] hover:bg-[#DDEEE3] text-[#184233] py-1.5 px-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span>Edit</span>
                        </button>

                        <button type="button"
                                onclick="openDeleteTableModal(this)"
                                data-delete-url="{{ route('admin.tables.destroy', $table) }}"
                                data-name="{{ $table->name }}"
                                class="bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 py-1.5 px-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center text-gray-400">
                Belum ada meja yang terdaftar. Silakan tambahkan meja baru.
            </div>
        @endforelse
    </div>

    <!-- Paginasi (Sesuai Figma Node 7:762) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#66776D] pt-4 border-t border-[#E6E0CC]">
        <p>
            Menampilkan {{ $tables->count() }} dari {{ $totalTables }} meja
        </p>
        <div class="flex items-center gap-1.5">
            @if($tables->onFirstPage())
                <span class="w-8 h-8 rounded-lg bg-gray-100 border border-[#E6E0CC] flex items-center justify-center text-gray-400 cursor-not-allowed">&lt;</span>
            @else
                <a href="{{ $tables->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-[#E6E0CC] flex items-center justify-center text-[#1B2B24] hover:bg-gray-50">&lt;</a>
            @endif

            @for($page = 1; $page <= $tables->lastPage(); $page++)
                @if($page == $tables->currentPage())
                    <span class="w-8 h-8 rounded-lg bg-[#184233] text-white font-semibold flex items-center justify-center">{{ $page }}</span>
                @else
                    <a href="{{ $tables->url($page) }}" class="w-8 h-8 rounded-lg bg-white border border-[#E6E0CC] text-[#1B2B24] font-semibold flex items-center justify-center hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endfor

            @if($tables->hasMorePages())
                <a href="{{ $tables->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-[#E6E0CC] flex items-center justify-center text-[#1B2B24] hover:bg-gray-50">&gt;</a>
            @else
                <span class="w-8 h-8 rounded-lg bg-gray-100 border border-[#E6E0CC] flex items-center justify-center text-gray-400 cursor-not-allowed">&gt;</span>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Meja -->
<div id="modal-tambah-meja" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-add-title">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 id="modal-add-title" class="font-bold text-base text-[#1B2B24]">Tambah Meja Baru</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-meja').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">×</button>
        </div>

        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-5">
                <div>
                    <label for="add-table-number" class="block text-xs font-semibold text-gray-700 mb-1">Nomor Meja *</label>
                    <input type="text" id="add-table-number" name="table_number" placeholder="Contoh: 09" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#184233] focus:outline-hidden">
                </div>
                <div>
                    <label for="add-table-name" class="block text-xs font-semibold text-gray-700 mb-1">Nama Meja (Opsional)</label>
                    <input type="text" id="add-table-name" name="name" placeholder="Contoh: Meja 09 (Outdoor)" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#184233] focus:outline-hidden">
                </div>
                <div>
                    <label for="add-table-status" class="block text-xs font-semibold text-gray-700 mb-1">Status Awal</label>
                    <select id="add-table-status" name="status" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#184233] focus:outline-hidden bg-white">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-tambah-meja').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#184233] text-white hover:bg-[#224E3D] cursor-pointer">Simpan Meja</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Meja -->
<div id="modal-edit-meja" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 id="modal-edit-title" class="font-bold text-base text-[#1B2B24]">Edit Meja &amp; QR</h3>
            <button type="button" onclick="closeEditTableModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">×</button>
        </div>

        <form id="form-edit-meja" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4 mb-5">
                <div>
                    <label for="edit-table-number" class="block text-xs font-semibold text-gray-700 mb-1">Nomor Meja *</label>
                    <input type="text" id="edit-table-number" name="table_number" maxlength="10" required placeholder="Contoh: 09" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#184233] focus:outline-hidden">
                </div>
                <div>
                    <label for="edit-table-name" class="block text-xs font-semibold text-gray-700 mb-1">Nama Meja (Opsional)</label>
                    <input type="text" id="edit-table-name" name="name" maxlength="50" placeholder="Contoh: Meja 09 (Outdoor)" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#184233] focus:outline-hidden">
                </div>
                <div>
                    <label for="edit-table-status" class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select id="edit-table-status" name="status" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#184233] focus:outline-hidden bg-white">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="rounded-xl border border-[#EBE6D8] bg-[#FAF8F4] p-3">
                    <label for="edit-regenerate-token" class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" id="edit-regenerate-token" name="regenerate_token" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#184233] focus:ring-[#184233]">
                        <span class="text-xs text-[#1B2B24] leading-relaxed">
                            <span class="font-semibold block">Buat ulang token QR</span>
                            <span class="text-[11px] text-[#66776D] block mt-0.5">
                                Token saat ini: <span id="edit-table-token-preview" class="font-mono text-gray-700"></span>. Jika dicentang, QR lama yang sudah dicetak tidak akan berlaku lagi.
                            </span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditTableModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#184233] text-white hover:bg-[#224E3D] cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Meja -->
<div id="modal-hapus-meja" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-delete-title">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h3 id="modal-delete-title" class="text-base font-bold text-[#1B2B24] text-center">Hapus Meja?</h3>
        <p id="delete-table-text" class="text-xs text-[#66776D] text-center mt-1.5 leading-relaxed">
            Meja dan QR code ini akan dihapus dan tidak dapat digunakan lagi untuk pemesanan.
        </p>

        <form id="form-hapus-meja" method="POST" action="" class="mt-5 flex items-center justify-center gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteTableModal()" class="w-1/2 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer">
                Batal
            </button>
            <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm cursor-pointer">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditTableModal(button) {
        const modal = document.getElementById('modal-edit-meja');
        const form = document.getElementById('form-edit-meja');
        const numberInput = document.getElementById('edit-table-number');
        const nameInput = document.getElementById('edit-table-name');
        const statusSelect = document.getElementById('edit-table-status');
        const tokenPreview = document.getElementById('edit-table-token-preview');
        const regenerateCheckbox = document.getElementById('edit-regenerate-token');

        form.action = button.dataset.updateUrl || '';
        numberInput.value = button.dataset.number || '';
        nameInput.value = button.dataset.name || '';
        statusSelect.value = button.dataset.status || 'aktif';
        tokenPreview.textContent = button.dataset.token || '-';
        regenerateCheckbox.checked = false;

        modal.classList.remove('hidden');
    }

    function closeEditTableModal() {
        document.getElementById('modal-edit-meja').classList.add('hidden');
    }

    function openDeleteTableModal(button) {
        const modal = document.getElementById('modal-hapus-meja');
        const form = document.getElementById('form-hapus-meja');
        const text = document.getElementById('delete-table-text');
        const tableName = button.dataset.name || 'meja ini';

        form.action = button.dataset.deleteUrl || '';
        text.textContent = `Apakah Anda yakin ingin menghapus "${tableName}"? QR code meja ini tidak dapat digunakan lagi setelah dihapus.`;

        modal.classList.remove('hidden');
    }

    function closeDeleteTableModal() {
        document.getElementById('modal-hapus-meja').classList.add('hidden');
    }

    ['modal-tambah-meja', 'modal-edit-meja', 'modal-hapus-meja'].forEach(function (modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        ['modal-tambah-meja', 'modal-edit-meja', 'modal-hapus-meja'].forEach(function (modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endpush
