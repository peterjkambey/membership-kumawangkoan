<x-filament::page>
    <div class="space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
            Status tagihan per Kartu Keluarga — 13 bulan terakhir.
            Kolom <span class="bg-green-50 text-green-700 px-1 rounded">hijau</span> = lunas,
            <span class="bg-yellow-50 text-yellow-700 px-1 rounded">kuning</span> = belum bayar,
            <span class="bg-red-50 text-red-700 px-1 rounded">merah</span> = menunggak.
        </div>

        {{ $this->table }}
    </div>
</x-filament::page>
