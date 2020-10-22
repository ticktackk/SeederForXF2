<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use SV\Threadmarks\Entity\ThreadmarkContentInterface as ThreadmarkContentEntityInterface;
use SV\Threadmarks\Entity\ThreadmarkContainerInterface as ThreadmarkContainerEntityInterface;
use SV\Threadmarks\Service\Threadmark\Creator as ThreadmarkCreatorSvc;
use SV\Threadmarks\Entity\ThreadmarkCategory as ThreadmarkCategoryEntity;
use XF\Mvc\Entity\Entity;

abstract class AbstractContentThreadmark extends AbstractSeed
{
    /**
     * @return Entity[]|ThreadmarkContainerEntityInterface[]|ThreadmarkContentEntityInterface[]|null
     */
    abstract protected function findRandomContentAndContainer() :? array;

    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();
        $randomContentAndContainer = $this->findRandomContentAndContainer();
        if (!$randomContentAndContainer)
        {
            return false;
        }
        [$content, $container] = $randomContentAndContainer;
        
        $threadmarkCreatorSvc = $this->getThreadmarkCreatorSvc($content, $container);
        $threadmarkCreatorSvc->setLabel(\implode(' ', Lorem::words()));
        $threadmarkCreatorSvc->setCategory($this->findRandomThreadmarkCategory());
        $threadmarkCreatorSvc->setPosition(false);
        $threadmarkCreatorSvc->resetNesting($faker->boolean);

        if (!$threadmarkCreatorSvc->validate($errors))
        {
            return false;
        }
        $threadmarkCreatorSvc->save();

        return true;
    }

    protected function getThreadmarkCreatorSvc(
        ThreadmarkContentEntityInterface $content,
        ThreadmarkContainerEntityInterface $container
    ) : ThreadmarkCreatorSvc
    {
        return $this->service('SV\Threadmarks:Threadmark\Creator', $content, $container);
    }

    protected function findRandomThreadmarkCategory() : ThreadmarkCategoryEntity
    {
        return $this->finderWithRandomOrder('SV\Threadmarks:ThreadmarkCategory')->fetchOne();
    }
}