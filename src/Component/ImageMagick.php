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
class ImageMagick
{
    /**
     * Read the image in as binary data from a string.
     *
     * @var string
     */
    const READ_METHOD_BINARY = 'binary';

    /**
     * Read the image in as a file resource.
     *
     * @var string
     */
    const READ_METHOD_RESOURCE = 'resource';

    /**
     * Read the image in from a file path.
     *
     * @var string
     */
    const READ_METHOD_FILE_PATH = 'filepath';

    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->imagick = new Imagick;
    }

    /**
     * Read an image file into the imagick library
     *
     * @param mixed  $imageContent
     * @param string $readMethod
     * @param string $imageName
     *
     * @throws MagickException
     *
     * @return $this
     */
    public function readImageIn($imageContent, $readMethod = self::READ_METHOD_BINARY, $imageName = null)
    {
        if ($readMethod === self::READ_METHOD_FILE_PATH) {

            if (false === is_readable($imageContent)) {
                throw new MagickException(sprintf('The requested file path "%s" is not readable.', (string) $imageContent));
            }

            $this->imagick->readImage($imageContent);

        } elseif ($readMethod === self::READ_METHOD_RESOURCE) {

            if (false === is_resource($imageContent)) {
                throw new MagickException(sprintf('The passed image value is not a file resource in "%s".', get_class($this)));
            }

            $this->imagick->readImageFile($imageContent, $imageName);

        } elseif ($readMethod === self::READ_METHOD_BINARY) {

            if ($imageName === null) {
                throw new MagickException(sprintf('You must specify a file name when passing binary type to "%s".', get_class($this)));
            }

            $this->imagick->readImageBlob($imageContent, $imageName);

        } else {

            throw new MagickException(sprintf('Invalid method type of "%s" provided to "%s".', $readMethod, get_class($this)));

        }

        $this->setFormat('jpeg');

        return $this;
    }

    /**
     * @return $this
     */
    public function flattenAndRemoveAlphaAndRgb()
    {
        $this->imagick->setImageBackgroundColor('white');
        $this->imagick->setImageAlphaChannel(defined(Imagick::ALPHACHANNEL_REMOVE) ? Imagick::ALPHACHANNEL_REMOVE : 11);
        $this->imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $this->imagick->transformimagecolorspace(Imagick::COLORSPACE_RGB);

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
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->imagick->setImageFormat($format);

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
            return $this->getGeometry();
        }

        if ($x > $y) {
            $newX = $max_x;
            $newY = $newX / $x * $y;
        } else {
            $newY = $max_y;
            $newX = $newY / $y * $x;
        }

        $this->imagick->resizeImage($newX, $newY, $filter, 0.88549061701764, $bestFit);

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
            ->cropImage($newX, $newY, $cropX, $cropY)
        ;

        $this
            ->imagick
            ->setImagePage($newX, $newY, 0, 0)
        ;

        $this->scaleImageMax($geometry, $geometry, false, $filter);

        return $this;
    }

    /**
     * @return $this
     */
    public function stripAll()
    {
        $this->imagick->stripImage();

        return $this;
    }

    /**
     * @return string
     */
    public function getBlob()
    {
        return $this
            ->imagick
            ->getImageBlob()
        ;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this
            ->imagick
            ->getImageLength()
        ;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this
            ->imagick
            ->getImageFormat()
        ;
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
        $this->imagick->destroy();
    }
}

/* EOF */
