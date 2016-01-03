<?php

/*
 * This file is part of the Teavee Image Magic Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\Teavee\ImageMagicBundle\Tests\Processor\ImageMagick;

use Scribe\Teavee\ImageMagicBundle\Processor\ImageMagick\ImageMagickProcessor;
use Scribe\WonkaBundle\Utility\TestCase\KernelTestCase;

/**
 * Class ImageMagickProcessorTest.
 */
class ImageMagickProcessorTest extends KernelTestCase
{
    protected function getImageFixtureBasePath()
    {
        return realpath(
            self::$staticContainer->getParameter('kernel.root_dir') .
            '/../../.config/testers/fixtures/teavee-image-magic-bundle/images/'
        );
    }

    protected function getImageFixturePaths($fixture, $ext = null)
    {
        $basePath = $this->getImageFixtureBasePath() . DIRECTORY_SEPARATOR;
        $fixtureExtension = $ext === null ? pathinfo($fixture, PATHINFO_EXTENSION) : $ext;
        $fixtureBaseName = pathinfo($fixture, PATHINFO_FILENAME);
        $fixtureVersion = PHP_MAJOR_VERSION . PHP_MINOR_VERSION;

        $paths = [
            realpath($basePath . $fixture),
            realpath($basePath . sprintf('%s.php%s.%s', (string) $fixtureBaseName, (string) $fixtureVersion, (string) $fixtureExtension)),
        ];

        if ($paths[0] === false || $paths[1] === false) {
            throw new \Exception(sprintf('Could not get path for %s in base dir %s.', $fixture, $basePath));
        }

        return $paths;
    }

    /**
     * @return ImageMagickProcessor
     */
    protected function getM()
    {
        return static::$staticContainer->get('s.image_processor');
    }

    public function testReadImageExceptionFilePath()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\Teavee\ImageMagicBundle\Exception\ProcessorException');

        $m->readFile('not-a-valid-file-path', 'file-name');
    }

    public function testReadImageAsFilePath()
    {
        list($original, $expected) = $this->getImageFixturePaths('icon-innovation.png', 'jpg');
        $m = $this->getM();

        $m
            ->readFile($original)
            ->convertFormat(ImageMagickProcessor::FORMAT_JPEG)
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorSpace(ImageMagickProcessor::COLOR_SPACE_SRGB)
            ->createThumbnail(300);

        //file_put_contents($expected, $m->getBlob());
        static::assertEquals(file_get_contents($expected), $m->getBlob());
    }

    public function testReadImageExceptionResource()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\Teavee\ImageMagicBundle\Exception\ProcessorException');

        $m->readResource('not-a-resource', 'file-name');
    }

    public function testReadImageAsResource()
    {
        list($original, $expected) = $this->getImageFixturePaths('scr-logo-1200.png', 'jpg');
        $m = $this->getM();

        $resource = fopen($original, 'r');

        $m
            ->readResource($resource, 'src-logo.png')
            ->convertFormat(ImageMagickProcessor::FORMAT_JPEG)
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorSpace(ImageMagickProcessor::COLOR_SPACE_SRGB)
            ->scaleImageMax(400, 400, true);

        //file_put_contents($expected, $m->getBlob());
        static::assertEquals(file_get_contents($expected), $m->getBlob());
    }

    public function testReadImageExceptionBlob()
    {
        $m = $this->getM();

        $this->setExpectedException('Scribe\Teavee\ImageMagicBundle\Exception\ProcessorException');

        $m
            ->readBinary('', null)
            ->convertFormat(ImageMagickProcessor::FORMAT_JPEG)
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorSpace(ImageMagickProcessor::COLOR_SPACE_SRGB)
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(50);
    }

    public function testReadImageAsBlob()
    {
        list($original, $expected) = $this->getImageFixturePaths('public-hero-fixed-background.jpg');
        $m = $this->getM();

        $image = file_get_contents($original);

        $m
            ->readBinary($image, 'public-hero-fixed-background.jpg')
            ->convertFormat(ImageMagickProcessor::FORMAT_JPEG)
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorSpace(ImageMagickProcessor::COLOR_SPACE_SRGB)
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(50);

        //file_put_contents($expected, $m->getBlob());
        static::assertEquals(file_get_contents($expected), $m->getBlob());
        static::assertEquals(41546, $m->getFileSize());
        static::assertEquals('jpeg', $m->getFormat());
        static::assertEquals([1198, 617], $m->getGeometry());
        static::assertEquals([300, 300], $m->getResolution());

        $m2 = $this->getM();

        $m2
            ->readBinary($image, 'public-hero-fixed-background.jpg')
            ->convertFormat(ImageMagickProcessor::FORMAT_JPEG)
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorSpace(ImageMagickProcessor::COLOR_SPACE_SRGB)
            ->scaleImageMax(1200, 1200, true)
            ->setCompressionQuality(100);

        static::assertNotEquals(file_get_contents($expected), $m2->getBlob());
    }

    public function testGetters()
    {
        list($original, $expected) = $this->getImageFixturePaths('icon-innovation.png', 'jpg');
        $m = $this->getM();

        $m
            ->readFile($original, null)
            ->convertFormat(ImageMagickProcessor::FORMAT_PNG)
            ->removeAlpha()
            ->removeLayers()
            ->removeMeta()
            ->convertColorSpace(ImageMagickProcessor::COLOR_SPACE_SRGB)
            ->createThumbnail(400);

        static::assertNotNull($m->getBlob());
        static::assertEquals(7415, $m->getFileSize());
        static::assertEquals('png', $m->getFormat());
        static::assertEquals([400, 400], $m->getGeometry());
    }
}

/* EOF */
