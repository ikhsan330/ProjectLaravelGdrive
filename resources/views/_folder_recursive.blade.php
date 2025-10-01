{{-- File: _folder_recursive.blade.php --}}
@foreach($folders as $folder)
    @php $hasChildren = !empty($groupedFolders[$folder->folder_id]); @endphp
<li class="space-y-1">
    <div class="folder-item flex items-center p-2 rounded-md hover:bg-gray-100 cursor-pointer" data-role="{{ $hasChildren ? 'toggle' : 'item' }}" data-folder-id="{{ $folder->id }}">
         <div class="w-4 h-4 mr-2 shrink-0">
            @if($hasChildren)
                <svg class="arrow-icon w-full h-full text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            @endif
        </div>
        <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
        <span class="text-gray-700 truncate">{{ $folder->name }}</span>
    </div>
    @if($hasChildren)
        <ul class="pl-6 border-l ml-3 hidden space-y-1">
            @include('_folder_recursive', ['folders' => $groupedFolders[$folder->folder_id], 'groupedFolders' => $groupedFolders])
        </ul>
    @endif
</li>
@endforeach
