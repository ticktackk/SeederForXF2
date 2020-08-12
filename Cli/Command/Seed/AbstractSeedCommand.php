<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\JobRunnerTrait;
use XF\Db\AbstractAdapter as DbAdapter;
use XF\Job\Manager as JobManager;

abstract class AbstractSeedCommand extends Command
{
    use JobRunnerTrait;

    abstract protected function getSeedName() : string;

    protected function getSeedDescription() : string
    {
        return 'Seeds ' . \strtolower($this->getContentTypePlural());
    }

    abstract protected function getContentTypePlural(InputInterface $input = null) : string;

    protected function getSeedClass(InputInterface $input) : string
    {
        $classParts = \explode('\\Cli\\Command\\Seed\\Seed', \get_called_class());
        
        return \implode(':', $classParts);
    }

    protected function configureOptions() : void
    {
    }

    protected function configure() : void
    {
        $this
            ->setName('tck-seeder:seed-' . $this->getSeedName())
            ->setDescription($this->getSeedDescription())
            ->addOption(
                'batch',
                'b',
                InputOption::VALUE_REQUIRED,
                'Batch size for this seed. Default: 500',
                100
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_REQUIRED,
                'Total amount of seeds to be planted. Default: 100',
                500
            )
            ->addOption(
                'resume',
                null,
                InputOption::VALUE_NONE
            );

        $this->configureOptions();
    }

    protected function getJobUniqueKey() : string
    {
        return 'tckSeederJob-' . $this->getSeedName();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $jobUniqueKey = $this->getJobUniqueKey();

        if ($input->getOption('resume'))
        {
            if ($this->jobManager()->getUniqueJob($jobUniqueKey))
            {
                $this->runJob($jobUniqueKey, $output);
                return 0;
            }

            $output->writeln("<error>There are no pending jobs of this type to resume.</error>");
            return 1;
        }

        $params = $this->getJobParams($input, $error);
        if ($error)
        {
            $output->writeln('<error>' . $error . '</error>');
            return 1;
        }

        $this->db()->logQueries(false);

        $this->setupAndRunJob(
            $jobUniqueKey,
            'TickTackk\Seeder:Seed',
            $params,
            $output
        );

        return 0;
    }

    protected function getOptionValues(InputInterface $input) : array
    {
        $options = $input->getOptions();
        $globalOptions = $this->getApplication()->getDefinition()->getOptions();

        foreach (\array_keys($globalOptions) AS $globalOption)
        {
            unset($options[$globalOption]);
        }

        unset($options['resume']);

        return $options;
    }

    protected function getSeedParams(InputInterface $input) : array
    {
        return [];
    }

    protected function getJobParams(InputInterface $input, &$error = null) : array
    {
        return \array_merge($this->getOptionValues($input), [
            'seed_class' => $this->getSeedClass($input),
            'content_type_plural' => $this->getContentTypePlural($input),
            'seed_params' => $this->getSeedParams($input)
        ]);
    }

    protected function jobManager() : JobManager
    {
        return \XF::app()->jobManager();
    }

    protected function db() : DbAdapter
    {
        return \XF::db();
    }
}