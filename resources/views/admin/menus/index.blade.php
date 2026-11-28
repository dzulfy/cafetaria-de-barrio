@extends('layouts.admin')

@section('title', 'Menu & Kategori')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm flex items-start justify-between gap-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" onclick="this.parentElement.remove()" class="text-xs font-bold cursor-pointer shrink-0">×</button>
        </div>
    @endif

    <!-- Top Header matching Image 1 -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Menu & Kategori</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola daftar menu, harga, dan ketersediaan</p>
        </div>
        <div>
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 bg-[#184233] hover:bg-[#224E3D] text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm transition-all hover:shadow active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Menu</span>
            </button>
        </div>
    </div>

    <!-- Category Filter Pills matching Image 1 -->
    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('admin.menus.index', ['q' => $search]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all shadow-sm {{ $selectedCategory === 'semua' ? 'bg-[#184233] text-white' : 'bg-white text-gray-700 hover:bg-[#EBF2EE]/60 border border-gray-200' }}">
            <span>Semua</span>
            <span class="text-xs opacity-90">{{ $totalMenusCount }}</span>
        </a>

        @foreach($categories as $category)
        <a href="{{ route('admin.menus.index', ['kategori' => $category->slug, 'q' => $search]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all shadow-sm {{ $selectedCategory === $category->slug ? 'bg-[#184233] text-white' : 'bg-white text-gray-700 hover:bg-[#EBF2EE]/60 border border-gray-200' }}">
            <span>{{ $category->name }}</span>
            <span class="text-xs {{ $selectedCategory === $category->slug ? 'text-[#8DAAA0]' : 'text-gray-400' }}">{{ $category->menus_count }}</span>
        </a>
        @endforeach
    </div>

    <!-- Search Input matching Image 1 -->
    <div class="relative">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="relative">
            @if($selectedCategory !== 'semua')
                <input type="hidden" name="kategori" value="{{ $selectedCategory }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" 
                   name="q" 
                   value="{{ $search }}" 
                   placeholder="Cari nama menu..." 
                   class="w-full bg-white pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] shadow-sm text-gray-800 placeholder-gray-400 transition-all">
        </form>
    </div>

    <!-- Menu Table Card matching Image 1 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/70 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#EBF2EE] text-[#184233] text-xs font-semibold tracking-wider border-b border-gray-200/60">
                        <th class="py-3.5 px-6">Menu</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Harga</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50/70 transition-colors group">
                        <!-- Menu Name & Icon Thumbnail -->
                        <td class="py-3.5 px-6">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-[#f5ede1] flex items-center justify-center text-[#966b3b] flex-shrink-0 shadow-inner overflow-hidden">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-900 block">{{ $menu->name }}</span>
                                    @if($menu->description)
                                    <span class="text-xs text-gray-400 truncate max-w-xs block">{{ $menu->description }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Kategori -->
                        <td class="py-3.5 px-6 text-gray-600 font-medium">
                            {{ $menu->getRelationValue('category')?->name ?? $menu->category }}
                        </td>

                        <!-- Harga -->
                        <td class="py-3.5 px-6 font-semibold text-gray-900">
                            {{ $menu->formatted_price }}
                        </td>

                        <!-- Status Toggle Button -->
                        <td class="py-3.5 px-6">
                            <button type="button"
                                    role="switch"
                                    aria-checked="{{ $menu->is_available ? 'true' : 'false' }}"
                                    data-id="{{ $menu->id }}"
                                    data-available="{{ $menu->is_available ? '1' : '0' }}"
                                    onclick="toggleMenuStatus(this)"
                                    title="Klik untuk ubah ketersediaan menu"
                                    class="status-toggle min-w-[124px] inline-flex items-center justify-between gap-2.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all cursor-pointer select-none {{ $menu->is_available ? 'bg-[#EBF2EE] border-[#184233]/20 text-[#184233] hover:bg-[#DDEEE3]' : 'bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100' }}">
                                <span class="status-track flex h-5 w-9 shrink-0 items-center rounded-full p-0.5 transition-colors {{ $menu->is_available ? 'bg-[#184233] justify-end' : 'bg-rose-300 justify-start' }}">
                                    <span class="w-4 h-4 rounded-full bg-white shadow-xs"></span>
                                </span>
                                <span id="status-label-{{ $menu->id }}" class="status-label flex-1 text-left">
                                    {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                </span>
                            </button>
                        </td>

                        <!-- Aksi (Edit & Delete) -->
                        <td class="py-3.5 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" 
                                        onclick="openEditModal(this)"
                                        data-update-url="{{ route('admin.menus.update', $menu) }}"
                                        data-name="{{ $menu->name }}"
                                        data-category-id="{{ $menu->category_id }}"
                                        data-price="{{ $menu->price }}"
                                        data-description="{{ $menu->description ?? '' }}"
                                        data-is-available="{{ $menu->is_available ? '1' : '0' }}"
                                        data-image-url="{{ $menu->image ? asset('storage/' . $menu->image) : '' }}"
                                        title="Edit Menu"
                                        class="p-1.5 text-gray-500 hover:text-[#184233] hover:bg-[#EBF2EE] rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        onclick="confirmDelete({{ $menu->id }}, '{{ addslashes($menu->name) }}')" 
                                        title="Hapus Menu"
                                        class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="font-medium text-gray-600">Tidak ada menu ditemukan</p>
                            <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian atau kategori filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer matching Image 1: Menampilkan 1-7 dari 24 menu, < 1 2 3 > -->
        <div class="px-6 py-4 bg-white border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
            <div>
                Menampilkan {{ $menus->firstItem() ?? 0 }}-{{ $menus->lastItem() ?? 0 }} dari {{ $menus->total() }} menu
            </div>
            <div class="flex items-center gap-1.5 self-center">
                {{-- Previous Page Link --}}
                @if ($menus->onFirstPage())
                    <span class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-300 cursor-not-allowed">
                        &lt;
                    </span>
                @else
                    <a href="{{ $menus->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">
                        &lt;
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($menus->getUrlRange(1, $menus->lastPage()) as $page => $url)
                    @if ($page == $menus->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-[#184233] text-white font-semibold flex items-center justify-center shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-700 hover:bg-[#EBF2EE]/60 font-medium flex items-center justify-center transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($menus->hasMorePages())
                    <a href="{{ $menus->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">
                        &gt;
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-300 cursor-not-allowed">
                        &gt;
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Slide-Over Form: Tambah / Edit Menu matching Image 1 -->
<div id="menu-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" onclick="closeMenuModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 border border-gray-100">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h3 id="modal-title" class="text-lg font-bold text-gray-900">Tambah Menu</h3>
                <button type="button" onclick="closeMenuModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="menu-form" method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div id="method-spoofing"></div>
                <input type="hidden" id="input-remove-image" name="remove_image" value="0">

                <!-- Nama Menu -->
                <div>
                    <label for="input-name" class="block text-xs font-semibold text-gray-700 mb-1">Nama menu</label>
                    <input type="text" 
                           id="input-name" 
                           name="name" 
                           required 
                           placeholder="Contoh: Kopi Tubruk" 
                           class="w-full text-sm rounded-xl border border-gray-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] placeholder-gray-400">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="input-category" class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select id="input-category" 
                            name="category_id" 
                            required 
                            class="w-full text-sm rounded-xl border border-gray-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] bg-white text-gray-700">
                        <option value="" disabled selected>Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga (Rp) -->
                <div>
                    <label for="input-price" class="block text-xs font-semibold text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" 
                           id="input-price" 
                           name="price" 
                           required 
                           min="0"
                           placeholder="Contoh: 12000" 
                           class="w-full text-sm rounded-xl border border-gray-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] placeholder-gray-400">
                </div>

                <!-- Deskripsi (Singkat) -->
                <div>
                    <label for="input-description" class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi menu</label>
                    <textarea id="input-description" 
                              name="description" 
                              rows="2"
                              placeholder="Contoh: Espresso, susu, gula aren asli" 
                              class="w-full text-sm rounded-xl border border-gray-200 px-3.5 py-2 focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] placeholder-gray-400"></textarea>
                </div>

                <!-- Foto Menu (Drag & Drop box matching Image 1) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Foto menu</label>
                    <label for="input-image" class="border-2 border-dashed border-gray-200 hover:border-[#184233]/40 bg-[#FAF8F4] rounded-2xl p-4 flex flex-col items-center justify-center cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-full bg-[#EBF2EE] text-[#184233] flex items-center justify-center mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-800">Klik untuk unggah foto</span>
                        <span class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP, atau GIF, maks. 5 MB</span>
                        <input type="file" id="input-image" name="image" accept="image/png, image/jpeg, image/webp, image/gif" class="sr-only" onchange="previewImage(this)">
                    </label>
                    <div id="image-preview" class="mt-2 hidden">
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl border border-gray-200">
                            <img id="preview-img" src="" alt="Preview" class="w-10 h-10 object-cover rounded-lg">
                            <span id="preview-name" class="text-xs text-gray-600 truncate flex-1"></span>
                            <button type="button" onclick="clearImagePreview(true)" class="text-red-500 hover:text-red-700 text-xs font-medium cursor-pointer">Hapus Foto</button>
                        </div>
                    </div>
                </div>

                <!-- Status Toggle -->
                <div class="pt-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status ketersediaan</label>
                    <label id="modal-status-pill"
                           for="input-status"
                           class="inline-flex items-center gap-3 px-3.5 py-2 rounded-full border border-[#184233]/20 bg-[#EBF2EE] text-[#184233] text-xs font-bold cursor-pointer select-none transition-all">
                        <input type="checkbox" id="input-status" name="is_available" value="1" checked onchange="syncModalStatusSwitch()" class="sr-only">
                        <span id="modal-status-track" class="flex h-5 w-9 shrink-0 items-center justify-end rounded-full bg-[#184233] p-0.5 transition-colors">
                            <span class="w-4 h-4 rounded-full bg-white shadow-xs"></span>
                        </span>
                        <span id="modal-status-label">Tersedia</span>
                    </label>
                </div>

                <!-- Buttons: Batal & Simpan -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" 
                            onclick="closeMenuModal()" 
                            class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-[#184233] hover:bg-[#224E3D] text-white text-xs font-semibold shadow-sm transition-all active:scale-95 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="delete-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6 border border-gray-100">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 text-center">Hapus Menu?</h3>
            <p id="delete-menu-text" class="text-xs text-gray-500 text-center mt-1">Menu ini akan dihapus secara permanen dari database.</p>

            <form id="delete-form" method="POST" action="" class="mt-5 flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="w-1/2 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function applyStatusButtonState(button, isAvailable) {
        const track = button.querySelector('.status-track');
        const label = button.querySelector('.status-label');

        button.dataset.available = isAvailable ? '1' : '0';
        button.setAttribute('aria-checked', isAvailable ? 'true' : 'false');

        button.className = isAvailable
            ? 'status-toggle min-w-[124px] inline-flex items-center justify-between gap-2.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all cursor-pointer select-none bg-[#EBF2EE] border-[#184233]/20 text-[#184233] hover:bg-[#DDEEE3]'
            : 'status-toggle min-w-[124px] inline-flex items-center justify-between gap-2.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all cursor-pointer select-none bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100';

        if (track) {
            track.className = isAvailable
                ? 'status-track flex h-5 w-9 shrink-0 items-center rounded-full p-0.5 transition-colors bg-[#184233] justify-end'
                : 'status-track flex h-5 w-9 shrink-0 items-center rounded-full p-0.5 transition-colors bg-rose-300 justify-start';
        }

        if (label) {
            label.textContent = isAvailable ? 'Tersedia' : 'Habis';
        }
    }

    async function toggleMenuStatus(button) {
        const menuId = button.dataset.id;
        const previousAvailable = button.dataset.available === '1';
        button.disabled = true;

        try {
            const response = await fetch(`/admin/menu/${menuId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            if (response.ok && data.success) {
                applyStatusButtonState(button, !!data.is_available);
                showToast(data.message, 'success');
            } else {
                applyStatusButtonState(button, previousAvailable);
                showToast(data.message || 'Gagal mengubah status menu', 'error');
            }
        } catch (err) {
            console.error(err);
            applyStatusButtonState(button, previousAvailable);
            showToast('Gagal mengubah status menu', 'error');
        } finally {
            button.disabled = false;
        }
    }

    function syncModalStatusSwitch() {
        const checkbox = document.getElementById('input-status');
        const pill = document.getElementById('modal-status-pill');
        const track = document.getElementById('modal-status-track');
        const label = document.getElementById('modal-status-label');
        if (!checkbox || !pill || !track || !label) return;

        const isChecked = checkbox.checked;
        pill.className = isChecked
            ? 'inline-flex items-center gap-3 px-3.5 py-2 rounded-full border border-[#184233]/20 bg-[#EBF2EE] text-[#184233] text-xs font-bold cursor-pointer select-none transition-all'
            : 'inline-flex items-center gap-3 px-3.5 py-2 rounded-full border border-rose-200 bg-rose-50 text-rose-700 text-xs font-bold cursor-pointer select-none transition-all';
        track.className = isChecked
            ? 'flex h-5 w-9 shrink-0 items-center justify-end rounded-full bg-[#184233] p-0.5 transition-colors'
            : 'flex h-5 w-9 shrink-0 items-center justify-start rounded-full bg-rose-300 p-0.5 transition-colors';
        label.textContent = isChecked ? 'Tersedia' : 'Habis';
    }

    // Create / Edit Modal Handler
    const modal = document.getElementById('menu-modal');
    const modalTitle = document.getElementById('modal-title');
    const menuForm = document.getElementById('menu-form');
    const methodSpoofing = document.getElementById('method-spoofing');

    function openCreateModal() {
        modalTitle.textContent = 'Tambah Menu';
        menuForm.action = "{{ route('admin.menus.store') }}";
        methodSpoofing.innerHTML = '';
        menuForm.reset();
        clearImagePreview(false);
        document.getElementById('input-remove-image').value = '0';
        document.getElementById('input-status').checked = true;
        syncModalStatusSwitch();
        modal.classList.remove('hidden');
    }

    function openEditModal(button) {
        const data = button.dataset;
        modalTitle.textContent = 'Edit Menu';
        menuForm.action = data.updateUrl || '';
        methodSpoofing.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('input-name').value = data.name || '';
        document.getElementById('input-category').value = data.categoryId || '';
        document.getElementById('input-price').value = data.price || '';
        document.getElementById('input-description').value = data.description || '';
        document.getElementById('input-status').checked = data.isAvailable === '1';
        document.getElementById('input-remove-image').value = '0';
        document.getElementById('input-image').value = '';
        syncModalStatusSwitch();

        if (data.imageUrl) {
            document.getElementById('preview-img').src = data.imageUrl;
            document.getElementById('preview-name').textContent = 'Foto saat ini';
            document.getElementById('image-preview').classList.remove('hidden');
        } else {
            clearImagePreview(false);
        }

        modal.classList.remove('hidden');
    }

    function closeMenuModal() {
        modal.classList.add('hidden');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            document.getElementById('input-remove-image').value = '0';
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-name').textContent = input.files[0].name;
                document.getElementById('image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImagePreview(markForRemoval = false) {
        document.getElementById('input-image').value = '';
        document.getElementById('preview-img').src = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('input-remove-image').value = markForRemoval ? '1' : '0';
    }

    // Delete confirmation
    function confirmDelete(id, name) {
        const deleteModal = document.getElementById('delete-modal');
        const deleteForm = document.getElementById('delete-form');
        const deleteText = document.getElementById('delete-menu-text');
        
        deleteForm.action = `/admin/menu/${id}`;
        deleteText.textContent = `Apakah Anda yakin ingin menghapus "${name}"?`;
        deleteModal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
@endpush
