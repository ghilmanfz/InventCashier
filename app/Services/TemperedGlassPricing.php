<?php

namespace App\Services;

class TemperedGlassPricing
{
    /**
     * @return array{effective_area:float,total_price:int}
     */
    public static function calculate(
        float $lengthCm,
        float $widthCm,
        int   $pricePerM2
    ): array {
        $chargedWidth = max($widthCm, 50);                    // syarat lebar min 50 cm
        $areaM2       = ($lengthCm * $chargedWidth) / 10000;  // luas m2
        $effective    = max($areaM2, 0.5);                    // min 0.5 m2

        return [
            'effective_area' => $effective,
            'total_price'    => (int) round($effective * $pricePerM2),
        ];
    }
}
