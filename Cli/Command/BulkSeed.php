<?php

namespace TickTackk\Seeder\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TickTackk\Seeder\Repository\Seed as SeedRepo;
use XF\Cli\Command\JobRunnerTrait;
use XF\Mvc\Entity\Repository;
use function is_numeric;

/**
 * Class BulkSeed
 *
 * @package TickTackk\Seeder\Cli\Command
 */
class BulkSeed extends Command
{
    use JobRunnerTrait;

    protected function configure() : void
    {
        $this
            ->setName('tck-seeder:bulk-seed')
            ->setDescription('Runs multiple seeders at once.')
            ->addOption(
                'seeds',
                's',
                InputOption::VALUE_OPTIONAL,
                "When provided, only the provided seeds (separated by a ',') will be seeded."
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \XF::db()->logQueries(false);

        $seedRepo = $this->getSeedRepo();
        $seeds = $input->getOption('seeds');
        $limits = [];
        if ($seeds)
        {
            $seeds = array_map(function ($seed)
            {
                return str_replace('/', '\\', $seed);
            }, explode(',', $seeds));

            foreach ($seeds AS $seed)
            {
                if (!$seedRepo->isValidSeed($seed))
                {
                    $output->writeln("<error>Provided seed '{$seed}' does not exist.</error>");
                    return 1;
                }
            }
        }
        else
        {
            $seeds = $seedRepo->getAvailableSeeds();
        }

        if (!$seeds)
        {
            $output->writeln('<error>No seeds available.</error>');
            return 1;
        }

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        foreach ($seeds AS $seed)
        {
            $question = new Question("<question>Enter the seed limit for '{$seed}' seed (default: 100)</question>", '100');
            $question->setValidator(function ($value) use($seed)
            {
                if (!is_numeric($value))
                {
                    throw new \InvalidArgumentException("Please enter the limit for '{$seed}' in valid number.");
                }

                return (int) $value;
            });
            $limit = $questionHelper->ask($input, $output, $question);
            if ($limit)
            {
                $limits[$seed] = $limit;
            }
            else
            {
                unset($seeds[$seed]);
            }
        }

        $this->setupAndRunJob('tckSeeder-bulk-seed-' .  implode(',', $seeds), 'TickTackk\Seeder:Seed', [
            'seeds' => $seeds,
            'bulk_limits' => $limits
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