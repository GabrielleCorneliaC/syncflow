{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║           SYNCFLOW — COMPONENT GUIDE (Panduan untuk Seluruh Tim)            ║
║                                                                              ║
║  File ini adalah REFERENSI saja, tidak perlu di-render.                     ║
║  Copy-paste class yang dibutuhkan ke halaman masing-masing.                 ║
║                                                                              ║
║  Lead Frontend: Elizabeth                                                    ║
╚══════════════════════════════════════════════════════════════════════════════╝

════════════════════════════════════════
  HOW TO USE MASTER LAYOUT
════════════════════════════════════════

@extends('layouts.app')                  ← selalu extends ini

@section('title', 'Nama Halaman')        ← judul tab browser
@section('page-title', 'Nama Halaman')  ← judul di Topbar

@section('content')
    ...konten halaman kamu di sini...
@endsection

@push('scripts')
    <script> ...JS spesifik halaman... </script>
@endpush


════════════════════════════════════════
  WARNA BRAND (Tailwind class)
════════════════════════════════════════

text-primary         → #b30084 (magenta brand)
bg-primary           → background magenta
text-textmain        → #1d1c17 (teks utama)
text-textsoft        → #49473a (teks sekunder)
text-texthint        → #9b9887 (teks hint/placeholder)
bg-surface           → #faf9f7 (background halaman)
bg-muted             → #f2ede5 (background input/card muted)
border-border        → #cbc7b6 (warna border standar)


════════════════════════════════════════
  TYPOGRAPHY
════════════════════════════════════════

font-heading         → Montserrat (untuk judul)
font-body            → Inter (untuk teks biasa, sudah default)

Contoh heading:
<h2 class="font-heading font-bold text-xl text-textmain">Judul</h2>

Contoh label:
<p class="text-xs text-texthint uppercase tracking-widest font-semibold">Label</p>


════════════════════════════════════════
  KOMPONEN STANDAR (sf-* classes)
════════════════════════════════════════

─── INPUT FORM ───

<div>
    <label class="sf-label">Nama Field</label>
    <input type="text" name="field" class="sf-input" placeholder="...">
    <p class="sf-error-msg">Pesan error di sini</p>    ← kalau ada error
</div>

─── TOMBOL ───

{{-- Primary (magenta) --}}
<button class="sf-btn sf-btn-primary">Simpan</button>

{{-- Secondary (outline) --}}
<button class="sf-btn sf-btn-secondary">Batal</button>

{{-- Danger (merah) --}}
<button class="sf-btn sf-btn-danger">Hapus</button>

{{-- Ukuran kecil - tambah class text-sm --}}
<button class="sf-btn sf-btn-primary text-sm">Simpan</button>


─── BADGE / STATUS ───

<span class="sf-badge sf-badge-primary">Admin</span>
<span class="sf-badge sf-badge-success">Selesai</span>
<span class="sf-badge sf-badge-warning">Pending</span>
<span class="sf-badge sf-badge-danger">Gagal</span>
<span class="sf-badge sf-badge-neutral">Draft</span>


─── CARD ───

<div class="sf-card">
    ...konten card...
</div>

{{-- Card dengan header --}}
<div class="sf-card">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-bold text-base text-textmain">Judul Card</h3>
        <button class="sf-btn sf-btn-primary text-sm">+ Tambah</button>
    </div>
    ...isi card...
</div>


─── TABEL ───

<div class="sf-card overflow-x-auto">
    <table class="sf-table">
        <thead class="bg-muted">
            <tr>
                <th>Kolom 1</th>
                <th>Kolom 2</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->field1 }}</td>
                <td>{{ $item->field2 }}</td>
                <td class="text-right">
                    <a href="#" class="sf-btn sf-btn-secondary text-xs px-3 py-1.5">Edit</a>
                    <button class="sf-btn sf-btn-danger text-xs px-3 py-1.5">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


─── FLASH MESSAGE ───

{{-- Success --}}
@if(session('success'))
<div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">
    {{ session('success') }}
</div>
@endif

{{-- Error --}}
@if(session('error'))
<div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
    {{ session('error') }}
</div>
@endif


─── EMPTY STATE ───

<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-16 h-16 rounded-2xl bg-muted flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-texthint" ...></svg>
    </div>
    <h3 class="font-heading font-semibold text-textmain mb-1">Belum Ada Data</h3>
    <p class="text-sm text-texthint mb-4">Deskripsi singkat mengapa kosong.</p>
    <a href="#" class="sf-btn sf-btn-primary">+ Tambah Pertama</a>
</div>


─── PAGINATION ───

Cukup taruh ini di bawah tabel/list:
{{ $items->links() }}


════════════════════════════════════════
  RESPONSIVITAS — BREAKPOINTS
════════════════════════════════════════

sm:  ≥ 640px   (tablet kecil)
md:  ≥ 768px   (tablet)
lg:  ≥ 1024px  (laptop)
xl:  ≥ 1280px  (desktop)

Pola grid responsif yang disarankan:
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

Sembunyikan di mobile, tampilkan di desktop:
<div class="hidden sm:block">...</div>

Tampilkan di mobile, sembunyikan di desktop:
<div class="sm:hidden">...</div>


════════════════════════════════════════
  CARA TAMBAH NAV ITEM DI SIDEBAR
════════════════════════════════════════

Di file layouts/app.blade.php, bagian @yield('sidebar-items'),
kamu bisa override dari halaman masing-masing, atau
minta Elizabeth untuk menambahkan nav item baru ke sidebar.

Alternatif: langsung edit app.blade.php dan tambahkan:

<a href="{{ route('nama.route') }}"
   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-textsoft hover:bg-muted transition
          {{ request()->routeIs('nama.*') ? 'active' : '' }}">
    <svg class="w-5 h-5 shrink-0" ...>...</svg>
    <span class="nav-label">Nama Menu</span>
</a>


════════════════════════════════════════
  CHART.JS — CONTOH PENGGUNAAN
════════════════════════════════════════

Chart.js sudah di-load di layouts/app.blade.php.
Tinggal buat canvas dan inisialisasi di @push('scripts').

<canvas id="myChart" class="max-h-64"></canvas>

@push('scripts')
<script>
new Chart(document.getElementById('myChart'), {
    type: 'bar',           // bar | line | doughnut | pie | radar
    data: {
        labels: ['Jan', 'Feb', 'Mar'],
        datasets: [{
            label: 'Jumlah',
            data: [12, 19, 8],
            backgroundColor: '#b30084',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
    }
});
</script>
@endpush

Warna brand yang konsisten untuk chart:
- Primary:  '#b30084'
- Muted:    '#f2ede5'
- Success:  '#16a34a'
- Warning:  '#ca8a04'
- Info:     '#2563eb'

--}}
