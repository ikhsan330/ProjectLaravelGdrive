{{-- File: _document_list.blade.php --}}

{{-- Header yang lebih informatif --}}
<div class="mb-5 pb-4 border-b">
    <h3 class="text-2xl font-bold text-gray-900">{{ $selectedFolder->name }}</h3>
    @if($selectedFolder->user)
    <p class="text-sm text-gray-500 mt-1">
        Pemilik: <span class="font-semibold">{{ $selectedFolder->user->name }}</span>
        <span class="mx-2">&bull;</span>
        {{ $documents->count() }} Dokumen
    </p>
    @endif
</div>

@if($documents->isNotEmpty())
<ul class="space-y-3">
    {{-- PERBAIKAN: Perulangan harus menggunakan variabel $documents --}}
    @foreach($documents as $document)
        @php
            $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
            $icon_path = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9 7.5l-4.5-4.5m0 0l-4.5 4.5m4.5-4.5v12.75';
            $icon_color = 'text-gray-400';
            if (in_array($extension, ['pdf'])) {
                $icon_path = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
                $icon_color = 'text-red-500';
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $icon_path = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
                $icon_color = 'text-blue-500';
            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                $icon_path = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 0h-4.5m4.5 0l-4.5 4.5M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M9 17.25h6M12 14.25v3';
                $icon_color = 'text-green-500';
            }
        @endphp
    <li>
        <a href="#" class="flex items-center p-4 rounded-xl hover:bg-gray-50 transition-colors duration-200 border group">
            <svg class="w-10 h-10 mr-4 shrink-0 {{ $icon_color }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon_path }}" /></svg>
            <div class="flex-grow">
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">{{ $document->name ?? 'Tanpa Judul' }}</p>
                <div class="flex items-center text-sm text-gray-500 mt-1">
                    <span>{{ $document->file_name }}</span>
                    <span class="mx-2">&bull;</span>
                    <span>{{ \Carbon\Carbon::parse($document->created_at)->format('d M Y') }}</span>
                </div>
            </div>
            <span class="text-sm text-gray-400 group-hover:text-blue-600 group-hover:font-semibold flex items-center gap-2">
                Download
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z" /><path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" /></svg>
            </span>
        </a>
    </li>
    @endforeach
</ul>
@else
<div class="text-center text-gray-500 py-16">
    <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008z" /></svg>
    <h4 class="mt-4 text-xl font-semibold text-gray-700">Folder Kosong</h4>
    <p class="mt-1 text-gray-500">Tidak ada dokumen yang ditemukan di dalam folder ini.</p>
</div>
@endif
