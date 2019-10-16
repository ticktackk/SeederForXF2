<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;

/**
 * Class Conversation
 *
 * @package TickTackk\Seeder\Seed
 */
class Conversation extends AbstractSeed
{
    /**
     * Conversation constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit($this->faker()->numberBetween(4000, 15000));
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle(): \XF\Phrase
    {
        return \XF::phrase('conversations');
    }

    /**
     * @param array|null $errors
     */
    protected function seedInternal(array &$errors = null): void
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();

        if ($randomUsers = $this->randomEntities('XF:User', $faker->numberBetween(1, 3), [
            ['user_id', '<>', $visitor->user_id]
        ]))
        {
            /** @var \XF\Service\Conversation\Creator $creator */
            $creator = $this->service('XF:Conversation\Creator', $visitor);
            $creator->setIsAutomated();
            $creator->setContent(Lorem::sentence(), $faker->text);
            if ($faker->boolean)
            {
                $creator->setLogIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
            }
            $creator->setRecipientsTrusted($randomUsers);
            if ($creator->validate($errors))
            {
                $creator->save();
            }
        }
    }
}