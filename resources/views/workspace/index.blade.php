@extends('layouts.dashboard')
@section('title', 'Project Workspaces')

@section('content')
<div class="p-8 min-h-screen" style="background: #F7F5F0;">

    {{-- PAGE HEADER --}}
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold" style="color: #1A1A2E;">Project Workspaces</h1>
    </div>

    {{-- TABS --}}
    <div class="flex gap-2 mb-6">
        <button onclick="switchTab('mine')" id="tab-mine"
            class="tab-btn px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200"
            style="background: #1A1A2E; color: white;">
            My Workspaces
        </button>
        <button onclick="switchTab('shared')" id="tab-shared"
            class="tab-btn px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 text-gray-500 bg-white border border-gray-200">
            Shared with me
        </button>
    </div>

    {{-- ===================== MY WORKSPACES ===================== --}}
    <div id="panel-mine">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            {{-- Loop through user's owned workspaces --}}
            @forelse($myWorkspaces ?? [] as $ws)
            <a href="{{ route('workspaces.show', $ws->id) }}"
               class="workspace-card group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block"
               style="height: 180px;">

                {{-- Cover Image --}}
                <div class="absolute inset-0">
                    @if($ws->cover_image)
                        <img src="{{ $ws->cover_image }}" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full" style="background: linear-gradient(135deg, #F9D6E7 0%, #C8216B 100%);"></div>
                    @endif
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.1) 60%, transparent 100%);"></div>
                </div>

                {{-- Badge --}}
                <div class="absolute top-3 left-3">
                    <span class="text-xs font-bold px-2 py-1 rounded-md" style="background: rgba(255,255,255,0.9); color: #C8216B;">OWNER</span>
                </div>

                {{-- Invite button --}}
                <button onclick="event.preventDefault(); openInviteModal({{ $ws->id }})"
                    class="absolute top-3 right-3 w-7 h-7 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    style="background: rgba(255,255,255,0.9);">
                    <svg width="14" height="14" fill="none" stroke="#C8216B" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                </button>

                {{-- Footer info --}}
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-white font-semibold text-sm leading-tight mb-2">{{ $ws->name }}</p>
                    {{-- Member avatars --}}
                    <div class="flex items-center gap-1">
                        @foreach($ws->members->take(3) as $member)
                        <img src="{{ $member->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=28&background=C8216B&color=fff' }}"
                             class="w-6 h-6 rounded-full border-2 border-white object-cover"
                             title="{{ $member->name }}">
                        @endforeach
                        @if($ws->members->count() > 3)
                        <span class="w-6 h-6 rounded-full border-2 border-white flex items-center justify-center text-xs font-bold text-white"
                              style="background: rgba(200,33,107,0.8);">
                            +{{ $ws->members->count() - 3 }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            {{-- Empty state placeholder (removed when real data loads) --}}
            @endforelse

            {{-- CREATE / JOIN WORKSPACE CARD --}}
            <button onclick="openCreateModal()"
                class="rounded-2xl border-2 border-dashed flex flex-col items-center justify-center gap-2 transition-all duration-200 hover:border-pink-400 hover:bg-pink-50 group"
                style="height: 180px; border-color: #D1D5DB;">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all group-hover:scale-110"
                     style="background: #F9D6E7;">
                    <svg width="20" height="20" fill="none" stroke="#C8216B" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-400 group-hover:text-pink-600">Create or Join Workspace</span>
            </button>
        </div>
    </div>

    {{-- ===================== SHARED WITH ME ===================== --}}
    <div id="panel-shared" class="hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($sharedWorkspaces ?? [] as $ws)
            <a href="{{ route('workspaces.show', $ws->id) }}"
               class="workspace-card group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block"
               style="height: 180px;">
                <div class="absolute inset-0">
                    @if($ws->cover_image)
                        <img src="{{ $ws->cover_image }}" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full" style="background: linear-gradient(135deg, #e0e7ff 0%, #6366f1 100%);"></div>
                    @endif
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.1) 60%, transparent 100%);"></div>
                </div>
                <div class="absolute top-3 left-3">
                    <span class="text-xs font-bold px-2 py-1 rounded-md" style="background: rgba(255,255,255,0.9); color: #4F46E5;">SHARED</span>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-white font-semibold text-sm leading-tight mb-2">{{ $ws->name }}</p>
                    <div class="flex items-center gap-1">
                        @foreach($ws->members->take(3) as $member)
                        <img src="{{ $member->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=28&background=6366f1&color=fff' }}"
                             class="w-6 h-6 rounded-full border-2 border-white object-cover">
                        @endforeach
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                <p class="text-sm">Belum ada workspace yang dibagikan ke kamu.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ===================== MODAL: CREATE WORKSPACE ===================== --}}
@push('modals')
<div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div onclick="closeCreateModal()" class="absolute inset-0" style="background: rgba(0,0,0,0.4);"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display font-bold text-xl" style="color: #1A1A2E;">Buat Workspace Baru</h2>
            <button onclick="closeCreateModal()" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('workspaces.store') }}" method="POST">
            @csrf
            {{-- Workspace Name --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Workspace</label>
                <input type="text" name="name" required placeholder="e.g. Kepanitiaan BEM 2025"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition-all">
            </div>

            {{-- Cover Image via Unsplash --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto Cover</label>
                <div class="flex gap-2 mb-2">
                    <input type="text" id="unsplash-query" placeholder="Cari foto (e.g. teamwork, nature...)"
                        class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition-all">
                    <button type="button" onclick="searchUnsplash()"
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                        style="background: var(--pink-primary, #C8216B);">
                        Cari
                    </button>
                </div>

                {{-- Unsplash Results Grid --}}
                <div id="unsplash-results" class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto hidden"></div>

                {{-- Loading --}}
                <div id="unsplash-loading" class="hidden text-center py-4">
                    <div class="inline-block w-6 h-6 border-2 border-pink-300 border-t-pink-600 rounded-full animate-spin"></div>
                </div>

                {{-- Hidden input for selected image URL --}}
                <input type="hidden" name="cover_image" id="selected-cover">

                {{-- Preview --}}
                <div id="cover-preview" class="hidden mt-2 rounded-xl overflow-hidden" style="height: 80px;">
                    <img id="cover-preview-img" src="" alt="" class="w-full h-full object-cover">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                    style="background: #C8216B;">
                    Buat Workspace
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // ─── TAB SWITCHING ───────────────────────────────────────────────
    function switchTab(tab) {
        const panels = { mine: document.getElementById('panel-mine'), shared: document.getElementById('panel-shared') };
        const tabs   = { mine: document.getElementById('tab-mine'),   shared: document.getElementById('tab-shared') };

        Object.keys(panels).forEach(k => {
            panels[k].classList.toggle('hidden', k !== tab);
            if (k === tab) {
                tabs[k].style.background = '#1A1A2E';
                tabs[k].style.color = 'white';
                tabs[k].classList.remove('text-gray-500', 'bg-white', 'border', 'border-gray-200');
            } else {
                tabs[k].style.background = 'white';
                tabs[k].style.color = '#6B7280';
                tabs[k].classList.add('text-gray-500', 'bg-white', 'border', 'border-gray-200');
            }
        });
    }

    // ─── CREATE MODAL ────────────────────────────────────────────────
    function openCreateModal() { document.getElementById('modal-create').classList.remove('hidden'); }
    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
        document.getElementById('unsplash-results').classList.add('hidden');
        document.getElementById('cover-preview').classList.add('hidden');
        document.getElementById('unsplash-query').value = '';
        document.getElementById('selected-cover').value = '';
    }

    // ─── UNSPLASH SEARCH ─────────────────────────────────────────────
    async function searchUnsplash() {
        const query   = document.getElementById('unsplash-query').value.trim();
        if (!query) return;

        const results  = document.getElementById('unsplash-results');
        const loading  = document.getElementById('unsplash-loading');

        results.classList.add('hidden');
        loading.classList.remove('hidden');

        try {
            // Call Laravel backend route which proxies Unsplash
            const res  = await fetch(`/api/unsplash/search?query=${encodeURIComponent(query)}`);
            const data = await res.json();

            loading.classList.add('hidden');
            results.innerHTML = '';

            data.results.forEach(photo => {
                const img = document.createElement('img');
                img.src = photo.urls.small;
                img.alt = photo.alt_description ?? query;
                img.className = 'w-full rounded-lg cursor-pointer object-cover transition-all hover:ring-2 hover:opacity-90';
                img.style.height = '64px';
                img.onclick = () => selectCover(photo.urls.regular, img);
                results.appendChild(img);
            });

            results.classList.remove('hidden');
        } catch (e) {
            loading.classList.add('hidden');
            results.innerHTML = '<p class="col-span-3 text-xs text-red-400 text-center py-2">Gagal memuat foto. Coba lagi.</p>';
            results.classList.remove('hidden');
        }
    }

    function selectCover(url, imgEl) {
        document.getElementById('selected-cover').value = url;
        // Highlight selected
        document.querySelectorAll('#unsplash-results img').forEach(i => i.classList.remove('ring-2', 'ring-pink-500'));
        imgEl.classList.add('ring-2', 'ring-pink-500');
        // Show preview
        document.getElementById('cover-preview-img').src = url;
        document.getElementById('cover-preview').classList.remove('hidden');
    }

    // Allow Enter key to search
    document.getElementById('unsplash-query')?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); searchUnsplash(); } });
</script>
@endpush
@endsection