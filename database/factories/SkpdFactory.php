<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skpd>
 */
class SkpdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'nama' => fake()->randomElement([
                'Dinas Pendidikan',
                'Dinas Kesehatan',
                'Dinas Pekerjaan Umum',
                'Dinas Perhubungan',
                'Dinas Sosial',
                'Dinas Kependudukan dan Pencatatan Sipil',
                'Dinas Pemuda dan Olahraga',
                'Dinas Pariwisata',
                'Dinas Komunikasi dan Informatika',
                'Badan Perencanaan Pembangunan Daerah',
            ]),
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }
}
