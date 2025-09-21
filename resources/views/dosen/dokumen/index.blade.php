<x-app-layout>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <a href="{{ route('dosen.dokumen.create') }}" class="btn btn-success mb-3">Upload File</a>
        <div class="document-structure">
            @include('dosen.dokumen.folder-tree', ['folders' => $foldersWithDocuments])
        </div>
    </div>
</x-app-layout>
