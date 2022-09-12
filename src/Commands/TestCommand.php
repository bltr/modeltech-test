<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $cars = getCarList($input->getArgument('name'));

            foreach ($cars as $car) {
                var_dump($car->getPhotoFileExt());
            }
        } catch (\Throwable $exception) {
            $output->writeln($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
