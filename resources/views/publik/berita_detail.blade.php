@extends('layouts.publik')

@section('title', $berita->judul . ' - Berita MBG')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <a href="{{ route('berita.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700 mb-6">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Semua Berita
    </a>
    <article class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm mb-12">
        <div class="flex items-center gap-3 text-xs text-slate-500 mb-4 pb-4 border-b border-slate-100">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                <i class="fa-regular fa-calendar"></i>
                {{ \Carbon\Carbon::parse($berita->tanggal_publish)->translatedFormat('d F Y') }}
            </span>
            <span>&bull;</span>
            <span>Status: <strong class="text-emerald-700 uppercase font-semibold">{{ $berita->status }}</strong></span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 mb-6 leading-tight">
            {{ $berita->judul }}
        </h1>
        @if ($berita->thumbnail)
            <div class="aspect-video w-full rounded-2xl overflow-hidden mb-8 bg-slate-100 shadow-xs">
                <img src="{{ url('media/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
            </div>
        @endif
        <div class="prose max-w-none text-slate-700 text-base sm:text-lg leading-relaxed whitespace-pre-line">
            {{ $berita->isi }}
        </div>
    </article>
    <!-- Berita Terkait / Lainnya -->
    @if ($berita_lainnya && $berita_lainnya->count() > 0)
        <div>
            <h3 class="text-xl font-bold text-slate-900 mb-6">Berita Terkait Lainnya</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($berita_lainnya as $item)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-emerald-300 transition">
                        @if ($item->thumbnail)
                            <div class="aspect-video rounded-xl overflow-hidden mb-3 bg-slate-100">
                                <img src="{{ url('media/' . $item->thumbnail) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <span class="text-xs text-slate-400 block mb-1">{{ \Carbon\Carbon::parse($item->tanggal_publish)->translatedFormat('d M Y') }}</span>
                        <h4 class="font-bold text-sm text-slate-800 line-clamp-2 hover:text-emerald-600 mb-2">
                            <a href="{{ route('berita.detail', $item->id) }}">{{ $item->judul }}</a>
                        </h4>
                        <a href="{{ route('berita.detail', $item->id) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                            Baca &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
