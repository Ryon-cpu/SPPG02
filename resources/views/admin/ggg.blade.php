@extends('layouts.admin')

@section('title', 'Profil Admin')
@section('page_title', 'Profil Admin')
@section('page_subtitle', 'Kelola informasi dan foto profil akun administrator')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    /* ===== MODAL OVERLAY ===== */
    #crop-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(2, 6, 23, 0.85);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn 0.2s ease;
    }
    #crop-modal.open {
        display: flex;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    #crop-modal-inner {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 1.5rem;
        width: 100%;
        max-width: 680px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 32px 80px rgba(0,0,0,0.6);
        animation: slideUp 0.25s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }

    /* ===== CROPPER AREA ===== */
    .cropper-wrap {
        width: 100%;
        height: 340px;
        min-height: 340px;
        background: #0f172a;
        border-radius: 1rem;
        overflow: hidden;
        border: 1.5px solid #334155;
        position: relative;
    }
    .cropper-wrap img {
        display: block;
        max-width: 100%;
    }
    .cropper-container {
        width: 100% !important;
        height: 100% !important;
    }
    /* Masking lingkaran di cropbox */
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
    }

    /* ===== AVATAR RING ===== */
    .avatar-ring {
        background: conic-gradient(from 0deg, #eeff00, #3b82f6, #8b5cf6, #10b981);
        padding: 3px;
        border-radius: 50%;
        display: inline-flex;
    }

    /* ===== UPLOAD ZONE ===== */
    .upload-zone {
        border: 2px dashed #334155;
        border-radius: 1.5rem;
        padding: 2.5rem 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: transparent;
    }
    .upload-zone:hover, .upload-zone.dragover {
        /*border-color: #10b981;*/
        background: rgba(16,185,129,0.04);
    }

    /* ===== CONTROL BUTTONS ===== */
    .ctrl-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #0f172a;
        border: 1px solid #334155;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.15s;
        font-size: 13px;
    }
    .ctrl-btn:hover {
        background: #1e3a5f;
        border-color: #3b82f6;
        color: #60a5fa;
    }

    /* ===== PREVIEWS ===== */
    #preview-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        background: #0f172a;
        border: 2px solid #334155;
        flex-shrink: 0;
    }
    #preview-circle-sm {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        overflow: hidden;
        background: #0f172a;
        border: 2px solid #334155;
        flex-shrink: 0;
    }

    /* Scrollbar modal */
    #crop-modal-inner::-webkit-scrollbar { width: 4px; }
    #crop-modal-inner::-webkit-scrollbar-track { background: #1e293b; }
    #crop-modal-inner::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
</style>
@endpush

@section('content')

