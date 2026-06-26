@extends('layouts.app')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')


@section('content')
<div class="sf-card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h2 class="font-heading font-bold text-base text-textmain">Semua User Terdaftar</h2>
            <p class="text-xs text-texthint">Total: {{ $users->total() }} user</p>
        </div>
    </div>

    {{-- Tabel (desktop) --}}
    <div class="hidden sm:block overflow-x-auto rounded-xl border border-border">
        <table class="sf-table">
            <thead class="bg-muted">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Bergabung</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="text-texthint">{{ $loop->iteration }}</td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                                 class="w-8 h-8 rounded-xl object-cover ring-2 ring-border">
                            <span class="font-medium text-textmain">{{ $u->name }}</span>
                            @if($u->id === Auth::id())
                                <span class="sf-badge sf-badge-primary text-[10px]">Kamu</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @if($u->isAdmin())
                            <span class="sf-badge sf-badge-primary">Admin</span>
                        @else
                            <span class="sf-badge sf-badge-neutral">Member</span>
                        @endif
                    </td>
                    <td class="text-texthint">{{ $u->created_at->format('d M Y') }}</td>
                    <td class="text-right">
                        @if($u->id !== Auth::id())
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                              onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="sf-btn sf-btn-danger text-xs px-3 py-1.5">
                                Hapus
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-texthint italic">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-texthint">
                        Belum ada user terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Kartu (mobile) --}}
    <div class="sm:hidden space-y-3">
        @forelse($users as $u)
        <div class="flex items-center gap-3 p-3 border border-border rounded-xl">
            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                 class="w-10 h-10 rounded-xl object-cover shrink-0">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-sm text-textmain truncate">{{ $u->name }}</p>
                <p class="text-xs text-texthint truncate">{{ $u->email }}</p>
            </div>
            @if($u->isAdmin())
                <span class="sf-badge sf-badge-primary text-[10px]">Admin</span>
            @else
                <span class="sf-badge sf-badge-neutral text-[10px]">Member</span>
            @endif
        </div>
        @empty
        <p class="text-center py-8 text-texthint text-sm">Belum ada user.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="mt-5 flex justify-center">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection

<style>
    nav[aria-label="Pagination Navigation"] span,
    nav[aria-label="Pagination Navigation"] a {
        @apply inline-flex items-center px-3 py-1.5 text-sm rounded-lg border border-border mx-0.5 transition;
    }
    nav[aria-label="Pagination Navigation"] a:hover {
        @apply bg-muted;
    }
    nav[aria-label="Pagination Navigation"] [aria-current="page"] span {
        @apply bg-primary text-white border-primary;
    }
</style>
