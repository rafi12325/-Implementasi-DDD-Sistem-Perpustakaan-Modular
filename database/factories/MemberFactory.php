<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Membership\Models\Member;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'member_number' => 'MBR-' . fake()->unique()->numberBetween(10000, 99999),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'status' => 'active',
        ];
    }
}
