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
     * @return int
     */
    public function getRunOrder(): int
    {
        return 5;
    }

    /**'
     * @return int
     */
    public function getLimit(): int
    {
        return $this->faker()->numberBetween(1000, 1500);
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
            'username' => $faker->firstName . '_' . $faker->lastName,
            'email' => $faker->email,
            'timezone' => $faker->timezone,
            'location' => $faker->boolean ? $faker->city : ''
        ]);
        $registrationService->setPassword($faker->password, '', false);
        $registrationService->setReceiveAdminEmail($faker->boolean);

        $dob = explode('-', $faker->dateTimeThisCentury->format('d-m-Y'));
        $registrationService->setDob($dob[0], $dob[1], $dob[2]);
        $registrationService->skipEmailConfirmation();

        if ($registrationService->validate($errors))
        {
            $registrationService->save();
        }
    }
}