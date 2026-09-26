<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TabelStatus>
 */
class TabelStatusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_id' => Report::factory(),
            'user_id' => User::factory()->admin(),
            'status' => 'diterima',
            'keterangan' => fake()->sentence(),
            'foto_perbaikan' => null,
        ];
    }

    /**
     * Status pada tahap tertentu.
     */
    public function status(string $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    /**
     * Riwayat status dibuat oleh admin tertentu.
     */
    public function olehAdmin(User $admin): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $admin->id,
        ]);
    }
}
