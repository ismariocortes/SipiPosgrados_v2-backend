<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $aspiranteId = Role::query()->where('name', 'aspirante')->value('id');
        $quickRegId = UserStatus::query()->where('code', UserStatus::CODE_QUICK_REGISTRATION)->value('id');

        return [
            'folio' => null,
            'folio_type' => null,
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'identity_type' => 'passport',
            'identity_value' => fake()->unique()->regexify('[A-Z0-9]{12}'),
            'phone' => fake()->unique()->numerify('##########'),
            'role_id' => $aspiranteId ?? 1,
            'user_status_id' => $quickRegId ?? 1,
        ];
    }
}
