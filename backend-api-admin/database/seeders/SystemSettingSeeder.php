<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessmentScaleInfo = [
            'BB' => [
                'title' => 'Belum Berkembang (BB)',
                'description' => "1. Anak masih dalam bimbingan sehingga diberi contoh oleh pendidik.\n2. Anak belum menunjukkan kemampuan sesuai dengan indikator yang ditetapkan dalam kelompok usianya."
            ],
            'MB' => [
                'title' => 'Mulai Berkembang (MB)',
                'description' => "1. Anak masih harus diingatkan atau dibantu oleh pendidik.\n2. Anak sudah mulai menunjukkan kemampuan sesuai dengan indikator yang ditetapkan dalam kelompok usianya."
            ],
            'BSH' => [
                'title' => 'Berkembang Sesuai dengan Harapan (BSH)',
                'description' => "1. Anak sudah dapat melakukannya secara mandiri dan konsisten tanpa harus diingatkan atau dicontohkan pendidik.\n2. Anak sudah menunjukkan kemampuan sesuai dengan indikator yang ditetapkan dalam kelompok usianya."
            ],
            'BSB' => [
                'title' => 'Berkembang Sangat Baik (BSB)',
                'description' => "1. Anak sudah dapat melakukannya secara mandiri dan sudah dapat membantu temannya yang belum mencapai kemampuan sesuai dengan indikator yang diharapkan.\n2. Anak sudah menunjukkan kemampuan di atas indikator yang ditetapkan dalam kelompok usianya."
            ]
        ];

        SystemSetting::set('assessment_scale_info', $assessmentScaleInfo);
    }
}
