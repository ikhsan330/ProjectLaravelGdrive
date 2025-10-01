{{-- File: _folder_contents.blade.php --}}
<h3 class="text-2xl font-bold text-gray-900 mb-4 pb-4 border-b">
    {{ $selectedFolder->name }}
    <span class="block text-sm font-normal text-gray-500 mt-1">Pemilik: {{ $selectedFolder->user->name }}</span>
</h3>

{{-- Bagian Subfolder --}}
@if($subfolders->isNotEmpty())
    <div class="mb-6">
        <h4 class="font-semibold text-gray-800 mb-2">Subfolder</h4>
        <ul class="space-y-2">
            @foreach($subfolders as $subfolder)
                <li class="flex items-center p-3 rounded-lg bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-yellow-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                    <span class="text-gray-800">{{ $subfolder->name }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Bagian Dokumen --}}
<div>
    <h4 class="font-semibold text-gray-800 mb-2">Dokumen</h4>
    @if($documents->isNotEmpty())
        <ul class="space-y-2">
            @foreach($documents as $document)
                <li class="flex items-center p-3 rounded-lg bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9 7.5l-4.5-4.5m0 0l-4.5 4.5m4.5-4.5v12.75" /></svg>
                    <span class="text-gray-800">{{ $document->name ?? $document->file_name }}</span>
                </li>
            @endforeach
        </ul>
    @else
        @if($subfolders->isEmpty())
             {{-- Hanya tampilkan jika tidak ada subfolder juga --}}
        @else
            <p class="text-gray-500 text-sm mt-2">Tidak ada dokumen di dalam folder ini.</p>
        @endif
    @endif
</div>

{{-- Kondisi jika folder benar-benar kosong --}}
@if($subfolders->isEmpty() && $documents->isEmpty())
    <div class="text-center text-gray-500 py-16">
        <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.012-1.244h3.86M2.25 13.5a2.25 2.25 0 000 4.5h19.5a2.25 2.25 0 000-4.5H2.25z" /></svg>
        <h4 class="mt-4 text-xl font-semibold text-gray-700">Folder Ini Kosong</h4>
        <p class="mt-1 text-gray-500">Tidak ada subfolder maupun dokumen yang ditemukan.</p>
    </div>
@endif
