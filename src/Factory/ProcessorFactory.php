<?php

/*
 * This file is part of the Teavee Image Magic Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\Teavee\ImageMagicBundle\Factory;

use Scribe\Teavee\ImageMagicBundle\Processor\ProcessorInterface;
use Scribe\WonkaBundle\Component\DependencyInjection\Container\ServiceFinder;

/**
 * Class ProcessorFactory.
 */
class ProcessorFactory
{
    /**
     * @param ServiceFinder $finder
     * @param string        $serviceKey
     *
     * @return ProcessorInterface
     */
    public static function getProcessor(ServiceFinder $finder, $serviceKey)
    {
        return $finder($serviceKey);
    }
}

/* EOF */
