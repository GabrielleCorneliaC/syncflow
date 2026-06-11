<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Workspace Tasks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    
    <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8 mt-4 sm:mt-8">
        
        <div class="mb-8 bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Daftar Tugas Kolaborasi</h2>
                <p class="text-blue-100 text-sm sm:text-base max-w-xl">Kelola, pantau, dan delegasikan tugas untuk workspace ini dengan mudah dan cepat.</p>
            </div>
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
        </div>

        <form action="/workspaces/{{ $workspace_id }}/tasks" method="POST" class="mb-10 bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
            @csrf
            
            <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Buat Tugas Baru
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Tugas</label>
                    <input type="text" name="title" required placeholder="Misal: Revisi Desain Homepage" 
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all px-4 py-2.5">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Delegasi (Assignee)</label>
                    <select name="assignee_id" 
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all px-4 py-2.5 cursor-pointer">
                        <option value="">-- Pilih Anggota Tim --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Detail</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan detail tugas ini agar tim mudah memahaminya..." 
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all px-4 py-3"></textarea>
                </div>

                <div class="md:col-span-2 sm:col-span-1 md:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Waktu (Due Date)</label>
                    <input type="datetime-local" name="due_date" 
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all px-4 py-2.5 cursor-pointer">
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Tambahkan ke Daftar
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>

        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Tugas Saat Ini
            </h3>
            
            <div class="space-y-4">
                @forelse($tasks as $task)
                    <div class="group flex flex-col md:flex-row md:items-center justify-between p-5 sm:p-6 bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 gap-4">
                        
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ $task->description ?: 'Tidak ada deskripsi yang ditambahkan.' }}</p>
                            
                            <div class="flex flex-wrap items-center gap-3 mt-4">
                                <span class="inline-flex items-center text-xs font-semibold text-gray-600 bg-gray-100/80 border border-gray-200 px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $task->assignee ? $task->assignee->name : 'Belum ditugaskan' }}
                                </span>
                                
                                @if($task->due_date)
                                <span class="inline-flex items-center text-xs font-semibold {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed' ? 'text-red-600 bg-red-50 border-red-200' : 'text-orange-600 bg-orange-50 border-orange-200' }} border px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-row md:flex-col items-center justify-between md:justify-end gap-3 w-full md:w-48 mt-2 md:mt-0 pt-4 md:pt-0 border-t md:border-0 border-gray-100">
                            
                            @php
                                $statusColor = match($task->status) {
                                    'completed' => 'bg-green-50 text-green-700 border-green-200 focus:ring-green-500',
                                    'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200 focus:ring-blue-500',
                                    'overdue' => 'bg-red-50 text-red-700 border-red-200 focus:ring-red-500',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200 focus:ring-gray-500',
                                };
                            @endphp

                            <select id="status-{{ $task->id }}" onchange="updateTaskStatus({{ $task->id }}, this.value)" 
                                    class="w-full cursor-pointer rounded-xl text-sm font-bold border shadow-sm transition-all focus:ring-4 {{ $statusColor }} px-4 py-2.5 appearance-none text-center">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>🚀 In Progress</option>
                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                <option value="overdue" {{ $task->status == 'overdue' ? 'selected' : '' }}>⚠️ Overdue</option>
                            </select>

                            <span id="loading-{{ $task->id }}" class="hidden text-xs font-semibold text-blue-600 animate-pulse bg-blue-50 px-2 py-1 rounded-md">Menyimpan...</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white border border-dashed border-gray-300 rounded-2xl shadow-sm">
                        <div class="mx-auto h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-gray-900">Workspace Masih Kosong</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">Belum ada tugas yang dibuat. Mulai delegasikan pekerjaan kepada tim dengan mengisi form di atas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

<script>
function updateTaskStatus(taskId, newStatus) {
    const loadingText = document.getElementById(`loading-${taskId}`);
    const selectEl = document.getElementById(`status-${taskId}`);
    
    loadingText.classList.remove('hidden');
    selectEl.disabled = true;

    // Pastikan meta tag csrf-token ada di <head> layout utamamu ya!
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    fetch(`/workspaces/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        loadingText.classList.add('hidden');
        selectEl.disabled = false;
        
        if (data.success || data.status) { // Sesuaikan dengan response format dari controllermu
            updateDropdownColor(selectEl, newStatus);
        } else {
            alert('Gagal mengupdate status tugas.');
        }
    })
    .catch(error => {
        console.error('Terjadi kesalahan AJAX:', error);
        loadingText.classList.add('hidden');
        selectEl.disabled = false;
        alert('Terjadi kesalahan server saat menyimpan status.');
    });
}

function updateDropdownColor(element, status) {
    // Reset classes
    element.className = element.className.replace(/\bbg-\w+-50\b/g, '');
    element.className = element.className.replace(/\btext-\w+-700\b/g, '');
    element.className = element.className.replace(/\bborder-\w+-200\b/g, '');
    element.className = element.className.replace(/\bfocus:ring-\w+-500\b/g, '');

    // Add new classes
    switch(status) {
        case 'completed':
            element.classList.add('bg-green-50', 'text-green-700', 'border-green-200', 'focus:ring-green-500');
            break;
        case 'in_progress':
            element.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200', 'focus:ring-blue-500');
            break;
        case 'overdue':
            element.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'focus:ring-red-500');
            break;
        default: // pending
            element.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-200', 'focus:ring-gray-500');
            break;
    }
}
</script>
    
</body>
</html>