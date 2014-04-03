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

use Scribe\ImageMagickBundle\Exception\ImageMagickException;
use \Imagick;

/**
 * ImageMagick class
 */
class ImageMagick
{
    /**
     * read image as binary string data
     */
    const READ_METHOD_BINARY = 'binary';

    /**
     * read image as file resource
     */
    const READ_METHOD_RESOURCE = 'resource';

    /**
     * read image as a filepath
     */
    const READ_METHOD_FILEPATH = 'filepath';

    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * constructory
     */
    public function __construct()
    {
        $this->imagick = new Imagick;
    }

    /**
     * @param  mixed       $image
     * @param  string|null $file_name
     * @param  string      $method
     */
    public function readImage($image, $file_name = null, $method = self::READ_METHOD_BINARY)
    {
        if ($method === self::READ_METHOD_FILEPATH) {

            if (!is_readable($image)) {
                throw new ImageMagickException('The requested filepath is not readable: '.$image);
            }
            $this->imagick->readImage($image);

        } elseif ($method === self::READ_METHOD_RESOURCE) {

            if (!is_resource($image)) {
                throw new ImageMagickException('The passed image is not a file resource');
            }
            $this->imagick->readImageFile($image, $file_name);

        } elseif ($method === self::READ_METHOD_BINARY) {

            if (!is_string($image)) {
                throw new ImageMagickException('The passed image is not a string');
            } elseif ($file_name === null) {
                throw new ImageMagickException('You must pass a file name for binary type');
            }
            $this->imagick->readImageBlob($image, $file_name);
        } else {
            throw new ImageMagickException('Invalid method type provided '.$method);
        }

        $this->imagick->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $this->imagick->setImageFormat('jpeg');
        $this->safeSetResolution(120);
        $this->imagick->setImageCompressionQuality(90);
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
            $resolution['y'],
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
            $geometry['height'],
        ];
    }

    /**
     * @param  int $dpi
     * @return array
     * @throws ImageMagickException
     */
    public function safeSetResolution($dpi)
    {
        list($current_x, $current_y) = $this->getResolution();

        if ($dpi < $current_x) {
            $this
                ->imagick
                ->setImageResolution($dpi, $dpi)
            ;
        }

        return $this->getResolution();
    }

    public function scaleImageMax($max_x, $max_y, $bestFit = true)
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

        $this->imagick->resizeImage($newX, $newY, Imagick::FILTER_LANCZOS, 0.5, $bestFit);

        return $this->getGeometry();
    }

    public function createThumbnail($geometry)
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

        $this->scaleImageMax($geometry, $geometry, false);

        return $this->getGeometry();
    }

    public function getBlob()
    {
        return $this
            ->imagick
            ->getImageBlob()
        ;
    }

    public function getFilename()
    {
        return $this
            ->imagick
            ->getFilename()
        ;
    }

    public function getFilesize()
    {
        return $this
            ->imagick
            ->getImageLength()
        ;
    }

    public function getFormat()
    {
        return $this
            ->imagick
            ->getImageFormat()
        ;
    }

    public function getDirect()
    {
        return $this->imagick;
    }
}