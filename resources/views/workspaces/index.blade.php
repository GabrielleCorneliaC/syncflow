@extends('layouts.app')
@section('title', 'Project Workspaces')
@section('page-title', 'Project Workspaces')
@section('favicon', $workspace->cover_image ?? asset('assets/logo.png'))


@section('content')
<!-- <div class="p-6 bg-surface min-h-screen"> -->

    {{-- PAGE HEADER --}}

    <div class="flex items-end justify-between
            border-b border-[rgba(203,199,182,0.3)] pb-[13px]
            mb-6">

    {{-- Judul --}}
    <h1 class="font-montserrat font-extrabold text-[#1d1c17] tracking-[-0.02em]
               text-3xl sm:text-4xl lg:text-5xl leading-tight">
        Project Workspaces
    </h1>
    </div>

    {{-- TABS --}}
    <div class="flex gap-2 mb-6">
        <button onclick="switchTab('mine')" id="tab-mine"
            class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 bg-primary text-white shadow-sm hover:scale-105">
            My Workspaces
        </button>
        <button onclick="switchTab('shared')" id="tab-shared"
            class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 text-textsoft bg-muted border border-border hover:scale-105">
            Shared with me
        </button>
    </div>

    {{-- ===================== MY WORKSPACES ===================== --}}
    <div id="panel-mine">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-5 gap-y-10 pt-8">

            @forelse($myWorkspaces ?? [] as $ws)
                <a href="{{ route('workspaces.show', $ws->id) }}" class="block group hover:-translate-y-1 transition-transform duration-300">
                    <div class="relative">
                        
                        {{-- TAB ATAS FOLDER --}}
                        <div class="absolute -top-[27px] left-[-1px] h-7 w-28 border-t border-l border-r {{ $ws->cover_image ? 'border-[#cbc7b6]' : 'border-[#5c5c5c]' }} rounded-t-xl bg-[#f4a3a4] z-20 flex items-center px-3">
                            <span class="text-[10px] font-bold text-[#2a2a2a] flex items-center gap-1.5">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                OWNER
                            </span>
                        </div>

                        {{-- CONTAINER UTAMA --}}
                        <div class="relative z-10 w-full h-[180px] border border-[#5c5c5c] rounded-b-xl rounded-tr-xl 
                                    shadow-[3px_3px_0px_#cbc7b6] group-hover:shadow-[5px_5px_0px_#a8a493] 
                                    transition-all overflow-hidden flex flex-col justify-between p-4 
                                    {{ $ws->cover_image ? 'bg-muted' : 'bg-[#f4a3a4]' }}">

                            {{-- LAYER GAMBAR--}}
                            @if($ws->cover_image)
                                <div class="absolute inset-0 z-0">
                                    <img src="{{ $ws->cover_image }}" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-black/50"></div>
                                </div>
                            @endif

                            <div class="absolute top-0 left-[1px] h-[2px] w-[108px] {{ $ws->cover_image ? 'bg-muted' : 'bg-[#f4a3a4]' }} -translate-y-[1px] z-20"></div>

                            {{-- KONTEN (Teks & Avatar) --}}
                            <div class="relative z-30 flex flex-col justify-between h-full">
                                <div>
                                    <h3 class="font-semibold text-lg leading-tight line-clamp-2 {{ $ws->cover_image ? 'text-white' : 'text-[#2a2a2a]' }}">
                                        {{ $ws->name }}
                                    </h3>
                                    @if($ws->description)
                                        <p class="text-xs line-clamp-1 mt-1 {{ $ws->cover_image ? 'text-white/80' : 'text-[#4a4a4a]/80' }}">
                                            {{ $ws->description }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex -space-x-1.5">
                                    @foreach($ws->members->take(3) as $member)
                                        <img src="{{ $member->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name ?? 'Member').'&size=24&background=49473a&color=fff' }}" 
                                            class="w-7 h-7 rounded-full border-2 {{ $ws->cover_image ? 'border-white' : 'border-[#5c5c5c]' }} object-cover"
                                            title="{{ $member->name }}">
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                <h3 class="font-heading font-semibold text-textmain mb-1">Belum Ada Workspace</h3>
                <p class="text-sm text-textsoft mb-4">Buat workspace pertama kamu untuk mulai kolaborasi.</p>
            </div>
            @endforelse

            {{-- CREATE WORKSPACE FOLDER CARD --}}
            <button onclick="openCreateModal()" class="block w-full text-left group hover:scale-[1.02] transition-transform duration-200">
                <div class="relative">
                    <div class="absolute -top-[27px] left-[-1px] h-7 w-28 border-t border-l border-r border-dashed border-[#8c8c8c] rounded-t-xl bg-[#f5f4ef] z-20"></div>
                    
                    <div class="relative z-10 w-full h-[180px] bg-[#f5f4ef] border border-dashed border-[#8c8c8c] rounded-b-xl rounded-tr-xl flex flex-col items-center justify-center group-hover:border-primary transition-colors">
                        <div class="absolute top-0 left-[1px] h-[2px] w-[108px] bg-[#f5f4ef] -translate-y-[1px] z-20"></div>
                        <div class="w-10 h-10 flex items-center justify-center text-[#6a6a6a] group-hover:text-primary transition-all mb-1">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-[#6a6a6a] group-hover:text-primary transition-colors">Create or Join Workspace</span>
                    </div>
                </div>
            </button>
        </div>
    </div>

    {{-- ===================== SHARED WITH ME ===================== --}}
<div id="panel-shared" class="hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-5 gap-y-10 pt-8">
        
        @forelse($sharedWorkspaces ?? [] as $ws)
        @php
            $myMember = $ws->members->firstWhere('id', auth()->id());
            $roleLabel = $myMember ? strtoupper($myMember->pivot->role) : 'SHARED';
        @endphp
        <a href="{{ route('workspaces.show', $ws->id) }}" class="block group hover:-translate-y-1 transition-transform duration-300">
            <div class="relative">
                
                <div class="absolute -top-[27px] left-[-1px] h-7 w-28 border-t border-l border-r {{ $ws->cover_image ? 'border-[#cbc7b6]' : 'border-[#5c5c5c]' }} rounded-t-xl bg-[#e6e4df] z-20 flex items-center px-3">
                    <span class="text-[10px] font-bold text-[#4a4a4a] flex items-center gap-1.5">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        {{ $roleLabel }}
                    </span>
                </div>

                @if($ws->cover_image)
                    {{-- STYLE 1: DENGAN GAMBAR --}}
                    <div class="relative z-10 w-full h-[180px] border border-[#cbc7b6] rounded-b-xl rounded-tr-xl overflow-hidden shadow-[3px_3px_0px_#cbc7b6] group-hover:shadow-[5px_5px_0px_#a8a493] transition-all bg-muted">
                        <div class="absolute inset-0">
                            <img src="{{ $ws->cover_image }}" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.1) 100%);"></div>
                        </div>
                        <div class="relative z-30 p-4 h-full flex flex-col justify-end">
                            <p class="text-white font-bold text-lg leading-tight mb-3 line-clamp-2">{{ $ws->name }}</p>
                            <div class="flex items-center -space-x-1.5">
                                @foreach($ws->members->take(3) as $member)
                                <img src="{{ $member->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name ?? 'Member').'&size=24&background=49473a&color=fff' }}"
                                    class="w-7 h-7 rounded-full border-2 border-[#5c5c5c] object-cover"
                                    title="{{ $member->name }}">
                                @endforeach
                                @if($ws->members->count() > 3)
                                <span class="w-7 h-7 rounded-full border-2 border-[#5c5c5c] flex items-center justify-center text-[10px] font-bold text-white bg-primary">+{{ $ws->members->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{-- STYLE 2: TANPA GAMBAR --}}
                    <div class="relative z-10 w-full h-[180px] bg-[#e6e4df] border border-[#5c5c5c] rounded-b-xl rounded-tr-xl shadow-[3px_3px_0px_#cbc7b6] group-hover:shadow-[5px_5px_0px_#a8a493] transition-all flex flex-col justify-between p-4">
                        <div class="absolute top-0 left-[1px] h-[2px] w-[108px] bg-[#e6e4df] -translate-y-[1px] z-20"></div>

                        <div class="relative z-30">
                            {{-- Judul --}}
                            <h3 class="font-semibold text-lg text-[#3a3a3a] leading-tight line-clamp-1">{{ $ws->name }}</h3>
                            
                            {{-- TAMBAHKAN INI: Deskripsi --}}
                            @if($ws->description)
                                <p class="text-xs text-[#6a6a6a] line-clamp-2 mt-1">{{ $ws->description }}</p>
                            @endif
                        </div>

                        <div class="flex -space-x-1.5 relative z-30">
                            @foreach($ws->members->take(3) as $member)
                            <img src="{{ $member->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name ?? 'Member').'&size=24&background=random' }}"
                                    class="w-7 h-7 rounded-full border border-[#5c5c5c] object-cover"
                                    title="{{ $member->name }}">
                            @endforeach
                            @if($ws->members->count() > 3)
                            <div class="w-7 h-7 rounded-full border border-[#5c5c5c] bg-[#e6e4df] flex items-center justify-center text-[9px] font-bold text-[#3a3a3a]">
                                +{{ $ws->members->count() - 3 }}
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </a>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
            <h3 class="font-heading font-semibold text-textmain mb-1">Belum Ada Data</h3>
            <p class="text-sm text-textsoft">Belum ada workspace yang dibagikan ke kamu.</p>
        </div>
        @endforelse
    </div>
</div>

    {{-- ===================== MODAL: CREATE WORKSPACE ===================== --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div onclick="closeCreateModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 border border-border flex flex-col max-h-[90vh]">
            
            <div class="flex-shrink-0 flex items-center justify-between p-6 pb-4 border-b border-gray-100">
                <h2 class="font-heading font-bold text-xl text-textmain">Buat Workspace Baru</h2>
                <button onclick="closeCreateModal()" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-muted transition-colors text-textsoft">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('workspaces.store') }}" method="POST" class="flex-1 overflow-y-auto p-6 pt-4 flex flex-col">
                @csrf
                @if ($errors->any())
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3">
                    <label class="block text-sm font-semibold text-textmain mb-1.5">Nama Workspace</label>
                    <input type="text" name="name" required placeholder="e.g. Kepanitiaan BEM 2026"
                        class="w-full border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-surface">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-semibold text-textmain mb-1.5">Deskripsi Singkat</label>
                    <input type="text" name="description" placeholder="Deskripsi singkat workspace (opsional)"
                        class="w-full border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-surface">
                </div>

                <div class="mb-2 flex-1">
                    <label class="block text-sm font-semibold text-textmain mb-1.5">Foto Cover (Opsional)</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="unsplash-query" placeholder="Cari foto (e.g. teamwork, nature...)"
                            class="flex-1 border border-border rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-surface">
                        <button type="button" onclick="searchUnsplash()" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition-colors">Cari</button>
                    </div>
                    <div id="unsplash-results" class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto hidden"></div>
                    <div id="unsplash-loading" class="hidden text-center py-4">
                        <div class="inline-block w-6 h-6 border-2 border-border border-t-primary rounded-full animate-spin"></div>
                    </div>
                    <input type="hidden" name="cover_image" id="selected-cover">
                    <div id="cover-preview" class="hidden mt-2 rounded-xl overflow-hidden border border-border" style="height: 80px;">
                        <img id="cover-preview-img" src="" alt="" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="flex gap-3 mt-auto pt-3">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 py-2 bg-muted text-textsoft font-semibold rounded-xl border border-border hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 py-2 bg-primary text-white font-semibold rounded-xl shadow-[3px_3px_0px_#6a1452] active:translate-y-px active:shadow-[1px_1px_0_#6a1452] transition-all">Buat Workspace</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MODAL: INVITE MEMBER ===================== --}}
    <div id="modal-invite" 
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div onclick="closeInviteModal()" class="absolute inset-0"></div>
        <div class="w-full max-w-md p-8 bg-white border border-border shadow-2xl rounded-3xl relative z-10">
            <h2 class="text-2xl font-heading font-bold text-center text-textmain mb-6">Undang Anggota via Email</h2>
            <form id="invite-form" class="space-y-6" method="POST" action="#">
                @csrf
                <div class="space-y-2">
                    <label for="invite-email" class="block text-sm font-medium text-textmain">Alamat Email</label>
                    <input type="email" id="invite-email" name="email" placeholder="e.g., nama@email.com"
                        class="w-full px-4 py-3 border border-border rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition bg-surface" required />
                </div>
                <div class="flex items-center gap-4">
                    <button type="button" onclick="closeInviteModal()" class="flex-1 py-2.5 bg-muted text-textsoft font-semibold rounded-xl border border-border hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-primary text-white font-semibold rounded-xl shadow-[3px_3px_0px_#6a1452] active:translate-y-px active:shadow-[1px_1px_0_#6a1452] transition-all">Kirim Undangan</button>
                </div>
            </form>
        </div>
    </div>

<!-- </div> -->
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalCreate = document.getElementById('modal-create');
        const modalInvite = document.getElementById('modal-invite');
        
        if(modalCreate) document.body.appendChild(modalCreate);
        if(modalInvite) document.body.appendChild(modalInvite);
    });

    function switchTab(tab) {
        const panels = { mine: document.getElementById('panel-mine'), shared: document.getElementById('panel-shared') };
        const tabs   = { mine: document.getElementById('tab-mine'),   shared: document.getElementById('tab-shared') };

        Object.keys(panels).forEach(k => {
            panels[k].classList.toggle('hidden', k !== tab);
            if (k === tab) {
                tabs[k].className = "px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 bg-primary text-white shadow-sm hover:scale-105";
            } else {
                tabs[k].className = "px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 text-textsoft bg-muted border border-border hover:scale-105";
            }
        });
    }

    function openCreateModal() { document.getElementById('modal-create').classList.remove('hidden'); }
    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
        document.getElementById('unsplash-results').classList.add('hidden');
        document.getElementById('cover-preview').classList.add('hidden');
        document.getElementById('unsplash-query').value = '';
        document.getElementById('selected-cover').value = '';
    }

    async function searchUnsplash() {
        const query = document.getElementById('unsplash-query').value.trim();
        if (!query) return;

        const results = document.getElementById('unsplash-results');
        const loading = document.getElementById('unsplash-loading');

        results.classList.add('hidden');
        loading.classList.remove('hidden');

        try {
            const res = await fetch(`/api/unsplash/search?query=${encodeURIComponent(query)}`);
            loading.classList.add('hidden');
            results.innerHTML = '';

            if (!res.ok) {
                results.innerHTML = '<p class="col-span-3 text-xs text-red-500 text-center py-2">Gagal memuat foto.</p>';
                results.classList.remove('hidden');
                return;
            }

            const data = await res.json();
            const photos = Array.isArray(data.results) ? data.results : [];

            if (photos.length === 0) {
                results.innerHTML = '<p class="col-span-3 text-xs text-textsoft text-center py-2">Tidak ditemukan.</p>';
                results.classList.remove('hidden');
                return;
            }

            photos.forEach(photo => {
                const img = document.createElement('img');
                img.src = photo.urls.small;
                img.alt = photo.alt_description ?? query;
                img.className = 'w-full rounded-lg cursor-pointer object-cover transition-all hover:ring-2 hover:ring-primary';
                img.style.height = '64px';
                img.onclick = () => selectCover(photo.urls.regular, img);
                results.appendChild(img);
            });

            results.classList.remove('hidden');
        } catch (e) {
            loading.classList.add('hidden');
            results.innerHTML = '<p class="col-span-3 text-xs text-red-500 text-center py-2">Gagal memuat foto.</p>';
            results.classList.remove('hidden');
        }
    }

    function selectCover(url, imgEl) {
        document.getElementById('selected-cover').value = url;
        document.querySelectorAll('#unsplash-results img').forEach(i => i.classList.remove('ring-2', 'ring-primary'));
        imgEl.classList.add('ring-2', 'ring-primary');
        document.getElementById('cover-preview-img').src = url;
        document.getElementById('cover-preview').classList.remove('hidden');
    }

    document.getElementById('unsplash-query')?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); searchUnsplash(); } });

   
</script>
@endpush