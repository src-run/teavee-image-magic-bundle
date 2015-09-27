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

use \Imagick;
use Scribe\MagickBundle\Exception\MagickException;

/**
 * ImageMagick class
 */
class ImageMagick implements ImageMagickInterface
{
    /**
     * Internal PHP ImageMagick object.
     *
     * @var Imagick
     */
    protected $imagick;

    /**
     * Constructor does not automatically create imagick instance to work with.
     */
    public function __construct() {}

    /**
     * Initialize internal PHP IMagick object.
     *
     * @return $this
     */
    public function init()
    {
        $this->imagick = new Imagick;

        return $this;
    }

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
    public function readBinary($image, $name)
    {
        try {
            $this->imagick->readImageBlob($image, $name);
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Unrecoverable error while reading binary image %s: %s', (string) $name, (string) $e->getMessage())
            );
        }

        return $this;
    }

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
    public function readFile($path, $name = null)
    {
        if (true !== is_readable($path)) {
            throw new MagickException(
                sprintf('File path is not readable: %s', (string) $path)
            );
        }

        if (null === $name || strlen($name) === 0) {
            $name = pathinfo($path, PATHINFO_FILENAME);
        }

        try {
            $this->imagick->readImage($path);
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Unrecoverable error while reading image %s from path %s: %s', (string) $name, (string) $path, (string) $e->getMessage())
            );
        }

        return $this;
    }

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
    public function readResource($resource, $name = null)
    {
        if (true !== is_resource($resource)) {
            throw new MagickException(
                sprintf('Invalid resource provided for %s', (string) $name)
            );
        }

        try {
            $this->imagick->readImageFile($resource, $name);
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Unrecoverable error while reading resource %s: %s', (string) $name, (string) $e->getMessage())
            );
        }

        return $this;
    }

    /**
     * Sets background color and removes alpha channel.
     *
     * @param  null|string $backgroundColor
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function removeAlpha($backgroundColor = null)
    {
        $backgroundColor = ($backgroundColor === null ? 'white' : $backgroundColor);

        try {
            $this->imagick->setBackgroundColor($backgroundColor);
            $this->imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Could not remove alpha channel: %s', (string) $e->getMessage())
            );
        }

        return $this;
    }

    /**
     * Removes (flattens) layers.
     *
     * @throws MagickException
     *
     * @returns $this
     */
    public function removeLayers()
    {
        try {
            $this->imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Could not remove layers: %s', (string) $e->getMessage())
            );
        }

        return $this;
    }

    /**
     * Remove excess metadata.
     *
     * @throws MagickException
     *
     * @return $this
     */
    public function removeMeta()
    {
        try {
            $this->imagick->stripImage();
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Could not remove image metadata: %s', (string) $e->getMessage())
            );
        }

        return $this;
    }

    public function convertColorspace($colorspace)
    {
        try {
		$this->imagick->transformimagecolorspace($colorspace);
        } catch(\Exception $e) {
            throw new MagickException(
                sprintf('Could not modify image colorspace to %s: %s', (string) $colorspace, (string) $e->getMessage())
            );
        }

        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function convertFormat($format)
    {
        $this->imagick->setImageFormat($format);

        return $this;
    }

    /**
     * @param int $units
     *
     * @return $this
     */
    public function setUnits($units)
    {
        $this->imagick->setImageUnits($units);

        return $this;
    }

    /**
     * @param int $quality
     *
     * @return $this
     */
    public function setCompressionQuality($quality)
    {
        $quality = ($quality < 1 ? 1 : $quality);
        $quality = ($quality > 100 ? 100 : $quality);

        $this->imagick->setImageCompressionQuality($quality);

        return $this;
    }

    /**
     * @return array
     */
    public function getResolution()
    {
        $resolution = $this->imagick->getImageResolution();

        return [
            $resolution['x'],
            $resolution['y']
        ];
    }

    /**
     * @return array
     */
    public function getGeometry()
    {
        $geometry = $this->imagick->getImageGeometry();

        return [
            $geometry['width'],
            $geometry['height']
        ];
    }

    /**
     * @param int  $max_x
     * @param int  $max_y
     * @param bool $bestFit
     * @param      $filter
     *
     * @return $this
     */
    public function scaleImageMax($max_x, $max_y, $bestFit = true, $filter = Imagick::FILTER_LANCZOSRADIUS)
    {
        list($x, $y) = $this->getGeometry();

        if ($x <= $max_x && $y <= $max_y) {
            return $this;
        }

        if ($x > $y) {
            $newX = $max_x;
            $newY = $newX / $x * $y;
        } else {
            $newY = $max_y;
            $newX = $newY / $y * $x;
        }

        $this
            ->imagick
            ->resizeImage($newX, $newY, $filter, 0.88549061701764, $bestFit);

        return $this;
    }

    /**
     * @param $geometry
     * @param $filter
     *
     * @return $this
     */
    public function createThumbnail($geometry, $filter = Imagick::FILTER_LANCZOSRADIUS)
    {
        list($x, $y) = $this->getGeometry();

        if ($x > $y) {
            $newX  = $y;
            $newY  = $y;
            $cropX = ($x - $newX) / 2;
            $cropY = 0;
        } elseif ($y > $x) {
            $newX  = $x;
            $newY  = $x;
            $cropX = 0;
            $cropY = ($y - $newY) / 2;
        } else {
            $newX  = $x;
            $newY  = $y;
            $cropX = 0;
            $cropY = 0;
        }

        $this
            ->imagick
            ->cropImage($newX, $newY, $cropX, $cropY);

        $this
            ->imagick
            ->setImagePage($newX, $newY, 0, 0);

        $this->scaleImageMax($geometry, $geometry);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlob()
    {
        return $this
            ->imagick
            ->getImageBlob();
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this
            ->imagick
            ->getImageLength();
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this
            ->imagick
            ->getImageFormat();
    }

    /**
     * @return Imagick
     */
    public function getImagick()
    {
        return $this->imagick;
    }

    /**
     * Destroy image instance on object running out of scope.
     */
    public function __destruct()
    {
        $this
            ->imagick
            ->destroy();
    }
}

/* EOF */
