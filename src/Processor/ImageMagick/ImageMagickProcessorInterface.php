<?php

/*
 * This file is part of the Teavee Image Magic Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\Teavee\ImageMagicBundle\Processor\ImageMagick;

use Scribe\Teavee\ImageMagicBundle\Processor\ProcessorInterface;

/**
 * Class ImageMagickInterface.
 */
interface ImageMagickProcessorInterface extends ProcessorInterface
{
    /**
     * @var string
     */
    const COLOR_SPACE_RGB = 'COLORSPACE_RGB';

    /**
     * @var string
     */
    const COLOR_SPACE_SRGB = 'COLORSPACE_SRGB';

    /**
     * @var float
     */
    const RESIZE_BLUR = 0.88549061701764;

    /**
     * @var string
     */
    const RESIZE_FILTER = 'FILTER_LANCZOSRADIUS';

    /**
     * @var string
     */
    const RESIZE_FILTER_ALT = 'FILTER_LANCZOS2';

    /**
     * @var string
     */
    const FORMAT_JPEG = 'jpeg';

    /**
     * @var string
     */
    const FORMAT_PNG = 'png';
}

/* EOF */
