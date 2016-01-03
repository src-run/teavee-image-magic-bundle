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

use Scribe\Teavee\ImageMagicBundle\Exception\ProcessorException;
use Scribe\Wonka\Exception\InvalidArgumentException;
use Scribe\Wonka\Utility\Error\DeprecationErrorHandler;

/**
 * ImageMagick class
 */
class ImageMagickProcessor implements ImageMagickProcessorInterface
{
    /**
     * @var \Imagick
     */
    protected $im;

    /**
     * @param bool $force
     *
     * @return $this
     */
    public function initialize($force = false)
    {
        if ($force === true || !($this->im instanceof \Imagick)) {
            $this->im = new \Imagick();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function deInitialize()
    {
        if ($this->im instanceof \Imagick) {
            $this->im->destroy();
        }

        return $this;
    }

    /**
     * @param mixed  $image
     * @param string $name
     *
     * @throws ProcessorException
     *
     * @returns $this
     */
    public function readBinary($image, $name)
    {
        $this->initialize(true);

        try {
            $this->im->readImageBlob($image, $name);
        } catch(\Exception $e) {
            throw new ProcessorException(
                'Could not read binary image %s: %s', null, $e,
                (string) $name, (string) $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @throws ProcessorException
     *
     * @returns $this
     */
    public function readFile($path, $name = null)
    {
        if (true !== is_readable($path)) {
            throw new ProcessorException('File path is not readable: %s', null, null, (string) $path);
        }

        if (null === $name || strlen($name) === 0) {
            $name = pathinfo($path, PATHINFO_FILENAME);
        }

        $this->initialize(true);

        try {
            $this->im->readImage($path);
        } catch(\Exception $e) {
            throw new ProcessorException(
                'Could not read image %s from path %s: %s', null, $e,
                (string) $name, (string) $path, (string) $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * @param resource    $resource
     * @param null|string $name
     *
     * @throws ProcessorException
     *
     * @returns $this
     */
    public function readResource($resource, $name = null)
    {
        if (true !== is_resource($resource)) {
            throw new ProcessorException('Invalid resource provided for %s', null, null, (string) $name);
        }

        $this->initialize(true);

        try {
            $this->im->readImageFile($resource, $name);
        } catch(\Exception $e) {
            throw new ProcessorException(
                'Unrecoverable error while reading resource %s: %s', null, $e,
                (string) $name, (string) $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * @param  null|string $backgroundColor
     *
     * @throws ProcessorException
     *
     * @returns $this
     */
    public function removeAlpha($backgroundColor = null)
    {
        $backgroundColor = ($backgroundColor === null ? 'white' : $backgroundColor);

        try {
            $this->im->setBackgroundColor($backgroundColor);
            $this->im->setImageAlphaChannel($this->getLibraryConstant('ALPHACHANNEL_REMOVE', null, 11));
        } catch(\Exception $e) {
            throw new ProcessorException('Could not remove alpha channel: %s', null, $e, (string) $e->getMessage());
        }

        return $this;
    }

    /**
     * @throws ProcessorException
     *
     * @returns $this
     */
    public function removeLayers()
    {
        try {
            $this->im->mergeImageLayers($this->getLibraryConstant('LAYERMETHOD_FLATTEN'));
        } catch(\Exception $e) {
            throw new ProcessorException('Could not remove layers: %s', null, $e, (string) $e->getMessage());
        }

        return $this;
    }

    /**
     * Remove excess metadata.
     *
     * @throws ProcessorException
     *
     * @return $this
     */
    public function removeMeta()
    {
        try {
            $this->im->stripImage();
        } catch(\Exception $e) {
            throw new ProcessorException('Could not remove image metadata: %s', null, $e, (string) $e->getMessage());
        }

        return $this;
    }

    /**
     * @param string|null $colorSpace
     *
     * @throws ProcessorException
     *
     * @return $this
     */
    public function convertColorSpace($colorSpace = null)
    {
        if (defined('self::COLOR_SPACE_' . $colorSpace)) {
            $colorSpace = constant('self::COLOR_SPACE_' . $colorSpace);
        }

        try {
		    $this->im->transformimagecolorspace($this->getLibraryConstant($colorSpace, self::COLOR_SPACE_SRGB));
        } catch(\Exception $e) {
            throw new ProcessorException(
                'Could not modify image color-space to %s: %s', null, $e,
                (string) $colorSpace, (string) $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * @param string   $format
     * @param int|null $compression
     *
     * @return $this
     */
    public function convertFormat($format, $compression = null)
    {
        $this->im->setImageFormat($format);

        if (null !== $compression) {
            $this->setCompressionQuality($compression);
        }

        return $this;
    }

    /**
     * @param int $units
     *
     * @return $this
     */
    public function setUnits($units)
    {
        $this->im->setImageUnits($units);

        return $this;
    }

    /**
     * @param int $quality
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setCompressionQuality($quality = 100)
    {
        if ($quality > 100 || $quality < 1) {
            throw new InvalidArgumentException('Invalid compression quality value provided of %s.', null, null, (string) $quality);
        }

        $this->im->setImageCompressionQuality($quality);

        return $this;
    }

    /**
     * @return array
     */
    public function getResolution()
    {
        $resolution = $this->im->getImageResolution();

        return [$resolution['x'], $resolution['y']];
    }

    /**
     * @return array
     */
    public function getGeometry()
    {
        $geometry = $this->im->getImageGeometry();

        return [$geometry['width'], $geometry['height']];
    }

    /**
     * @param int        $x
     * @param int        $y
     * @param bool       $fit
     *
     * @return $this
     */
    public function scaleImageMax($x, $y, $fit = true)
    {
        list($originalX, $originalY) = $this->getGeometry();

        if ($originalX <= $x && $originalY <= $y) {
            return $this;
        }

        if ($originalX > $originalY) {
            $resizeX = $x;
            $resizeY = $resizeX / $originalX * $originalY;
        } else {
            $resizeY = $y;
            $resizeX = $resizeY / $originalY * $originalX;
        }

        $filter = $this->getLibraryConstant(self::RESIZE_FILTER, self::RESIZE_FILTER_ALT);

        $this->im->resizeImage($resizeX, $resizeY, $filter, self::RESIZE_BLUR, $fit);

        return $this;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function createThumbnail($size)
    {
        list($originalX, $originalY) = $this->getGeometry();

        $resizeCropX = 0;
        $resizeCropY = 0;

        if ($originalX > $originalY) {
            $resizeX  = $originalY;
            $resizeY  = $originalY;
            $resizeCropX = ($originalX - $resizeX) / 2;
        } elseif ($originalY > $originalX) {
            $resizeX  = $originalX;
            $resizeY  = $originalX;
            $resizeCropY = ($originalY - $resizeY) / 2;
        } else {
            $resizeX  = $originalX;
            $resizeY  = $originalY;
        }

        $this->im->cropImage($resizeX, $resizeY, $resizeCropX, $resizeCropY);
        $this->im->setImagePage($resizeX, $resizeY, 0, 0);

        $this->scaleImageMax($size, $size);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlob()
    {
        return $this->im->getImageBlob();
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->im->getImageLength();
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->im->getImageFormat();
    }

    /**
     * @return \Imagick|null
     */
    public function getImagick()
    {
        return $this->im;
    }

    /**
     * @param null|string $constant
     * @param null|string $default
     * @param null|mixed  $defaultReturn
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    protected function getLibraryConstant($constant = null, $default = null, $defaultReturn = null)
    {
        if (notNullOrEmpty($constant) && defined('\Imagick::' . $constant)) {
            return constant('\Imagick::' . $constant);
        }

        if (notNullOrEmpty($default) && defined('\Imagick::' . $default)) {
            return constant('\Imagick::' . $default);
        }

        if (notNullOrEmpty($defaultReturn)) {
            return $defaultReturn;
        }

        throw new InvalidArgumentException(
            'No valid Imagick constant provided (constant:%s,default:%s,defaultRet:%s).', null, null,
            (string) $constant, (string) $default, (string) $defaultReturn
        );
    }
}

/* EOF */
