<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'phone_no' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'experience' => rand(1, 10),
            'cv_url' => 'storage/artist/CVs/kjsaBSJBKSAAsn121.pdf',
            'image_url' => 'storage/profileImages/default-profile-image.png',
            'artist_verified_at' => now(),
            'total_balance' => $this->faker->numberBetween($min = 100, $max = 1000)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
