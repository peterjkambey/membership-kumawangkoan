<x-filament::page>
    {{-- Welcome Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Welcome Card --}}
        <div class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a1a2e] via-[#16213e] to-[#0f3460] p-8 shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-[#d4a853]/20 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-[#2d6a6b]/20 to-transparent rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl font-bold text-white font-['Playfair_Display']">
                    Selamat Datang, {{ filament()->auth()->user()->name }} 👋
                </h1>
                <p class="mt-2 text-white/60 text-sm max-w-lg">
                    Kelola data keanggotaan, iuran, dan informasi organisasi Perkumpulan Kumawangkoan dalam satu dashboard terpadu.
                </p>
                <div class="flex gap-4 mt-6">
                    <div class="flex items-center gap-2 text-white/80 text-sm">
                        <x-heroicon-o-user-group class="w-4 h-4 text-[#d4a853]" />
                        <span>{{ \App\Models\Member::count() }} Anggota</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/80 text-sm">
                        <x-heroicon-o-identification class="w-4 h-4 text-[#d4a853]" />
                        <span>{{ \App\Models\FamilyCard::count() }} KK</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/80 text-sm">
                        <x-heroicon-o-map-pin class="w-4 h-4 text-[#d4a853]" />
                        <span>{{ \App\Models\Region::count() }} Wilayah</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ \App\Filament\Resources\MemberResource::getUrl('create') }}"
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#faf6f0] transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-[#d4a853]/10 flex items-center justify-center group-hover:bg-[#d4a853]/20 transition-colors">
                        <x-heroicon-o-user-plus class="w-5 h-5 text-[#d4a853]" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Tambah Anggota</p>
                        <p class="text-xs text-gray-400">Registrasi anggota baru</p>
                    </div>
                </a>
                <a href="{{ \App\Filament\Resources\FamilyCardResource::getUrl('create') }}"
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#faf6f0] transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-[#2d6a6b]/10 flex items-center justify-center group-hover:bg-[#2d6a6b]/20 transition-colors">
                        <x-heroicon-o-document-plus class="w-5 h-5 text-[#2d6a6b]" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Buat KK Baru</p>
                        <p class="text-xs text-gray-400">Kartu Keluarga baru</p>
                    </div>
                </a>
                <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('create') }}"
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#faf6f0] transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-[#c75b5b]/10 flex items-center justify-center group-hover:bg-[#c75b5b]/20 transition-colors">
                        <x-heroicon-o-credit-card class="w-5 h-5 text-[#c75b5b]" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Catat Pembayaran</p>
                        <p class="text-xs text-gray-400">Input iuran anggota</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 font-['Playfair_Display']">Ringkasan Organisasi</h2>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @php
                $stats = [
                    ['label' => 'Total Anggota', 'value' => number_format(\App\Models\Member::count()), 'icon' => 'heroicon-o-user-group', 'color' => 'from-[#d4a853] to-[#b8913a]', 'bg' => 'bg-[#d4a853]/10'],
                    ['label' => 'Aktif', 'value' => number_format(\App\Models\Member::where('status', 'active')->count()), 'icon' => 'heroicon-o-check-circle', 'color' => 'from-emerald-500 to-emerald-600', 'bg' => 'bg-emerald-50'],
                    ['label' => 'Total KK', 'value' => number_format(\App\Models\FamilyCard::count()), 'icon' => 'heroicon-o-identification', 'color' => 'from-[#2d6a6b] to-[#3d8a8b]', 'bg' => 'bg-[#2d6a6b]/10'],
                    ['label' => 'Wilayah', 'value' => number_format(\App\Models\Region::count()), 'icon' => 'heroicon-o-map-pin', 'color' => 'from-violet-500 to-violet-600', 'bg' => 'bg-violet-50'],
                    ['label' => 'Badan Pembantu', 'value' => number_format(\App\Models\SupportBody::count()), 'icon' => 'heroicon-o-users', 'color' => 'from-rose-500 to-rose-600', 'bg' => 'bg-rose-50'],
                ];
            @endphp
            @foreach($stats as $stat)
                <div class="relative overflow-hidden rounded-xl bg-white border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 group cursor-default">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1 font-['Playfair_Display']">{{ $stat['value'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $stat['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-dynamic-component :component="$stat['icon']" class="w-5 h-5" style="color: {{ str_contains($stat['color'], 'd4a853') ? '#d4a853' : (str_contains($stat['color'], '2d6a6b') ? '#2d6a6b' : '') }}" />
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r {{ $stat['color'] }} scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Filament Default Widgets --}}
    <div class="space-y-6">
        @php
            $widgets = \Filament\Facades\Filament::getWidgets();
        @endphp
        @foreach($widgets as $widget)
            @if($widget !== \Filament\Widgets\AccountWidget::class)
                <livewire:dynamic-component :component="$widget" />
            @endif
        @endforeach
    </div>
</x-filament::page>
