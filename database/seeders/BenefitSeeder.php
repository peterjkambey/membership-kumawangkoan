<?php

namespace Database\Seeders;

use App\Models\Benefit;
use Illuminate\Database\Seeder;

class BenefitSeeder extends Seeder
{
    public function run(): void
    {
        $benefits = [
            ['code' => 'ETOLL_CARD', 'name' => 'Kartu Anggota E-Toll', 'description' => 'Kartu anggota berbasis e-toll untuk transaksi dan identitas anggota.', 'icon' => 'heroicon-o-identification', 'sort_order' => 1],
            ['code' => 'SOCIAL_AID', 'name' => 'Bantuan Sosial', 'description' => 'Bantuan sosial dari perkumpulan untuk anggota yang membutuhkan.', 'icon' => 'heroicon-o-hand-raised', 'sort_order' => 2],
            ['code' => 'BASIC_FOOD', 'name' => 'Paket Sembako', 'description' => 'Bantuan paket sembako berkala untuk anggota.', 'icon' => 'heroicon-o-truck', 'sort_order' => 3],
            ['code' => 'VOTING_RIGHT', 'name' => 'Hak Suara', 'description' => 'Hak suara dalam musyawarah tahunan dan pemilihan pengurus.', 'icon' => 'heroicon-o-star', 'sort_order' => 4],
            ['code' => 'FUNERAL_AID', 'name' => 'Santunan Duka', 'description' => 'Santunan untuk anggota atau keluarga inti yang meninggal dunia.', 'icon' => 'heroicon-o-heart', 'sort_order' => 5],
            ['code' => 'SCHOLARSHIP', 'name' => 'Beasiswa Pendidikan', 'description' => 'Bantuan pendidikan untuk anak-anak anggota yang berprestasi.', 'icon' => 'heroicon-o-academic-cap', 'sort_order' => 6],
            ['code' => 'LOAN_ACCESS', 'name' => 'Pinjaman Khusus', 'description' => 'Akses pinjaman dari usaha dana Kumawangkoan.', 'icon' => 'heroicon-o-banknotes', 'sort_order' => 7],
            ['code' => 'EVENT_ACCESS', 'name' => 'Akses Acara Khusus', 'description' => 'Undangan dan akses ke acara tahunan dan kegiatan khusus perkumpulan.', 'icon' => 'heroicon-o-trophy', 'sort_order' => 8],
        ];

        foreach ($benefits as $data) {
            Benefit::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
