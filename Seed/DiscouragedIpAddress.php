<?php

namespace TickTackk\Seeder\Seed;

use XF\Repository\Banning as BanningRepo;

class DiscouragedIpAddress extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();

        $ip = $faker->boolean ? $faker->ipv4 : $faker->ipv6;
        $reason = $faker->boolean ? $faker->text : '';

        return $this->getBanningRepo()->discourageIp(
            $ip,
            $reason
        );
    }

    protected function getBanningRepo() : BanningRepo
    {
        return $this->repository('XF:Banning');
    }
}