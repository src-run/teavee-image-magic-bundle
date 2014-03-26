<?php
/*
 * This file is part of the Scribe World Application.
 *
 * (c) Scribe Inc. <scribe@scribenet.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\ImageMagickBundle\Component;

/**
 * ImageMagickFactory class
 */
class ImageMagickFactory
{
    /**
     * @return ImageMagick
     */
    public function get() 
    {
        return new ImageMagick;
    }
}