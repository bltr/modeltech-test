<?php

declare(strict_types=1);

namespace App\Entities;

enum CarType: string
{
    case car = 'car';
    case spec_machine = 'spec_machine';
    case truck = 'truck';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, static::cases());
    }
}
