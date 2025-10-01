{{-- File: _folder_sidebar.blade.php --}}
@forelse ($groupedRootFolders as $folderName => $folders)
<li class="space-y-1">
    {{-- LEVEL 1: NAMA GRUP FOLDER --}}
    <div class="folder-item flex items-center p-2 rounded-md hover:bg-gray-100 cursor-pointer" data-role="toggle">
        <svg class="arrow-icon w-4 h-4 mr-2 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span class="font-bold text-gray-800 truncate">{{ $folderName }}</span>
    </div>

    {{-- LEVEL 2: DAFTAR DOSEN --}}
    <ul class="pl-7 border-l-2 ml-3 hidden space-y-1">
        @foreach($folders as $folder)
            @php $hasChildren = !empty($groupedFolders[$folder->folder_id]); @endphp
        <li>
            <div class="folder-item flex items-center p-2 rounded-md hover:bg-gray-100 cursor-pointer" data-role="{{ $hasChildren ? 'toggle' : 'item' }}" data-folder-id="{{ $folder->id }}">
                <div class="w-4 h-4 mr-2 shrink-0">
                    @if($hasChildren)
                        <svg class="arrow-icon w-full h-full text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    @endif
                </div>
                <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                <span class="text-gray-700 truncate">{{ $folder->user_name }}</span>
            </div>

            @if($hasChildren)
                {{-- LEVEL 3: SUBFOLDER DOSEN --}}
                <ul class="pl-6 border-l ml-3 hidden space-y-1">
                    @include('_folder_recursive', ['folders' => $groupedFolders[$folder->folder_id], 'groupedFolders' => $groupedFolders])
                </ul>
            @endif
        </li>
        @endforeach
    </ul>
</li>
@empty
<li class="p-2 text-gray-500">Tidak ada direktori folder yang dibuat.</li>
@endforelse
