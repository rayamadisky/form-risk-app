<?php

namespace Database\Factories;

use App\Models\RiskCause;
use App\Models\RiskItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiskCause>
 */
class RiskCauseFactory extends Factory
{
    protected $model = RiskCause::class;

    public function definition(): array
    {
        return [
            'risk_item_id' => RiskItem::factory(),
            'penyebab' => fake()->sentence(2),
        ];
    }
}