{{-- ===== MODAL CROP (tersembunyi, muncul saat foto dipilih) ===== --}}
<div id="crop-modal">
    <div id="crop-modal-inner">
        {{-- Header Modal --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-700">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-crop-simple text-emerald-400"></i>
                    Sesuaikan Foto Profil
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Geser, zoom, dan putar foto lalu klik Simpan.</p>
            </div>
            <button type="button" id="modal-close-btn" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-700 transition" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        {{-- Cropper Area --}}
        <div class="px-6 pt-6">
            <div class="cropper-wrap">
                <img id="crop-image" src="#" alt="Preview Foto">
            </div>
        </div>

        {{-- Controls & Preview --}}
        <div class="px-6 py-4 flex items-center justify-between gap-4">
            {{-- Preview Lingkaran --}}
            <div class="flex items-center gap-3">
                <div class="avatar-ring" style="padding:2px">
                    <div id="preview-circle"></div>
                </div>
                <div class="avatar-ring" style="padding:2px">
                    <div id="preview-circle-sm"></div>
                </div>
                <span class="text-xs text-slate-500">Preview</span>
            </div>
            {{-- Tombol Kontrol --}}
            <div class="flex items-center gap-2 flex-wrap justify-end">
                <button type="button" class="ctrl-btn" onclick="if(cropper)cropper.zoom(0.1)" title="Zoom In"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                <button type="button" class="ctrl-btn" onclick="if(cropper)cropper.zoom(-0.1)" title="Zoom Out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                <button type="button" class="ctrl-btn" onclick="if(cropper)cropper.rotate(-45)" title="Putar Kiri"><i class="fa-solid fa-rotate-left"></i></button>
                <button type="button" class="ctrl-btn" onclick="if(cropper)cropper.rotate(45)" title="Putar Kanan"><i class="fa-solid fa-rotate-right"></i></button>
                <button type="button" class="ctrl-btn" onclick="if(cropper)cropper.reset()" title="Reset"><i class="fa-solid fa-arrows-rotate"></i></button>
                <button type="button" class="ctrl-btn" onclick="document.getElementById('foto-input').click()" title="Ganti Foto"><i class="fa-solid fa-images"></i></button>
            </div>
        </div>
        {{-- Tombol Simpan --}}
        <div class="px-6 pb-6">
            <form id="profile-form" method="POST" action="{{ route('admin.profil.update') }}">
                @csrf
                <input type="hidden" name="cropped_image" id="cropped-image-input">
                <button
                    type="submit"
                    id="save-btn"
                    class="w-full flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-lg shadow-emerald-600/25 active:scale-95">
                    <i class="fa-solid fa-floppy-disk text-base"></i>
                    Simpan Foto Profil
                </button>
            </form>
        </div>
    </div>
</div>
{{-- ===== HALAMAN PROFIL UTAMA ===== --}}
<div class="max-w-3xl mx-auto space-y-8">
    {{-- Kartu Ringkasan Profil --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-5">
                    {{-- Avatar & Ring --}}
                    <div class="avatar-ring" style="padding: 3px;">
                        @php
                            $hasCustomPhoto = file_exists(public_path('images/profile.jpeg')) || file_exists(public_path('images/profile.png'));
                        @endphp
                        @if ($hasCustomPhoto)
                            <img id="current-avatar" src="{{ asset('images/profile.jpeg') }}?v={{ time() }}" alt="Foto Profil" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover aspect-square block bg-slate-800 flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-3xl items-center justify-center hidden">
                                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
                            </div>
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-3xl flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info Singkat --}}
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">{{ auth()->user()->nama }}</h2>
                        <p class="text-sm text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>

                        <div class="flex flex-wrap items-center gap-2 mt-2.5">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                                Administrator
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">
                                <i class="fa-solid fa-calendar text-xs"></i>
                                Bergabung {{ auth()->user()->created_at->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons Foto --}}
                <div class="flex items-center gap-2 self-start sm:self-center">
                    <button type="button" id="change-photo-btn" onclick="document.getElementById('foto-input').click()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-md shadow-emerald-600/25 active:scale-95">
                        <i class="fa-solid fa-camera"></i>
                        <span>{{ $hasCustomPhoto ? 'Ganti Foto' : 'Unggah Foto' }}</span>
                    </button>

                    @if ($hasCustomPhoto)
                        <form action="{{ route('admin.profil.photo.delete') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali ke avatar inisial?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs border border-rose-200 transition active:scale-95" title="Hapus Foto Profil">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    @endif

                    {{-- Input file tersembunyi --}}
                    <input type="file" id="foto-input" accept="image/jpeg,image/png,image/webp,image/jpg" class="hidden">
                </div>
            </div>

            {{-- Detail Statistik/Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-xs border border-slate-200 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="fa-solid fa-user-shield text-base"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Hak Akses Role</p>
                        <p class="text-sm font-bold text-slate-800">Super Administrator</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-xs border border-slate-200 flex items-center justify-center text-sky-600 flex-shrink-0">
                        <i class="fa-solid fa-envelope-circle-check text-base"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs text-slate-400 font-medium">Email Login Aktif</p>
                        <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid 2 Kolom Form: Edit Akun & Ubah Kata Sandi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Card 1: Ganti Informasi Akun --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user-pen text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 leading-tight">Ubah Informasi Akun</h3>
                        <p class="text-xs text-slate-400">Ganti nama tampilan dan alamat email login</p>
                    </div>
                </div>

                <form action="{{ route('admin.profil.account') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                value="{{ old('nama', auth()->user()->nama) }}"
                                required
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-sm text-slate-800 font-medium"
                                placeholder="Nama Lengkap Administrator"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alamat Email Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', auth()->user()->email) }}"
                                required
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-sm text-slate-800 font-medium"
                                placeholder="admin@email.com"
                            >
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-md shadow-emerald-600/20 active:scale-95">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Card 2: Ubah Kata Sandi --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 border border-sky-200 text-sky-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 leading-tight">Ubah Kata Sandi</h3>
                        <p class="text-xs text-slate-400">Pastikan akun aman dengan sandi yang kuat</p>
                    </div>
                </div>

                <form action="{{ route('admin.profil.password') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 text-sm text-slate-800"
                                placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock-open absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                minlength="8"
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 text-sm text-slate-800"
                                placeholder="Minimal 8 karakter"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                minlength="8"
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 text-sm text-slate-800"
                                placeholder="Ulangi kata sandi baru"
                            >
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm transition shadow-md shadow-sky-600/20 active:scale-95">
                            <i class="fa-solid fa-shield-halved"></i>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    let cropper = null;

    const fotoInput    = document.getElementById('foto-input');
    const cropImage    = document.getElementById('crop-image');
    const modal        = document.getElementById('crop-modal');
    const modalClose   = document.getElementById('modal-close-btn');
    const croppedInput = document.getElementById('cropped-image-input');
    const profileForm  = document.getElementById('profile-form');
    const saveBtn      = document.getElementById('save-btn');

    // ---- Buka modal crop ----
    function openModal() {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    // ---- Tutup modal crop ----
    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        fotoInput.value = '';
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk text-base"></i> Simpan Foto Profil';
    }

    // ---- Klik di luar modal â†’ tutup ----
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    modalClose.addEventListener('click', closeModal);

    // ---- Escape key ----
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });

    // ---- Inisialisasi Cropper ----
    function setupCropper(imageSrc) {
        openModal();

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        cropImage.src = imageSrc;

        // Tunggu modal tampil dan browser menghitung ukuran wadah cropper
        setTimeout(() => {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.85,
                responsive: true,
                restore: false,
                checkCrossOrigin: false,
                checkOrientation: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                preview: ['#preview-circle', '#preview-circle-sm'],
                ready: function () {
                    if (cropper) {
                        cropper.crop();
                    }
                }
            });
        }, 80);
    }

    // ---- File dipilih â†’ tampilkan cropper ----
    fotoInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file melebihi 5MB. Silakan pilih foto yang lebih kecil.');
            fotoInput.value = '';
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/pjpeg'];
        const validExtensions = ['.jpg', '.jpeg', '.png', '.webp'];
        const fileName = file.name.toLowerCase();
        const isExtensionValid = validExtensions.some(ext => fileName.endsWith(ext));

        if (!validTypes.includes(file.type.toLowerCase()) && !isExtensionValid) {
            alert('Format tidak didukung. Gunakan JPEG, PNG, atau WebP.');
            fotoInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            setupCropper(ev.target.result);
        };
        reader.readAsDataURL(file);
    });

    // ---- Submit: ambil hasil crop base64 â†’ submit form ----
    profileForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!cropper) {
            alert('Belum ada foto yang dipilih atau crop belum siap.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>&nbsp; Menyimpan...';

        try {
            let canvas = null;

            // 1. Coba ambil cropped canvas resolusi 400x400
            try {
                canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
            } catch (e) {
                console.warn('getCroppedCanvas dengan dimensi gagal:', e);
            }

            // 2. Fallback: tanpa parameter dimensi
            if (!canvas) {
                try {
                    canvas = cropper.getCroppedCanvas();
                } catch (e) {
                    console.warn('getCroppedCanvas standar gagal:', e);
                }
            }

            // 3. Fallback manual dari koordinat crop jika method getCroppedCanvas return null
            if (!canvas) {
                const data = cropper.getData(true);
                canvas = document.createElement('canvas');
                canvas.width = 400;
                canvas.height = 400;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                const sx = Math.max(0, data.x || 0);
                const sy = Math.max(0, data.y || 0);
                const sw = data.width || cropImage.naturalWidth || 400;
                const sh = data.height || cropImage.naturalHeight || 400;
                ctx.drawImage(cropImage, sx, sy, sw, sh, 0, 0, 400, 400);
            }

            const base64Data = canvas.toDataURL('image/jpeg', 0.92);
            if (!base64Data || !base64Data.startsWith('data:image/jpeg;base64,')) {
                throw new Error('Format gambar tidak valid.');
            }

            croppedInput.value = base64Data;
            
            // Submit form
            HTMLFormElement.prototype.submit.call(profileForm);
        } catch (err) {
            console.error(err);
            alert('Gagal memproses foto: ' + (err.message || 'Terjadi kesalahan sistem.'));
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk text-base"></i> Simpan Foto Profil';
        }
    });
</script>
@endpush
