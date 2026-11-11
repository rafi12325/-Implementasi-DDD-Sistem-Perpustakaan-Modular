<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Peminjaman</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow p-6 space-y-2 text-sm">
                <p><strong>No. Peminjaman:</strong> {{ $loan->loan_number }}</p>
                <p><strong>Anggota:</strong> {{ $loan->member?->user?->name }} ({{ $loan->member?->member_number }})</p>
                <p><strong>Status:</strong> {{ $loan->status }}</p>
                <p><strong>Dipinjam:</strong> {{ $loan->borrowed_at?->format('d M Y H:i') }}</p>
                <p><strong>Jatuh Tempo:</strong> {{ $loan->due_at?->format('d M Y H:i') }}</p>
                <p><strong>Dikembalikan:</strong> {{ $loan->returned_at?->format('d M Y H:i') ?? '-' }}</p>

                <h3 class="font-semibold pt-4">Detail Buku (snapshot)</h3>
                <ul class="list-disc pl-5">
                    @foreach ($loan->items as $item)
                        <li>{{ $item->book_title }} — ISBN: {{ $item->book_isbn }} x{{ $item->quantity }}
                            @if ($item->returned_at) <span class="text-green-600">(dikembalikan)</span> @endif
                        </li>
                    @endforeach
                </ul>

                <h3 class="font-semibold pt-4">Denda</h3>
                @forelse ($loan->fines as $fine)
                    <p>Rp {{ number_format($fine->amount, 0, ',', '.') }} ({{ $fine->days_late }} hari x Rp {{ number_format($fine->daily_rate, 0, ',', '.') }})</p>
                @empty
                    <p class="text-gray-500">Tidak ada denda.</p>
                @endforelse

                @if ($loan->status === 'borrowed')
                    <form method="POST" action="{{ route('loans.return', $loan) }}" class="pt-4">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded-md">Kembalikan Buku</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
