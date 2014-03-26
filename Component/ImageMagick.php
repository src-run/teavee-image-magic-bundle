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

        }
    }
}