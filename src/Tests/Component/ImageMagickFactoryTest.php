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

use Scribe\Utility\UnitTest\AbstractMantleKernelTestCase;

/**
 * Class ImageMagickFactoryTest.
 */
class ImageMagickFactoryTest extends AbstractMantleKernelTestCase
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
