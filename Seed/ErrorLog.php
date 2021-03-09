<?php

namespace TickTackk\Seeder\Seed;

use XF\Repository\User as UserRepo;
use XF\Entity\User as UserEntity;
use XF\Entity\ErrorLog as ErrorLogEntity;
use XF\Util\File as FileUtil;
use XF\Util\Ip as IpUtil;

class ErrorLog extends AbstractSeed
{
    /**
     * @var null|string[]
     */
    protected $filesList = null;

    /**
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();

        /** @var ErrorLogEntity $errorLog */
        $errorLog = $this->em()->create('XF:ErrorLog');
        $errorLog->exception_date = $faker->dateTime()->getTimestamp();
        $errorLog->user_id = $this->getRegisteredUserOrGuest()->user_id;
        $errorLog->ip_address = $faker->boolean ? (IpUtil::convertIpStringToBinary($faker->boolean ? $faker->ipv4 : $faker->ipv6)) : '';
        $errorLog->exception_type = $this->getRandomExceptionType();
        $errorLog->message = $faker->text;
        $errorLog->request_state = $faker->randomElements();
        $errorLog->trace_string = $faker->text;
        $errorLog->filename = $this->getRandomFile();
        $errorLog->line = $this->getRandomLineFromFilename($errorLog->filename);

        return $errorLog->save();
    }

    protected function getRegisteredUserOrGuest() : UserEntity
    {
        $faker = $this->faker();
        if ($faker->boolean)
        {
            return \XF::visitor();
        }

        return $this->getUserRepo()->getGuestUser($faker->boolean ? $faker->name : null);
    }

    protected function getExceptionTypes() : array
    {
        return [
            'InvalidArgumentException',
            'BadFunctionCallException',
            'DomainException',
            'LengthException',
            'OutOfRangeException',
            'RuntimeException',
            'OutOfBoundsException',
            'OverflowException',
            'RangeException',
            'UnderflowException',
            'UnexpectedValueException',
            'Exception',
            'Error',
        ];
    }

    protected function getRandomExceptionType() : string
    {
        $exceptionTypes = $this->getExceptionTypes();
        $exceptionTypeIndex = \array_rand($exceptionTypes);

        return $exceptionTypes[$exceptionTypeIndex];
    }

    protected function getFileList() : array
    {
        if ($this->filesList === null)
        {
            $filesList = [];

            foreach (FileUtil::getRecursiveDirectoryIterator(\XF::getSourceDirectory()) AS $fileInfo)
            {
                if ($fileInfo->isDir())
                {
                    continue;
                }

                $filesList[] = $fileInfo->getRealPath();
            }

            $this->filesList = $filesList;
        }

        return $this->filesList;
    }

    protected function getRandomFile() : string
    {
        $sourceDir = \XF::getSourceDirectory() . \XF::$DS;

        $fileList = $this->getFileList();
        $fileIndex = \array_rand($fileList);
        $file = $fileList[$fileIndex];

        return \substr($file, utf8_strlen($sourceDir));
    }

    protected function getRandomLineFromFilename(string $filePath) : int
    {
        $filePath = \XF::getSourceDirectory() . \XF::$DS . $filePath;
        return $this->faker()->numberBetween(0, \count(\file($filePath)));
    }

    protected function getUserRepo() : UserRepo
    {
        return $this->repository('XF:User');
    }
}