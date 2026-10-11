@extends('layouts.admin')

@section('title', 'Tambah Menu Gizi Harian')
@section('page_title', 'Tambah Menu Harian')
@section('page_subtitle', 'Input rincian item makanan dan nilai gizi untuk tanggal distribusi')

@push('styles')
<style>
    .item-row { transition: all 0.2s ease; }
    .item-row:hover { background: #f8fafc; }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { opacity: 0.5; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @if (isset($errors) && $errors->any())
        <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-sm">
            <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 flex-shrink-0 text-base"></i>
            <div>
                <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="menu-form" action="{{ route('admin.menu.store') }}" method="POST">
        @csrf

        {{-- Pilih Tanggal --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-day text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Tanggal Menu</h3>
                    <p class="text-xs text-slate-500">Pilih tanggal distribusi menu ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Tanggal <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal"
                           value="{{ old('tanggal', date('Y-m-d')) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-sm">
                </div>
                <div class="flex items-end">
                    <div id="nama-hari-display" class="w-full px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-emerald-500"></i>
                        <span id="hari-text">â€”</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Item Menu --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <i class="fa-solid fa-list-ul text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Item Makanan</h3>
                        <p class="text-xs text-slate-500">Masukkan setiap bahan makanan beserta nilai gizinya</p>
                    </div>
                </div>
                <button type="button" id="add-row-btn"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="items-table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-4 py-3 min-w-[160px]">Nama Item <span class="text-rose-400">*</span></th>
                            <th class="text-left px-3 py-3 min-w-[110px]">Takaran <span class="text-rose-400">*</span></th>
                            <th class="text-center px-3 py-3 min-w-[90px]"><span class="text-amber-600">Energi</span> (kkal)</th>
                            <th class="text-center px-3 py-3 min-w-[90px]"><span class="text-sky-600">Protein</span> (g)</th>
                            <th class="text-center px-3 py-3 min-w-[90px]"><span class="text-orange-500">Lemak</span> (g)</th>
                            <th class="text-center px-3 py-3 min-w-[90px]"><span class="text-emerald-600">Karbo</span> (g)</th>
                            <th class="text-center px-3 py-3 min-w-[90px]"><span class="text-purple-600">Serat</span> (g)</th>
                            <th class="px-3 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- Baris awal --}}
                        @php $defaultItems = old('items', [['nama_item'=>'','takaran'=>'','energi'=>0,'protein'=>0,'lemak'=>0,'karbo'=>0,'serat'=>0]]); @endphp
                        @foreach ($defaultItems as $i => $item)
                        <tr class="item-row border-b border-slate-50" data-index="{{ $i }}">
                            <td class="px-4 py-2.5">
                                <input type="text" name="items[{{ $i }}][nama_item]" placeholder="Nasi Putihâ€¦"
                                       value="{{ $item['nama_item'] ?? '' }}" required
                                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs">
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="text" name="items[{{ $i }}][takaran]" placeholder="200 g"
                                       value="{{ $item['takaran'] ?? '' }}" required
                                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs">
                            </td>
                            @foreach (['energi','protein','lemak','karbo','serat'] as $field)
                            <td class="px-3 py-2.5 text-center">
                                <input type="number" name="items[{{ $i }}][{{ $field }}]" placeholder="0"
                                       value="{{ $item[$field] ?? 0 }}" min="0" step="0.001" required
                                       class="w-full px-2 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs text-center">
                            </td>
                            @endforeach
                            <td class="px-3 py-2.5 text-center">
                                <button type="button" class="delete-row-btn p-1.5 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition" title="Hapus baris">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    {{-- Footer total --}}
                    <tfoot>
                        <tr class="bg-slate-900 text-white text-xs font-bold">
                            <td class="px-4 py-3" colspan="2"><i class="fa-solid fa-sigma mr-1.5 text-emerald-400"></i> Total</td>
                            <td class="px-3 py-3 text-center"><span id="total-energi" class="text-amber-300">0</span></td>
                            <td class="px-3 py-3 text-center"><span id="total-protein" class="text-sky-300">0</span></td>
                            <td class="px-3 py-3 text-center"><span id="total-lemak" class="text-orange-300">0</span></td>
                            <td class="px-3 py-3 text-center"><span id="total-karbo" class="text-emerald-300">0</span></td>
                            <td class="px-3 py-3 text-center"><span id="total-serat" class="text-purple-300">0</span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('admin.menu.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-lg shadow-emerald-600/25 active:scale-95">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Menu
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const HARI = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const tglInput = document.getElementById('tanggal');
    const hariText = document.getElementById('hari-text');

    function updateHariDisplay() {
        if (!tglInput.value) return;
        const d = new Date(tglInput.value + 'T00:00:00');
        hariText.textContent = 'Hari ' + HARI[d.getDay()] + ', ' + tglInput.value.split('-').reverse().join('/');
    }
    tglInput.addEventListener('change', updateHariDisplay);
    updateHariDisplay();

    // â”€â”€â”€ Hitung total kolom â”€â”€â”€
    function recalcTotals() {
        const fields = ['energi','protein','lemak','karbo','serat'];
        const totals = {energi:0,protein:0,lemak:0,karbo:0,serat:0};
        document.querySelectorAll('#items-body tr').forEach(row => {
            fields.forEach(f => {
                const inp = row.querySelector(`input[name*="[${f}]"]`);
                if (inp) totals[f] += parseFloat(inp.value || 0);
            });
        });
        document.getElementById('total-energi').textContent = totals.energi.toFixed(3);
        document.getElementById('total-protein').textContent = totals.protein.toFixed(3);
        document.getElementById('total-lemak').textContent   = totals.lemak.toFixed(3);
        document.getElementById('total-karbo').textContent   = totals.karbo.toFixed(3);
        document.getElementById('total-serat').textContent   = totals.serat.toFixed(3);
    }

    // â”€â”€â”€ Tambah baris baru â”€â”€â”€
    let rowIndex = {{ count($defaultItems) }};
    document.getElementById('add-row-btn').addEventListener('click', function () {
        const tbody = document.getElementById('items-body');
        const idx = rowIndex++;
        const tr = document.createElement('tr');
        tr.className = 'item-row border-b border-slate-50';
        tr.dataset.index = idx;
        tr.innerHTML = `
            <td class="px-4 py-2.5">
                <input type="text" name="items[${idx}][nama_item]" placeholder="Nama makananâ€¦" required
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs">
            </td>
            <td class="px-3 py-2.5">
                <input type="text" name="items[${idx}][takaran]" placeholder="100 g" required
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs">
            </td>
            ${['energi','protein','lemak','karbo','serat'].map(f => `
            <td class="px-3 py-2.5 text-center">
                <input type="number" name="items[${idx}][${f}]" placeholder="0" value="0" min="0" step="0.001" required
                       class="w-full px-2 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs text-center">
            </td>`).join('')}
            <td class="px-3 py-2.5 text-center">
                <button type="button" class="delete-row-btn p-1.5 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition" title="Hapus baris">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        tr.querySelector('input').focus();
        bindDeleteAndInput(tr);
        recalcTotals();
    });

    function bindDeleteAndInput(row) {
        row.querySelector('.delete-row-btn').addEventListener('click', function () {
            if (document.querySelectorAll('#items-body tr').length <= 1) {
                alert('Minimal harus ada 1 item makanan.');
                return;
            }
            row.remove();
            recalcTotals();
        });
        row.querySelectorAll('input[type="number"]').forEach(inp => {
            inp.addEventListener('input', recalcTotals);
        });
    }

    // Bind baris yang sudah ada
    document.querySelectorAll('#items-body tr').forEach(bindDeleteAndInput);
    recalcTotals();
</script>
@endpush
