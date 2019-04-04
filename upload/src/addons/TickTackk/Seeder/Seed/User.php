<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class User
 *
 * @package TickTackk\Seeder\Seed
 */
class User extends AbstractSeed
{
    /**
     * User constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit($this->faker()->numberBetween(1000, 1500));
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle() : \XF\Phrase
    {
        return $this->app->getContentTypePhrase('user', true);
    }

    /**
     * @param array|null $errors
     */
    protected function seedInternal(array &$errors = null) : void
    {
        $faker = $this->faker();

        /** @var \XF\Service\User\Registration $registrationService */
        $registrationService = $this->service('XF:User\Registration');
        $registrationService->setMapped([
            'username' => $faker->boolean ? $faker->userName : $faker->firstName . '_' . $faker->lastName,
            'email' => $faker->userName . '_' . $faker->email,
            'timezone' => $faker->timezone,
            'location' => $faker->boolean ? $faker->city : ''
        ]);
        $registrationService->setPassword($faker->password, '', false);
        $registrationService->setReceiveAdminEmail($faker->boolean);

        $dob = explode('-', $faker->dateTimeThisCentury->format('d-m-Y'));
        $registrationService->setDob($dob[0], $dob[1], $dob[2]);
        $registrationService->skipEmailConfirmation();
        $registrationService->setReceiveAdminEmail($faker->boolean);

        if ($faker->boolean)
        {
            $registrationService->setAvatarUrl($faker->imageUrl());
        }

        if ($registrationService->validate($errors))
        {
            $registrationService->save();
        }
    }
}