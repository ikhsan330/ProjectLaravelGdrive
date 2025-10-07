{{-- File: resources/views/partials/_folder_sidebar.blade.php --}}

@forelse ($rootFolders as $folder)
    {{-- Memanggil partial rekursif untuk setiap folder induk --}}
    @include('partials._folder_recursive', ['folder' => $folder])
@empty
    <li class="p-2 text-gray-500 text-sm">Tidak ada direktori folder yang tersedia.</li>
@endforelse
