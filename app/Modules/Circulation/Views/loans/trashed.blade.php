<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sampah Peminjaman (Soft Deletes)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('loans.index') }}" class="text-blue-600 hover:underline">&larr; Kembali</a>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">No. Peminjaman</th>
                            <th class="p-3">Anggota</th>
                            <th class="p-3">Dihapus</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            <tr class="border-t">
                                <td class="p-3">{{ $loan->loan_number }}</td>
                                <td class="p-3">{{ $loan->member?->user?->name }}</td>
                                <td class="p-3">{{ $loan->deleted_at?->format('d M Y H:i') }}</td>
                                <td class="p-3">
                                    <form method="POST" action="{{ route('loans.restore', $loan->id) }}">
                                        @csrf
                                        <button class="text-green-600 hover:underline">Restore</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-3 text-gray-500">Sampah kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
