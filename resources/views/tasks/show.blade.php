<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Komentar Tugas</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#F7F5F0;">

<div class="min-h-screen flex items-center justify-center px-6 py-8">
    <div class="w-full max-w-5xl rounded-[32px] p-6"
         style="background:linear-gradient(180deg,#FFFDF8 0%,#F7F5F0 100%); box-shadow:0 18px 50px rgba(0,0,0,0.08);">

        <div class="w-full bg-white rounded-[28px] shadow-xl overflow-hidden border border-gray-100">

            <div class="flex items-start justify-between px-8 py-6 border-b border-gray-200"
                 style="background:#F4ECDF;">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                        {{ $task->name ?? $task->title }}
                    </h1>

                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                        <span>📎 {{ $task->comments->whereNotNull('attachment_path')->count() }} files attached</span>
                        <span>•</span>
                        <span>{{ ucfirst($task->status ?? 'pending') }}</span>
                    </div>
                </div>

                <a href="{{ route('workspaces.show', $workspace_id) }}"
                   class="w-9 h-9 flex items-center justify-center rounded-full text-2xl text-gray-500 hover:bg-white hover:text-gray-900 transition">
                    ×
                </a>
            </div>

            <div id="comment-list"
                 class="h-[440px] overflow-y-auto px-8 py-7 space-y-5"
                 style="background:#FDF9F1;">

                @forelse($task->comments as $comment)
                    <div class="flex gap-3">
                        <img src="{{ $comment->user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name ?? 'User').'&size=40&background=C8216B&color=fff' }}"
                             class="w-10 h-10 rounded-full object-cover border border-white shadow-sm">

                        <div class="max-w-[75%]">
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-xs font-semibold text-gray-700">
                                    {{ $comment->user->name ?? 'User' }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $comment->created_at->format('H:i') }}
                                </span>
                            </div>

                            <div class="rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-gray-700 shadow-sm"
                                 style="background:#FCE8F1;">
                                <p class="leading-relaxed">{{ $comment->comment }}</p>

                                @if($comment->attachment_path)
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/' . $comment->attachment_path) }}"
                                          download
                                           class="inline-flex items-center gap-2 bg-white px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:text-pink-600">
                                            📄 Download Lampiran
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="empty-comment" class="h-full flex items-center justify-center text-sm text-gray-400">
                        Belum ada komentar.
                    </div>
                @endforelse
            </div>

            <form id="comment-form" enctype="multipart/form-data"
                  class="px-7 py-5 border-t border-gray-200 bg-white">
                @csrf
                <input type="hidden" name="collaborative_task_id" value="{{ $task->id }}">

                <div class="flex flex-col md:flex-row md:items-end gap-4">
                    <div class="flex-1">
                        <textarea name="comment" rows="2" required
                            placeholder="Ketikkan sesuatu ..."
                            class="w-full border border-gray-300 rounded-2xl px-5 py-4 text-sm resize-none focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-100"></textarea>

                        <input type="file" name="attachment"
                            class="mt-3 block text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-pink-50 file:text-pink-700 file:font-semibold">
                    </div>

                    <button type="submit"
                        class="px-8 py-4 rounded-xl text-white text-sm font-semibold hover:opacity-90 transition"
                        style="background:#8A0F5A;">
                        Send ➤
                    </button>
                </div>

                <p id="comment-status" class="hidden mt-3 text-xs font-semibold"></p>
            </form>

        </div>
    </div>
</div>

<script>
const form = document.getElementById('comment-form');
const textarea = form.querySelector('textarea[name="comment"]');
const commentList = document.getElementById('comment-list');

// Enter = kirim, Shift+Enter = baris baru
textarea.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();

        if (this.value.trim() !== '') {
            form.requestSubmit();
        }
    }
});

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    try {
        const response = await fetch("{{ route('task-comments.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                "Accept": "application/json"
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {

            // hapus tulisan "Belum ada komentar."
            const emptyComment = document.getElementById('empty-comment');
            if (emptyComment) {
                emptyComment.remove();
            }

            const comment = data.comment;

            let attachmentHtml = '';

            if (comment.attachment_path) {
                attachmentHtml = `
                    <div class="mt-3">
                        <a href="/storage/${comment.attachment_path}"
                        download
                        class="inline-flex items-center gap-2 bg-white px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:text-pink-600">
                            📄 Download Lampiran
                        </a>
                    </div>
                `;
            }

            const item = document.createElement('div');
            item.className = 'flex gap-3';

            item.innerHTML = `
                <img
                    src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user?.name ?? 'User')}&size=40&background=C8216B&color=fff"
                    class="w-10 h-10 rounded-full object-cover">

                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-xs font-semibold text-gray-700">
                            ${comment.user?.name ?? 'User'}
                        </span>

                        <span class="text-xs text-gray-400">
                            Baru saja
                        </span>
                    </div>

                    <div class="bg-pink-50 rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-gray-700 max-w-md">
                        ${comment.comment}

                        ${attachmentHtml}
                    </div>
                </div>
            `;

            commentList.appendChild(item);

            // scroll ke komentar terbaru
            commentList.scrollTop = commentList.scrollHeight;

            // reset form
            form.reset();

            // fokus kembali ke textarea
            textarea.focus();

        } else {
            alert(data.message || 'Komentar gagal dikirim.');
        }

    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan saat mengirim komentar.');
    }
});
</script>
</body>
</html>
