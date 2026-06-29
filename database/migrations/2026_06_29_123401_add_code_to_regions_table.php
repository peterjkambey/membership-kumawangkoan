<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->string('code', 10)->unique()->nullable()->after('id');
        });

        // Isi code untuk data yang sudah ada di production
        DB::table('regions')->where('name', 'Wilayah 1 - Tompola')->update(['code' => 'TMP']);
        DB::table('regions')->where('name', 'Wilayah 2 - Mawale')->update(['code' => 'MWL']);
        DB::table('regions')->where('name', 'Wilayah 3 - Tombara\'an')->update(['code' => 'TMB']);
        DB::table('regions')->where('name', 'Wilayah 4 - Lewetan')->update(['code' => 'LWT']);
        DB::table('regions')->where('name', 'Wilayah 5 - Wawona')->update(['code' => 'WWN']);
        DB::table('regions')->where('name', 'Wilayah 6 - Ranowangko')->update(['code' => 'RNW']);
        DB::table('regions')->where('name', 'Wilayah 7 - KuntungMu\'ukur')->update(['code' => 'KNT']);
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
