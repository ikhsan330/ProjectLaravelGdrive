{{-- File: resources/views/partials/_folder_contents.blade.php --}}

{{-- Header Konten Folder --}}
<div class="pb-4 mb-6 border-b border-gray-200">
    <h3 class="text-2xl font-bold text-gray-900">{{ $selectedFolder->name }}</h3>
    <p class="text-sm text-gray-500 mt-1">
        {{ $subfolders->count() }} Subfolder
        <span class="mx-2">&bull;</span>
        {{ $documents->count() }} Dokumen
    </p>
</div>

{{-- Daftar Subfolder --}}
@if($subfolders->isNotEmpty())
    <div class="mb-8">
        <h4 class="font-semibold text-gray-800 mb-3">Subfolder</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($subfolders as $subfolder)
                <div class="folder-item flex items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 transition cursor-pointer"
                   data-role="item" data-folder-id="{{ $subfolder->id }}">
                    <svg class="w-5 h-5 mr-3 text-yellow-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                    <span class="text-gray-800 font-medium truncate">{{ $subfolder->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Daftar Dokumen --}}
<div>
    <h4 class="font-semibold text-gray-800 mb-3">Dokumen</h4>
    @if($documents->isNotEmpty())
        <ul class="space-y-3">
            @foreach($documents as $document)
                @php
                    // Logika untuk menentukan ikon berdasarkan ekstensi file
                    $extension = strtolower(pathinfo($document->name, PATHINFO_EXTENSION));
                    $icon_path = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9 7.5l-4.5-4.5m0 0l-4.5 4.5m4.5-4.5v12.75';
                    $icon_color = 'text-gray-400';
                    if ($extension == 'pdf') {
                        $icon_color = 'text-red-500';
                    } elseif (in_array($extension, ['doc', 'docx'])) {
                        $icon_color = 'text-blue-500';
                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                        $icon_color = 'text-green-500';
                    }
                @endphp
                <li>
                        <svg class="w-8 h-8 mr-4 flex-shrink-0 {{ $icon_color }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>

                        <div class="flex-grow">
                            <p class="font-medium text-gray-800 group-hover:text-blue-600">{{ $document->file_name }}</p>
                            <div class="flex items-center text-sm text-gray-500 mt-1">
                                <span>{{ $document->name }}</span>
                                @if($document->user)
                                    <span class="mx-2">&bull;</span>
                                    <span>Oleh: {{ $document->user->name }}</span>
                                @endif
                                <span class="mx-2">&bull;</span>
                                <span>{{ $document->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="flex-shrink-0 mx-4">
                            @if($document->verified)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.35 2.35 4.493-6.74a.75.75 0 0 1 1.04-.208Z" clip-rule="evenodd" /></svg>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10.25a.75.75 0 0 0-1.5 0v4.5c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75v-3.75Z" clip-rule="evenodd" /></svg>
                                    Belum Diverifikasi
                                </span>
                            @endif
                        </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center text-gray-500 py-16">
            <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008z" /></svg>
            <h4 class="mt-4 text-xl font-semibold text-gray-700">Tidak ada dokumen</h4>
            <p class="mt-1 text-gray-500">Folder ini belum memiliki dokumen.</p>
        </div>
    @endif
</div>
