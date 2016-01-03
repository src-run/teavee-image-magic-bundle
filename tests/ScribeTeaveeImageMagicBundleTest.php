<?php

/*
 * This file is part of the Teavee Image Magic Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\Teavee\ImageMagicBundle\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Scribe\Teavee\ImageMagicBundle\ScribeTeaveeImageMagicBundle;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScribeTeaveeImageMagicBundleTest.
 */
class ScribeTeaveeImageMagicBundleTest extends PHPUnit_Framework_TestCase
{
    const FULLY_QUALIFIED_CLASS_NAME = 'Scribe\Teavee\ImageMagicBundle\ScribeTeaveeImageMagicBundle';

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
        return new ScribeTeaveeImageMagicBundle();
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
        static::assertTrue($this->container->has('s.image_processor'));
    }
}

/* EOF */
