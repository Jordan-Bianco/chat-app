<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->sentence(2);
        $slug = Str::slug($name);

        $userId = User::all()->count() > 1 ? User::all()->random()->id : User::factory()->create()->id;

        return [
            'id' => $this->faker->uuid(),
            'user_id' => $userId,
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->paragraph()
        ];
    }
}
