<?php

/*
 * This file is part of the Scribe Magick Bundle.
 *
 * (c) Scribe Inc. <https://scr.be/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scribe\MagickBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Scribe\Component\DependencyInjection\AbstractExtension;

/**
 * Class ScribeMagickExtension.
 */
class ScribeMagickExtension extends AbstractExtension
{
    /**
     * Load the configuration directives/files for this bundle.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->autoLoad($configs, $container, new Configuration(), 's.magick');
    }
}
