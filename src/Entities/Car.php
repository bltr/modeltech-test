<?php

declare(strict_types=1);

namespace App\Entities;

use Webmozart\Assert\Assert;

class Car extends BaseCar
{
    private int $passengerSeatsCount;

    public function __construct(
        string $brand,
        string $photoFileName,
        float  $carrying,
        int    $passengerSeatsCount
    ) {
        Assert::notEmpty($passengerSeatsCount);

        parent::__construct($brand, $photoFileName, $carrying);
        $this->passengerSeatsCount = $passengerSeatsCount;
        $this->carType = CarType::car;
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            trim($attributes['brand'] ?? ''),
            trim($attributes['photo_file_name'] ?? ''),
            (float) $attributes['carrying'] ?? 0,
            (int) $attributes['passenger_seats_count'] ?? 0,
        );
    }
}
