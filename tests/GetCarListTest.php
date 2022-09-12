<?php

declare(strict_types=1);

namespace Tests;

use App\Entities\Car;
use App\Entities\SpecMachine;
use App\Entities\Track;
use PHPUnit\Framework\TestCase;

class GetCarListTest extends TestCase
{
    private $tmpFile;

    public function setUp(): void
    {
        parent::setUp();
        $this->tmpFile = sys_get_temp_dir() . '/cars.csv';
    }

    public function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function test_happy_path()
    {
        file_put_contents(
            $this->tmpFile,
            <<<CSV
            car_type;brand;passenger_seats_count;photo_file_name;body_whl;carrying;extra
            car;Nissan xTtrail;4;f1.jpeg;;2.5;
            truck;Man;;f2.png;8x3x2.5;20;
            
            spec_machine;Hitachi;;f4;;1.2;Легкая техника для уборки снега
            CSV
        );

        $cars = \getCarList($this->tmpFile);

        $this->assertCount(3, $cars);
        $this->assertEquals(new Car('Nissan xTtrail', 'f1.jpeg', 2.5, 4), array_shift($cars));
        $this->assertEquals(new Track('Man', 'f2.png', 20., 8., 3., 2.5), array_shift($cars));
        $this->assertEquals(new SpecMachine('Hitachi', 'f4', 1.2, 'Легкая техника для уборки снега'), array_shift($cars));
    }

    public function test_invalid_car_type()
    {
        file_put_contents(
            $this->tmpFile,
            <<<CSV
            car_type;brand;passenger_seats_count;photo_file_name;body_whl;carrying;extra
            car;Nissan xTtrail;4;f1.jpeg;;2.5;
            cars;Nissan xTtrail;4;f1.jpeg;;2.5;
            trucks;Man;;f2.png;8x3x2.5;20;
            spec_machines;Hitachi;;f4;;1.2;Легкая техника для уборки снега
            CSV
        );

        $cars = \getCarList($this->tmpFile);

        $this->assertCount(1, $cars);
        $this->assertEquals(new Car('Nissan xTtrail', 'f1.jpeg', 2.5, 4), array_shift($cars));
    }


    public function test_trim_values()
    {
        file_put_contents(
            $this->tmpFile,
            <<<CSV
            car_type;brand;passenger_seats_count;photo_file_name;body_whl;carrying;extra
            car ; Nissan xTtrail ; 4 ; f1.jpeg ;; 2.5;
            truck ; Man ;; f2.png ; 8x3x2.5 ; 20 ;
            
            spec_machine ; Hitachi ; ; f4 ;; 1.2 ; Легкая техника для уборки снега
            CSV
        );

        $cars = \getCarList($this->tmpFile);

        $this->assertCount(3, $cars);
        $this->assertEquals(new Car('Nissan xTtrail', 'f1.jpeg', 2.5, 4), array_shift($cars));
        $this->assertEquals(new Track('Man', 'f2.png', 20., 8., 3., 2.5), array_shift($cars));
        $this->assertEquals(new SpecMachine('Hitachi', 'f4', 1.2, 'Легкая техника для уборки снега'), array_shift($cars));
    }

    public function test_dimensions_default_value()
    {
        file_put_contents(
            $this->tmpFile,
            <<<CSV
            car_type;brand;passenger_seats_count;photo_file_name;body_whl;carrying;extra
            truck;Man;;f2.png;8x3;20;
            truck;Man;;f2.png;8;20;
            truck;Man;;f2.png;;20;
            CSV
        );

        $cars = \getCarList($this->tmpFile);

        $this->assertCount(3, $cars);
        $this->assertEquals(new Track('Man', 'f2.png', 20, 8., 3., 0), array_shift($cars));
        $this->assertEquals(new Track('Man', 'f2.png', 20, 8., 0, 0), array_shift($cars));
        $this->assertEquals(new Track('Man', 'f2.png', 20, 0, 0, 0), array_shift($cars));
    }

    /**
     * @dataProvider getFileContent
     */
    public function test_validation($fileContent)
    {
        file_put_contents(
            $this->tmpFile,
            <<<CSV
            car_type;brand;passenger_seats_count;photo_file_name;body_whl;carrying;extra
            car;Nissan xTtrail;4;f1.jpeg;;2.5;
            {$fileContent}
            CSV
        );

        $cars = \getCarList($this->tmpFile);

        $this->assertCount(1, $cars);
        $this->assertEquals(new Car('Nissan xTtrail', 'f1.jpeg', 2.5, 4), array_shift($cars));
    }

    public function getFileContent(): array
    {
        return [
            'car\'s brand is required' => ['car;;4;f1.jpeg;;2.5;'],
            'car\'s passenger_seat_count is required' => ['car;Nissan xTtrail;;f1.jpeg;;2.5;'],
            'car\'s photo_file_name is required' => ['car;Nissan xTtrail;4;;;2.5;'],
            'car\'s carrying is required' => ['car;Nissan xTtrail;4;f1.jpeg;;;'],
            'track\'s brand is required' => ['truck;;;f2.png;;20;'],
            'track\'s photo_file_name is required' => ['truck;Man;;;;20;'],
            'track\'s carrying is required' => ['truck;Man;;f2.png;;;'],
            'spec_machine\'s brand is required' => ['spec_machine;;;f4;;1.2;Легкая техника для уборки снега'],
            'spec_machine\'s photo_file_name is required' => ['spec_machine;Hitachi;;;;1.2;Легкая техника для уборки снега'],
            'spec_machine\'s carrying is required' => ['spec_machine;Hitachi;;f4;;;Легкая техника для уборки снега'],
            'spec_machine\'s extra is required' => ['spec_machine;Hitachi;;f4;;1.2;'],
            'invalid column count' => ['car;Nissan xTtrail;4;f1.jpeg;;2.5'],
        ];
    }

    /**
     * @dataProvider getHeaders
     */
    public function test_header_format($headers)
    {
        file_put_contents(
            $this->tmpFile,
            <<<CSV
            {$headers}
            car;Nissan xTtrail;4;f1.jpeg;;2.5;
            CSV
        );

        $this->expectExceptionMessage('Invalid header format');
        \getCarList($this->tmpFile);
    }

    public function getHeaders()
    {
        return [
            'header is missing' => ['car_type;;passenger_seats_count;photo_file_name;body_whl;carrying;extra'],
            'header is typo' => ['car_type;brands;passenger_seats_count;photo_file_name;body_whl;carrying;extra'],
            'header is extra' => ['car_type;brand;passenger_seats_count;photo_file_name;body_whl;carrying;extra;additional'],
        ];
    }

    public function test_missing_file()
    {
        $this->expectExceptionMessage('No such file or directory');
        \getCarList($this->tmpFile);
    }
}
