<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task Comments</title>

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
                                            📄 Download Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="empty-comment" class="h-full flex items-center justify-center text-sm text-gray-400">
                        No comments yet.
                    </div>
                @endforelse
            </div>

            <form id="comment-form" enctype="multipart/form-data"
                  class="px-7 py-5 border-t border-gray-200 bg-white">
                @csrf
                <input type="hidden" name="collaborative_task_id" value="{{ $task->id }}">

                <div class="flex flex-col md:flex-row md:items-end gap-4">
                    <div class="flex-1">
                        <div id="textarea-wrapper"
                             class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus-within:border-pink-500 focus-within:ring-2 focus-within:ring-pink-100 transition">

                            <div id="file-preview" class="hidden items-center gap-2 mb-2 bg-pink-50 border border-pink-100 rounded-xl px-3 py-2">
                                <span class="text-sm">📎</span>
                                <span id="file-preview-name" class="text-xs font-medium text-gray-700 truncate flex-1"></span>
                                <button type="button" id="remove-file-btn"
                                        class="text-gray-400 hover:text-pink-600 text-sm font-bold leading-none px-1">
                                    ×
                                </button>
                            </div>

                            <textarea name="comment" rows="2"
                                placeholder="Type something ..."
                                class="w-full text-sm resize-none focus:outline-none border-0 p-0"></textarea>
                        </div>

                        <label class="mt-3 inline-flex items-center gap-2 text-xs text-pink-700 font-semibold bg-pink-50 px-4 py-2 rounded-xl cursor-pointer hover:bg-pink-100 transition">
                            📎 Choose File
                            <input type="file" name="attachment" id="attachment-input" class="hidden">
                        </label>
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
const fileInput = document.getElementById('attachment-input');
const commentList = document.getElementById('comment-list');
const filePreview = document.getElementById('file-preview');
const filePreviewName = document.getElementById('file-preview-name');
const removeFileBtn = document.getElementById('remove-file-btn');

function showFilePreview(file) {
    filePreviewName.textContent = file.name;
    filePreview.classList.remove('hidden');
    filePreview.classList.add('flex');
}

function clearFilePreview() {
    fileInput.value = '';
    filePreview.classList.add('hidden');
    filePreview.classList.remove('flex');
    filePreviewName.textContent = '';
}

fileInput.addEventListener('change', function () {
    if (this.files.length > 0) {
        showFilePreview(this.files[0]);
    } else {
        clearFilePreview();
    }
    textarea.focus();
});

removeFileBtn.addEventListener('click', function () {
    clearFilePreview();
});

textarea.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();

        if (this.value.trim() !== '' || fileInput.files.length > 0) {
            form.requestSubmit();
        }
    }
});

form.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (textarea.value.trim() === '' && fileInput.files.length === 0) {
        return;
    }

    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const statusText = document.getElementById('comment-status');

    submitButton.disabled = true;
    statusText.className = 'mt-3 text-xs font-semibold text-gray-400';
    statusText.textContent = 'Sending comment...';
    statusText.classList.remove('hidden');

    try {
        const response = await fetch("{{ route('task-comments.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Accept": "application/json"
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error('Failed to send comment');
        }

        const emptyComment = document.getElementById('empty-comment');
        if (emptyComment) emptyComment.remove();

        const comment = data.comment;

        const commentText = comment.comment
            ? `<p class="leading-relaxed">${comment.comment}</p>`
            : `<p class="leading-relaxed text-gray-500 italic">Sending attachment.</p>`;

        const attachmentHtml = comment.attachment_path
            ? `
                <div class="mt-3">
                    <a href="/storage/${comment.attachment_path}"
                       download
                       class="inline-flex items-center gap-2 bg-white px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:text-pink-600">
                        📄 Download Attachment
                    </a>
                </div>
              `
            : '';

        const item = document.createElement('div');
        item.className = 'flex gap-3';
        item.innerHTML = `
            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user?.name ?? 'User')}&size=40&background=C8216B&color=fff"
                 class="w-10 h-10 rounded-full object-cover border border-white shadow-sm">

            <div class="max-w-[75%]">
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-xs font-semibold text-gray-700">${comment.user?.name ?? 'User'}</span>
                    <span class="text-xs text-gray-400">Just now</span>
                </div>

                <div class="rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-gray-700 shadow-sm"
                     style="background:#FCE8F1;">
                    ${commentText}
                    ${attachmentHtml}
                </div>
            </div>
        `;

        commentList.appendChild(item);
        commentList.scrollTop = commentList.scrollHeight;

        form.reset();
        clearFilePreview();
        textarea.focus();

        statusText.className = 'mt-3 text-xs font-semibold text-green-600';
        statusText.textContent = 'Comment sent successfully.';
    } catch (error) {
        console.error(error);
        statusText.className = 'mt-3 text-xs font-semibold text-red-600';
        statusText.textContent = 'Failed to send comment. Try again.';
    } finally {
        submitButton.disabled = false;
    }
});
</script>
</body>
</html>
