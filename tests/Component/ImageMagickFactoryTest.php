<?php

/*
 * This file is part of the Scribe Magick Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\MagickBundle\Tests\Component;

use Scribe\WonkaBundle\Utility\TestCase\KernelTestCase;

/**
 * Class ImageMagickFactoryTest.
 */
class ImageMagickFactoryTest extends KernelTestCase
{
    public function testFactoryReturnedInstance()
    {
        static::assertInstanceOf(
            'Scribe\MagickBundle\Component\ImageMagick',
            static::$staticContainer->get('s.magick')
        );
    }
}

/* EOF */
