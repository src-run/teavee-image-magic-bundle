<?php

/*
 * This file is part of the Scribe Magick Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\MagickBundle\Component;

/**
 * Class ImageMagickFactory.
 */
class ImageMagickFactory
{
    /**
     * @return ImageMagick
     */
    public function get()
    {
        return (new ImageMagick())->init();
    }
}

/* EOF */
