<?php

namespace TickTackk\Seeder\Repository;

use TickTackk\Seeder\Seed\AbstractSeed;
use XF\Mvc\Entity\Repository;

/**
 * Class Seed
 *
 * @package TickTackk\Seeder\Repository
 */
class Seed extends Repository
{
    /**
     * @param string $type
     * @param bool   $throw
     *
     * @return null|AbstractSeed
     * @throws \Exception
     */
    public function getSeedHandler(string $type, bool $throw = false) :? AbstractSeed
    {
        $handlerClass = $this->app()->getContentTypeFieldValue($type, 'seed_handler_class');
        if (!$handlerClass)
        {
            if ($throw)
            {
                throw new \InvalidArgumentException("No Seed handler for '$type'");
            }
            return null;
        }

        if (!class_exists($handlerClass))
        {
            if ($throw)
            {
                throw new \InvalidArgumentException("Seed handler for '$type' does not exist: $handlerClass");
            }
            return null;
        }

        $handlerClass = \XF::extendClass($handlerClass);
        return new $handlerClass($this->app(), $type);
    }

    /**
     * @param bool $throw
     *
     * @return AbstractSeed[]
     * @throws \Exception
     */
    public function getSeedHandlers($throw = false) : array
    {
        $handlers = [];

        foreach (\XF::app()->getContentTypeField('seed_handler_class') AS $contentType => $handlerClass)
        {
            if ($handler = $this->getSeedHandler($contentType, $throw))
            {
                $handlers[$contentType] = $handler;
            }
        }

        return $handlers;
    }

    /**
     * @param array|null $contentTypes
     * @return array
     * @throws \Exception
     */
    public function getOrderedSeeds(array $contentTypes = null)
    {
        $handlers = $this->getSeedHandlers();
        $internalHandlerCollection = [];

        foreach ($handlers AS $contentType => $handler)
        {
            if ($contentTypes === null || \in_array($contentType, $contentTypes, true))
            {
                $internalHandlerCollection[$handler->getRunOrder()][] = [
                    'contentType' => $contentType,
                    'handler' => $handler
                ];
            }
        }
        ksort($internalHandlerCollection);

        $finalHandlers = [];
        foreach ($internalHandlerCollection AS $order => $internalHandler)
        {
            foreach ($internalHandler AS $data)
            {
                $finalHandlers[$order . '_' . $data['contentType']] = $data['contentType'];
            }
        }

        return $finalHandlers;
    }
}