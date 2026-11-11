<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Katalog Buku</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            {{-- Filter pencarian & kategori (query scopes) --}}
            <form method="GET" action="{{ route('books.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul buku..."
                       class="flex-1 rounded-md border-gray-300 shadow-sm">
                <button class="px-4 py-2 bg-gray-800 text-white rounded-md">Cari</button>
            </form>

            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse ($books as $book)
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-gray-900">{{ $book->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $book->author?->name }} — {{ $book->publisher?->name }}</p>
                        <p class="text-xs text-gray-500">ISBN: {{ $book->isbn->getFormatted() }}</p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach ($book->categories as $category)
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">{{ $category->name }}</span>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm">Stok: <strong>{{ $book->stock }}</strong></p>
                        @role('admin')
                            <form method="POST" action="{{ route('books.destroy', $book) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Soft delete</button>
                            </form>
                        @endrole
                    </div>
                @empty
                    <p class="text-gray-500">Tidak ada buku.</p>
                @endforelse
            </div>

            {{ $books->links() }}
        </div>
    </div>
</x-app-layout>
