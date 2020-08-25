<?php

namespace TickTackk\Seeder\Seed;

trait RandomVersionGeneratorTrait
{
    protected function getRandomVersionString() : string
    {
        $faker = $this->faker();
        $versionParts = [];
        $joiner = $faker->boolean ? '-' : ($faker->boolean ? '_' : ($faker->boolean ? '.' : ' '));

        do
        {
            if ($faker->boolean)
            {
                $versionParts[] = $faker->dayOfMonth;
                $versionParts[] = $faker->month;
                $versionParts[] = $faker->year;
            }
            else if ($faker->boolean)
            {
                $versionParts[] = $faker->randomNumber();
                $versionParts[] = $faker->randomNumber();
                $versionParts[] = $faker->randomNumber();
            }
        }
        while (\count($versionParts) < 3);

        return \implode($joiner, $versionParts);
    }
}