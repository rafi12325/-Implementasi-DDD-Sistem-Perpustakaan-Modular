<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman Buku</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex gap-2">
                <a href="{{ route('loans.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">+ Peminjaman Baru</a>
                <a href="{{ route('loans.trashed') }}" class="px-4 py-2 bg-gray-200 rounded-md">Sampah</a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">No. Peminjaman</th>
                            <th class="p-3">Anggota</th>
                            <th class="p-3">Buku</th>
                            <th class="p-3">Jatuh Tempo</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Denda</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr class="border-t">
                                <td class="p-3">{{ $loan->loan_number }}</td>
                                <td class="p-3">{{ $loan->member?->user?->name }} ({{ $loan->member?->member_number }})</td>
                                <td class="p-3">
                                    @foreach ($loan->items as $item)
                                        {{ $item->book_title }} x{{ $item->quantity }}<br>
                                    @endforeach
                                </td>
                                <td class="p-3">{{ $loan->due_at?->format('d M Y') }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $loan->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td class="p-3">Rp {{ number_format($loan->fines->sum('amount'), 0, ',', '.') }}</td>
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:underline">Detail</a>
                                    @if ($loan->status === 'borrowed')
                                        <form method="POST" action="{{ route('loans.return', $loan) }}" class="inline">
                                            @csrf
                                            <button class="text-green-600 hover:underline">Kembalikan</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('loans.destroy', $loan) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $loans->links() }}
        </div>
    </div>
</x-app-layout>
