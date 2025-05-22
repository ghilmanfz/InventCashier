<?php

if (! function_exists('generateSequentialNumber')) {
    function generateSequentialNumber(string $model, ?string $initials = null, string $column = 'order_number'): string
    {
        $lastRecord = $model::latest('id')->first();

        $lastNumber = $lastRecord ? intval(substr($lastRecord->$column, strlen($initials))) : 0;
        $newNumber = $lastNumber + 1;

        return $initials . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
    }
}
