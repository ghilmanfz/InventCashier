<?php

use App\Services\TemperedGlassPricing;

it('applies minimum area rule', function () {
    $calc = TemperedGlassPricing::calculate(40,40,100000);
    expect($calc['effective_area'])->toBe(0.5)
        ->and($calc['total_price'])->toBe(50000);
});
