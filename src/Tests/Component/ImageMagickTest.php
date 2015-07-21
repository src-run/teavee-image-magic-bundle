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
use Scribe\Utility\UnitTest\AbstractMantleKernelTestCase;

/**
 * Class ImageMagickTest.
 */
class ImageMagickTest extends AbstractMantleKernelTestCase
{
    public function getImageFixtureBasePath()
    {
        return realpath(
            static::$staticContainer->getParameter('kernel.root_dir').
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

        $m->readImageIn('not-a-valid-file-path', ImageMagick::READ_METHOD_FILE_PATH, 'file-name');
    }

    public function testReadImageAsFilePath()
    {
        $m = $this->getM();

        $m
            ->readImageIn($this->getImageFixturePath1(),ImageMagick::READ_METHOD_FILE_PATH)
            ->setFormat('jpeg')
            ->flattenAndRemoveAlphaAndRgb()
            ->createThumbnail(300)
            ->stripAll()
        ;

        static::assertEquals(file_get_contents($this->getImageThumbnailFixturePath1()), $m->getBlob());
    }

    public function testReadImageExceptionResource()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\MagickBundle\Exception\MagickException');

        $m->readImageIn('not-a-resource', ImageMagick::READ_METHOD_RESOURCE, 'file-name');
    }

    public function testReadImageAsResource()
    {
        $m = $this->getM();

        $resource = fopen('https://static.scribenet.com/images/logo/scr-logo-web_1200.png', 'r');

        $m
            ->readImageIn($resource, ImageMagick::READ_METHOD_RESOURCE, 'src-logo.png')
            ->flattenAndRemoveAlphaAndRgb()
            ->scaleImageMax(400, 400, true)
            ->stripAll()
        ;

        static::assertEquals(file_get_contents($this->getImageThumbnailFixturePath2()), $m->getBlob());
    }

    public function testReadImageExceptionBlob()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\MagickBundle\Exception\MagickException');

        $m
            ->readImageIn('', ImageMagick::READ_METHOD_BINARY, null)
            ->setFormat('jpeg')
            ->flattenAndRemoveAlphaAndRgb()
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(50)
            ->stripAll()
        ;
    }

    public function testReadImageAsBlob()
    {
        $m = $this->getM();

        $image = file_get_contents('https://static.scribenet.com/web/v1/bundles/public/public-hero-fixed-background.jpg');

        $m
            ->readImageIn($image, ImageMagick::READ_METHOD_BINARY, 'public-hero-fixed-background.jpg')
            ->setFormat('jpeg')
            ->flattenAndRemoveAlphaAndRgb()
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(50)
            ->stripAll()
        ;

        static::assertEquals(file_get_contents($this->getImageFixtureBasePath().'public-hero-fixed-background.jpeg'), $m->getBlob());
        static::assertEquals(41666, $m->getFileSize());
        static::assertEquals('jpeg', $m->getFormat());
        static::assertEquals([1198, 617], $m->getGeometry());
        static::assertEquals([300, 300], $m->getResolution());

        $m2 = $this->getM();

        $m2
            ->readImageIn($image, ImageMagick::READ_METHOD_BINARY, 'public-hero-fixed-background-high-quality.jpg')
            ->setFormat('jpeg')
            ->flattenAndRemoveAlphaAndRgb()
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(100)
            ->stripAll()
        ;

        static::assertNotEquals(file_get_contents($this->getImageFixtureBasePath().'public-hero-fixed-background.jpeg'), $m2->getBlob());
    }

    public function testGetters()
    {
        $m = $this->getM();

        $m
            ->readImageIn($this->getImageFixturePath1(), ImageMagick::READ_METHOD_FILE_PATH, null)
            ->setFormat('png')
            ->flattenAndRemoveAlphaAndRgb()
            ->createThumbnail(400)
            ->stripAll()
        ;

        $m->getBlob();
        static::assertEquals(7450, $m->getFileSize());
        static::assertEquals('png', $m->getFormat());
        static::assertEquals([400, 400], $m->getGeometry());
    }
}

/* EOF */
