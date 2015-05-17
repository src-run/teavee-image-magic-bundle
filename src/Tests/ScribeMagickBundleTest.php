<?php

/*
 * This file is part of the Scribe Magick Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\MagickBundle\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Scribe\MagickBundle\ScribeMagickBundle;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScribeMagickBundleTest.
 */
class ScribeMagickBundleTest extends PHPUnit_Framework_TestCase
{
    const FULLY_QUALIFIED_CLASS_NAME = 'Scribe\MagickBundle\ScribeMagickBundle';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    public function getNewBundle()
    {
        return new ScribeMagickBundle();
    }

    public function getReflection()
    {
        return new ReflectionClass(self::FULLY_QUALIFIED_CLASS_NAME);
    }

    public function testCanBuildContainer()
    {
        static::assertTrue(($this->container instanceof Container));
    }

    public function testCanAccessContainerServices()
    {
        static::assertTrue($this->container->has('s.magick'));
    }
}

/* EOF */
