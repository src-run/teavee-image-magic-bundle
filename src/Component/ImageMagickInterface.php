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

use Scribe\MagickBundle\Exception\MagickException;

/**
 * ImageMagickInterface
 */
interface ImageMagickInterface
{
    /**
     * RGB colorspace.
     */
    const COLORSPACE_RGB = \Imagick::COLORSPACE_RGB;

    /**
     * SRGB colorspace.
     */
    const COLORSPACE_SRGB = \Imagick::COLORSPACE_SRGB;

    /**
     * Format image as JPEG.
     *
     * @var string
     */
    const FORMAT_JPEG = 'jpeg';

    /**
     * Initialize internal PHP IMagick object.
     *
     * @return $this
     */
    public function init();

    /**
     * Read image as binary
     *
     * @param mixed  $image
     * @param string $name
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function readBinary($image, $name);

    /**
     * Read image from file path
     *
     * @param string      $path
     * @param null|string $name
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function readFile($path, $name = null);

    /**
     * Read image from resource handle
     *
     * @param resource    $resource
     * @param null|string $name
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function readResource($resource, $name = null);

    /**
     * Sets background color and removes alpha channel.
     *
     * @param  null|string $backgroundColor
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function removeAlpha($backgroundColor = null);

    /**
     * Removes (flattens) layers.
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function removeLayers();

    /**
     * Remove excess metadata.
     *
     * @throws MagickException
     *
     * @return $this
     */
    public function removeMeta();
}

/* EOF */
