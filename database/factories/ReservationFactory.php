<?php

namespace Database\Factories;

use App\Enum\ReservationStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'hotel_id' => Hotel::all()->random()->id,
            'user_id' => User::all()->random()->id,
            'step' => $this->faker->randomElement(ReservationStatus::asArray()),
        ];
    }
}
