<?php

/*
 * This file is part of the Teavee Image Magic Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\Teavee\ImageMagicBundle\Tests\Factory;

use Scribe\WonkaBundle\Utility\TestCase\KernelTestCase;

/**
 * Class ProcessorFactoryImageMagickTest.
 */
class ProcessorFactoryImageMagickTest extends KernelTestCase
{
    public function testFactoryReturnedInstance()
    {
        static::assertInstanceOf(
            'Scribe\Teavee\ImageMagicBundle\Processor\ImageMagick\ImageMagickProcessor',
            static::$staticContainer->get('s.magick')
        );

        static::assertInstanceOf(
            'Scribe\Teavee\ImageMagicBundle\Processor\ProcessorInterface',
            static::$staticContainer->get('s.magick')
        );
    }
}

/* EOF */
