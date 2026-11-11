<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="p-3 mb-4 bg-red-100 text-red-800 rounded">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('loans.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Anggota</label>
                    <select name="member_id" required class="mt-1 w-full rounded-md border-gray-300">
                        <option value="">-- Pilih anggota aktif --</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->member_number }} — {{ $member->user?->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Buku (pilih satu atau lebih)</label>
                    <div class="mt-2 max-h-72 overflow-y-auto border rounded divide-y">
                        @foreach ($books as $book)
                            <label class="flex items-center gap-3 p-2">
                                <input type="checkbox" name="items[{{ $book->id }}][checked]" value="1"
                                       onchange="this.closest('label').querySelector('[type=number]').disabled = !this.checked">
                                <span class="flex-1">
                                    {{ $book->title }}
                                    <span class="text-xs text-gray-500">({{ $book->author?->name }}, stok: {{ $book->stock }})</span>
                                </span>
                                <input type="hidden" name="items[{{ $book->id }}][book_id]" value="{{ $book->id }}">
                                <input type="number" name="items[{{ $book->id }}][quantity]" value="1" min="1" max="5"
                                       disabled class="w-16 rounded-md border-gray-300">
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Lama pinjam (hari)</label>
                    <input type="number" name="loan_days" value="7" min="1" max="30" class="mt-1 w-32 rounded-md border-gray-300">
                </div>

                <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan Peminjaman</button>
            </form>
        </div>
    </div>
</x-app-layout>
