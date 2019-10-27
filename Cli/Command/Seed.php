<?php

namespace TickTackk\Seeder\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\JobRunnerTrait;
use XF\Mvc\Entity\Repository;
use TickTackk\Seeder\Repository\Seed as SeedRepo;

/**
 * Class Seed
 *
 * @package TickTackk\Seeder\Cli\Command
 */
class Seed extends Command
{
    use JobRunnerTrait;

    protected function configure() : void
    {
        $this
            ->setName('tck-seeder:seed')
            ->setDescription('Runs a specific seed.')
            ->addArgument(
                'seed',
                InputArgument::REQUIRED,
                'Short class of the seed..'
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit how much data must be seeded.',
                500
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

        $seed = str_replace('/', '\\', $input->getArgument('seed'));
        $seedRepo = $this->getSeedRepo();
        if (!$seedRepo->isValidSeed($seed))
        {
            $output->writeln("<error>Provided seed '{$seed}' does not exist.</error>");
            return 1;
        }

        $limit = (int) $input->getOption('limit');
        $this->setupAndRunJob('tckSeeder-seed-' .  $seed, 'TickTackk\Seeder:Seed', [
            'seeds' => [$seed],
            'limit' => $limit
        ], $output);

        \XF::db()->logQueries(true);

        return 0;
    }

    /**
     * @return Repository|SeedRepo
     */
    protected function getSeedRepo() : SeedRepo
    {
        return \XF::app()->repository('TickTackk\Seeder:Seed');
    }
}