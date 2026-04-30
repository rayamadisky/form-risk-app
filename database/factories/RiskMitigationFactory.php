<?php

namespace Database\Factories;

use App\Models\RiskMitigation;
use App\Models\RiskCause;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiskMitigation>
 */
class RiskMitigationFactory extends Factory
{
    protected $model = RiskMitigation::class;

    public function definition(): array
    {
        return [
            'risk_cause_id' => RiskCause::factory(),
            'mitigasi' => fake()->sentence(4),
        ];
    }
}
