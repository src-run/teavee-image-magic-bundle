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
    const READ_METHOD_FILEPATH = 'filepath';

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
     * @param mixed       $image
     * @param null|string $file_name
     * @param string      $method
     * @param string      $format
     * @param int         $quality
     *
     * @throws MagickException
     *
     * @return $this
     */
    public function readImage($image, $file_name = null, $method = self::READ_METHOD_BINARY, $format = 'jpeg', $quality = 100)
    {
        if ($method === self::READ_METHOD_FILEPATH) {

            if (false === is_readable($image)) {
                throw new MagickException(sprintf('The requested file path "%s" is not readable.', (string) $image));
            }

            $this->imagick->readImage($image);

        } elseif ($method === self::READ_METHOD_RESOURCE) {

            if (false === is_resource($image)) {
                throw new MagickException(sprintf('The passed image value is not a file resource in "%s".', get_class($this)));
            }

            $this->imagick->readImageFile($image, $file_name);

        } elseif ($method === self::READ_METHOD_BINARY) {

            if ($file_name === null) {
                throw new MagickException(sprintf('You must specify a file name when passing binary type to "%s".', get_class($this)));
            }

            $this->imagick->readImageBlob($image, $file_name);

        } else {

            throw new MagickException(sprintf('Invalid method type of "%s" provided to "%s".', $method, get_class($this)));

        }

        $this->setFormat($format);

        return $this;
    }

    /**
     * @return $this
     */
    public function flattenAndRemoveAlphaAndRgb()
    {
        $this->imagick->setImageBackgroundColor('white');
        $this->imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
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
        $resolution = $this
            ->imagick
            ->getImageResolution()
        ;

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
        $geometry = $this
            ->imagick
            ->getImageGeometry()
        ;

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
