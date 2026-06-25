<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Membership Card - {{ $member->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Kartu -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-700 to-green-500 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-lg font-bold">Kumawangkoan</h1>
                        <p class="text-xs text-green-100">E-Membership Card</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-green-100">No. Anggota</p>
                        <p class="font-mono text-sm font-bold">{{ $member->membership_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <div class="flex items-center gap-4 mb-4">
                    <!-- Foto -->
                    <div class="w-20 h-20 rounded-full bg-gray-200 overflow-hidden flex-shrink-0 border-2 border-green-500">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-3xl font-bold">
                                {{ strtoupper(substr($member->full_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-bold text-gray-800 truncate">{{ $member->full_name }}</h2>
                        <p class="text-sm text-gray-500">{{ $member->region?->name ?? '-' }}</p>
                        <div class="mt-1">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                @if($member->status === 'active') bg-green-100 text-green-700
                                @elseif($member->status === 'inactive') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif">
                                {{ $member->status === 'active' ? 'AKTIF' : ($member->status === 'inactive' ? 'TIDAK AKTIF' : 'MENINGGAL') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detail -->
                <div class="border-t border-gray-100 pt-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kartu Keluarga</span>
                        <span class="font-medium text-gray-800">{{ $member->familyCard?->family_no ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Peran</span>
                        <span class="font-medium text-gray-800">{{ $member->family_role_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Wilayah</span>
                        <span class="font-medium text-gray-800">{{ $member->region?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bergabung</span>
                        <span class="font-medium text-gray-800">{{ $member->join_date?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    @if($member->activeMembership && $member->activeMembership->end_date)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Masa Berlaku</span>
                        <span class="font-medium text-gray-800">{{ $member->activeMembership->end_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Footer with QR -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                <div class="text-xs text-gray-400">
                    <p>Perkumpulan Kumawangkoan</p>
                    <p>Pindai QR untuk verifikasi</p>
                </div>
                <div class="w-16 h-16 bg-white rounded-lg p-1 shadow">
                    {!! \App\Helpers\QRCodeHelper::generateSvg(url('/card/' . $member->id), 80) !!}
                </div>
            </div>
        </div>

        <!-- Tombol Download -->
        <div class="mt-4 text-center">
            <button onclick="window.print()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                Cetak / Download PDF
            </button>
        </div>
    </div>
</body>
</html>
