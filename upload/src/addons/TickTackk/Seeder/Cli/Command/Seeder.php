<?php

namespace TickTackk\Seeder\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\JobRunnerTrait;

/**
 * Class Seeder
 *
 * @package TickTackk\Seeder\Cli\Command
 */
class Seeder extends Command
{
    use JobRunnerTrait;

    protected function configure() : void
    {
        $this
            ->setName('tck-seeder:seed')
            ->setDescription('Runs all the seeds to fill your forum with dummy data.')
            ->addOption(
                'content-type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Run specific seed instead of all'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ? int
    {
        \XF::db()->logQueries(false);

        /** @var \TickTackk\Seeder\Repository\Seed $seedRepo */
        $seedRepo = \XF::app()->repository('TickTackk\Seeder:Seed');
        $contentType = $input->getOption('content-type');
        $orderedSeeds = $seedRepo->getOrderedSeeds(!empty($contentType) ? [$contentType] : null);

        $this->setupAndRunJob('tckSeeder' . (!empty($contentType) ? '_' . $contentType : ''), 'TickTackk\Seeder:Seed', [
            'seeds' => $orderedSeeds
        ], $output);

        return 0;
    }
}