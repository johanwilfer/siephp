<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * Set private property.
     */
    public function setProperty(object $object, string $name, mixed $value): void
    {
        $reflectionObject = new \ReflectionObject($object);

        // use reflection to set private property
        $reflectionProperty = $reflectionObject->getProperty($name);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Get private property.
     */
    public function getProperty(object $object, string $name): mixed
    {
        $reflectionObject = new \ReflectionObject($object);

        // use reflection to set private property
        $reflectionProperty = $reflectionObject->getProperty($name);
        return $reflectionProperty->getValue($object);
    }
}