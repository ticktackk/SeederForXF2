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
            ->setDescription('Runs the seeds to fill your forum with dummy data.')
            ->addOption(
                'seed',
                't',
                InputOption::VALUE_OPTIONAL,
                'Name of the specific seed which should be used for seeding.'
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

        $seedNames = [];
        $seedName = $input->getOption('seed');

        if ($seedName)
        {
            $seedNames = [$seedName];
        }
        else
        {
            \XF::fire('seed_list', [\XF::app(), &$seedNames]);
        }

        $this->setupAndRunJob('tckSeeder' . \count($seedNames) === 1 ? '_' . reset($seedNames) : '', 'TickTackk\Seeder:Seed', [
            'seeds' => $seedNames
        ], $output);

        return 0;
    }
}