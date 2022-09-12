<?php

declare(strict_types=1);

namespace App\Entities;

use Webmozart\Assert\Assert;

class SpecMachine extends BaseCar
{
    private string $extra;

    public function __construct(string $brand, string $photoFileName, float $carrying, string $extra)
    {
        Assert::notEmpty($extra);

        parent::__construct($brand, $photoFileName, $carrying);
        $this->extra = $extra;
        $this->carType = CarType::spec_machine;
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            trim($attributes['brand'] ?? ''),
            trim($attributes['photo_file_name'] ?? ''),
            (float) $attributes['carrying'] ?? 0,
            trim($attributes['extra'] ?? ''),
        );
    }
}
