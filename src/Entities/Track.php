<?php

declare(strict_types=1);

namespace App\Entities;

class Track extends BaseCar
{
    private float $width;
    private float $height;
    private float $length;

    public function __construct(
        string $brand,
        string $photoFileName,
        float  $carrying,
        float  $width,
        float  $height,
        float  $length,
    ) {
        parent::__construct($brand, $photoFileName, $carrying);

        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->carType = CarType::truck;
    }

    public static function fromArray(array $attributes): static
    {
        $dimensions = explode('x', $attributes['body_whl'] ?? '');
        $width = $dimensions[0] ?? 0;
        $height = $dimensions[1] ?? 0;
        $length = $dimensions[2] ?? 0;

        return new static(
            trim($attributes['brand'] ?? ''),
            trim($attributes['photo_file_name'] ?? ''),
            (float) $attributes['carrying'] ?? 0,
            (float) $width,
            (float) $height,
            (float) $length,
        );
    }

    public function getBodyVolume(): float
    {
        return $this->width * $this->height * $this->length;
    }
}
