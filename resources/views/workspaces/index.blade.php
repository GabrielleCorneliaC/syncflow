@extends('layouts.app')
@section('title', 'Project Workspaces')
@section('page-title', 'Project Workspaces')

@section('content')
{{-- Pembungkus utama disesuaikan dengan background halaman resmi tim --}}
<div class="p-6 bg-surface min-h-screen">

    {{-- PAGE HEADER --}}
    <div class="mb-6">
        <h1 class="font-heading font-bold text-3xl text-textmain">Project Workspaces</h1>
    </div>

    {{-- TABS --}}
    <div class="flex gap-2 mb-6">
        <button onclick="switchTab('mine')" id="tab-mine"
            class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 bg-primary text-white">
            My Workspaces
        </button>
        <button onclick="switchTab('shared')" id="tab-shared"
            class="px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 text-textsoft bg-muted border border-border">
            Shared with me
        </button>
    </div>

    {{-- ===================== MY WORKSPACES ===================== --}}
    <div id="panel-mine">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            {{-- Loop through user's owned workspaces --}}
            @forelse($myWorkspaces ?? [] as $ws)
            <a href="{{ route('workspaces.show', $ws->id) }}"
               class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block bg-muted"
               style="height: 180px;">

                {{-- Cover Image --}}
                <div class="absolute inset-0">
                    @if($ws->cover_image)
                        <img src="{{ $ws->cover_image }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{-- Gradasi menggunakan warna utama tim (Magenta) --}}
                        <div class="w-full h-full" style="background: linear-gradient(135deg, #f2ede5 0%, #b30084 100%);"></div>
                    @endif
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);"></div>
                </div>

                {{-- Badge Admin --}}
                <div class="absolute top-3 left-3">
                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-white text-primary">OWNER</span>
                </div>

                {{-- Invite button --}}
                <button onclick="event.preventDefault(); openInviteModal({{ $ws->id }})"
                    class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-white shadow">
                    <svg width="14" height="14" fill="none" stroke="#b30084" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                </button>

                {{-- Footer info --}}
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-white font-bold text-base leading-tight mb-1">{{ $ws->name }}</p>
                    @if($ws->description)
                    <p class="text-xs text-white/85 truncate mb-2">{{ \Illuminate\Support\Str::limit($ws->description, 60) }}</p>
                    @endif
                    
                    {{-- Member avatars --}}
                    <div class="flex items-center gap-1">
                        @foreach($ws->members->take(3) as $member)
                        <img src="{{ $member->user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->user->name ?? 'Member').'&size=24&background=b30084&color=fff' }}"
                             class="w-6 h-6 rounded-full border-2 border-white object-cover"
                             title="{{ $member->user->name ?? 'Member' }}">
                        @endforeach
                        @if($ws->members->count() > 3)
                        <span class="w-6 h-6 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold text-white bg-primary/90">
                            +{{ $ws->members->count() - 3 }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            {{-- Empty state standar tim --}}
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                <h3 class="font-heading font-semibold text-textmain mb-1">Belum Ada Workspace</h3>
                <p class="text-sm text-textsoft mb-4">Buat workspace pertama kamu untuk mulai kolaborasi.</p>
            </div>
            @endforelse

            {{-- CREATE / JOIN WORKSPACE CARD --}}
            <button onclick="openCreateModal()"
                class="rounded-2xl border-2 border-dashed flex flex-col items-center justify-center gap-2 transition-all duration-200 border-border hover:border-primary hover:bg-muted group"
                style="height: 180px;">
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-muted group-hover:bg-white transition-all group-hover:scale-110">
                    <svg width="20" height="20" fill="none" stroke="#b30084" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-textsoft group-hover:text-primary">Create or Join Workspace</span>
            </button>
        </div>
    </div>

    {{-- ===================== SHARED WITH ME ===================== --}}
    <div id="panel-shared" class="hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($sharedWorkspaces ?? [] as $ws)
            <a href="{{ route('workspaces.show', $ws->id) }}"
               class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block bg-muted"
               style="height: 180px;">
                <div class="absolute inset-0">
                    @if($ws->cover_image)
                        <img src="{{ $ws->cover_image }}" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full" style="background: linear-gradient(135deg, #f2ede5 0%, #49473a 100%);"></div>
                    @endif
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);"></div>
                </div>
                <div class="absolute top-3 left-3">
                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-white text-textsoft">SHARED</span>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-white font-bold text-base leading-tight mb-2">{{ $ws->name }}</p>
                    <div class="flex items-center gap-1">
                        @foreach($ws->members->take(3) as $member)
                        <img src="{{ $member->user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($member->user->name ?? 'Member').'&size=24&background=49473a&color=fff' }}"
                             class="w-6 h-6 rounded-full border-2 border-white object-cover">
                        @endforeach
                    </div>
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
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div onclick="closeCreateModal()" class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-heading font-bold text-xl text-textmain">Buat Workspace Baru</h2>
                <button onclick="closeCreateModal()" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-muted transition-colors text-textsoft">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('workspaces.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-textmain mb-1.5">Nama Workspace</label>
                    <input type="text" name="name" required placeholder="e.g. Kepanitiaan BEM 2026"
                        class="w-full border border-border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-surface">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-textmain mb-1.5">Deskripsi Singkat</label>
                    <input type="text" name="description" placeholder="Deskripsi singkat workspace (opsional)"
                        class="w-full border border-border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-surface">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-textmain mb-1.5">Foto Cover</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="unsplash-query" placeholder="Cari foto (e.g. teamwork, nature...)"
                            class="flex-1 border border-border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-surface">
                        <button type="button" onclick="searchUnsplash()" class="sf-btn sf-btn-primary text-sm">Cari</button>
                    </div>
                    <div id="unsplash-results" class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto hidden"></div>
                    <div id="unsplash-loading" class="hidden text-center py-4">
                        <div class="inline-block w-6 h-6 border-2 border-border border-t-primary rounded-full animate-spin"></div>
                    </div>
                    <input type="hidden" name="cover_image" id="selected-cover">
                    <div id="cover-preview" class="hidden mt-2 rounded-xl overflow-hidden" style="height: 80px;">
                        <img id="cover-preview-img" src="" alt="" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeCreateModal()" class="sf-btn sf-btn-secondary flex-1">Batal</button>
                    <button type="submit" class="sf-btn sf-btn-primary flex-1">Buat Workspace</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MODAL: INVITE MEMBER ===================== --}}
    <div id="modal-invite" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/40">
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
                    <button type="button" onclick="closeInviteModal()" class="sf-btn sf-btn-secondary flex-1">Batal</button>
                    <button type="submit" class="sf-btn sf-btn-primary flex-1">Kirim Undangan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        const panels = { mine: document.getElementById('panel-mine'), shared: document.getElementById('panel-shared') };
        const tabs   = { mine: document.getElementById('tab-mine'),   shared: document.getElementById('tab-shared') };

        Object.keys(panels).forEach(k => {
            panels[k].classList.toggle('hidden', k !== tab);
            if (k === tab) {
                tabs[k].className = "px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 bg-primary text-white";
            } else {
                tabs[k].className = "px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 text-textsoft bg-muted border border-border";
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

    function openInviteModal(workspaceId) {
        let id = workspaceId;
        if (!id) {
            const parts = window.location.pathname.split('/').filter(Boolean);
            const idx = parts.indexOf('workspaces');
            if (idx !== -1 && parts.length > idx + 1) id = parts[idx + 1];
        }
        if (!id) return alert('Workspace ID tidak ditemukan.');

        const form = document.getElementById('invite-form');
        form.action = `/workspaces/${id}/members`;
        document.getElementById('modal-invite').classList.remove('hidden');
    }

    function closeInviteModal() {
        document.getElementById('modal-invite').classList.add('hidden');
        const form = document.getElementById('invite-form');
        form.action = '#';
        form.reset();
    }
</script>
@endpush