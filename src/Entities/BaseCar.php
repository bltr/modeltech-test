<?php

declare(strict_types=1);

namespace App\Entities;

use Webmozart\Assert\Assert;

abstract class BaseCar
{
    protected CarType $carType;
    protected string $brand;
    protected string $photoFileName;
    protected float $carrying;

    public function __construct(string $brand, string $photoFileName, float $carrying)
    {
        Assert::notEmpty($brand);
        Assert::notEmpty($photoFileName);
        Assert::notEmpty($carrying);

        $this->brand = $brand;
        $this->photoFileName = $photoFileName;
        $this->carrying = $carrying;
    }

    public function getPhotoFileExt(): string
    {
        $lastDotPosition = strrpos($this->photoFileName, '.');

        if ($lastDotPosition === false) {
            return '';
        }

        return substr($this->photoFileName, $lastDotPosition);
    }
}
