<?php

declare(strict_types=1);

use App\Entities\BaseCar;
use App\Entities\Car;
use App\Entities\CarType;
use App\Entities\SpecMachine;
use App\Entities\Track;
use Webmozart\Assert\Assert;

/**
 * @throws InvalidArgumentException
 *
 * @return array|BaseCar[]
 */
function getCarList(string $fileName): array
{
    if (!file_exists($fileName)) {
        throw new InvalidArgumentException('No such file or directory');
    }
    $file = fopen($fileName, 'r');

    $headers = fgetcsv($file, null, ';');
    $requiredHeaders = ['car_type', 'brand', 'passenger_seats_count', 'photo_file_name', 'body_whl', 'carrying', 'extra'];
    Assert::eq($headers, $requiredHeaders, 'Invalid header format');

    $cars = [];
    while (($attributes = fgetcsv($file, null, ';')) !== false) {
        if (count($attributes) < count($requiredHeaders)) {
            continue;
        }

        $attributes = array_combine($headers, $attributes);

        $carType = trim($attributes['car_type']);
        if (!in_array($carType, CarType::values())) {
            continue;
        }

        try {
            $cars[] = match ($carType) {
                CarType::car->value => Car::fromArray($attributes),
                CarType::truck->value => Track::fromArray($attributes),
                CarType::spec_machine->value => SpecMachine::fromArray($attributes),
            };
        } catch (InvalidArgumentException $exception) {
            continue;
        }
    }

    return $cars;
}
