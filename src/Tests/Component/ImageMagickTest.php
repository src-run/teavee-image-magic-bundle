<?php

/*
 * This file is part of the Scribe Magick Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\MagickBundle\Tests\Component;

use Scribe\MagickBundle\Component\ImageMagick;
use Scribe\WonkaBundle\Utility\TestCase\KernelTestCase;

/**
 * Class ImageMagickTest.
 */
class ImageMagickTest extends KernelTestCase
{
    public function getImageFixtureBasePath()
    {
        return realpath(
            self::$staticContainer->getParameter('kernel.root_dir').
            '/../../../app/config/shared_public/tests/fixtures/ScribeMagickBundle/Component/'
        ).DIRECTORY_SEPARATOR;
    }

    public function getImageFixturePath1()
    {
        return realpath(
            $this->getImageFixtureBasePath().'icon-innovation.png'
        );
    }

    public function getImageThumbnailFixturePath1()
    {
        return
            $this->getImageFixtureBasePath().'icon-innovation-thumbnail.jpeg'
        ;
    }

    public function getImageFixturePath2()
    {
        return 'https://static.scribenet.com/images/logo/scr-logo-web_1200.png';
    }

    public function getImageThumbnailFixturePath2()
    {
        return
            $this->getImageFixtureBasePath().'scr-logo-0800.jpg'
        ;
    }

    /**
     * @return ImageMagick
     */
    public function getM()
    {
        return static::$staticContainer->get('s.magick');
    }

    public function testReadImageExceptionFilePath()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\MagickBundle\Exception\MagickException');

        $m->readFile('not-a-valid-file-path', 'file-name');
    }

    public function testReadImageAsFilePath()
    {
        $m = $this->getM();

        $m
            ->readFile($this->getImageFixturePath1())
            ->convertFormat('jpeg')
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorspace(ImageMagick::COLORSPACE_SRGB)
            ->createThumbnail(300);

        static::assertEquals(file_get_contents($this->getImageThumbnailFixturePath1()), $m->getBlob());
    }

    public function testReadImageExceptionResource()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\MagickBundle\Exception\MagickException');

        $m->readResource('not-a-resource', 'file-name');
    }

    public function testReadImageAsResource()
    {
        $m = $this->getM();

        $resource = fopen('https://static.scribenet.com/images/logo/scr-logo-web_1200.png', 'r');

        $m
            ->readResource($resource, 'src-logo.png')
            ->convertFormat('jpeg')
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorspace(ImageMagick::COLORSPACE_SRGB)
            ->scaleImageMax(400, 400, true);

        static::assertEquals(file_get_contents($this->getImageThumbnailFixturePath2()), $m->getBlob());
    }

    public function testReadImageExceptionBlob()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\MagickBundle\Exception\MagickException');

        $m
            ->readBinary('', null)
            ->convertFormat('jpeg')
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorspace(ImageMagick::COLORSPACE_SRGB)
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(50);
    }

    public function testReadImageAsBlob()
    {
        $m = $this->getM();

        $image = file_get_contents('https://static.scribenet.com/web/v1/bundles/public/public-hero-fixed-background.jpg');

        $m
            ->readBinary($image, 'public-hero-fixed-background.jpg')
            ->convertFormat('jpeg')
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorspace(ImageMagick::COLORSPACE_SRGB)
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(50);

        static::assertEquals(file_get_contents($this->getImageFixtureBasePath().'public-hero-fixed-background.jpeg'), $m->getBlob());
        static::assertEquals(41546, $m->getFileSize());
        static::assertEquals('jpeg', $m->getFormat());
        static::assertEquals([1198, 617], $m->getGeometry());
        static::assertEquals([300, 300], $m->getResolution());

        $m2 = $this->getM();

        $m2
            ->readBinary($image, 'public-hero-fixed-background-high-quality.jpg')
            ->convertFormat('jpeg')
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorspace(ImageMagick::COLORSPACE_SRGB)
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(100);

        static::assertNotEquals(file_get_contents($this->getImageFixtureBasePath().'public-hero-fixed-background.jpeg'), $m2->getBlob());
    }

    public function testGetters()
    {
        $m = $this->getM();

        $m
            ->readFile($this->getImageFixturePath1(), null)
            ->convertFormat('png')
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorspace(ImageMagick::COLORSPACE_SRGB)
            ->createThumbnail(400);

        $m->getBlob();
        static::assertEquals(7459, $m->getFileSize());
        static::assertEquals('png', $m->getFormat());
        static::assertEquals([400, 400], $m->getGeometry());
    }
}

/* EOF */
