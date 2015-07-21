<?php
/*
 * This file is part of the Scribe Cache Bundle.
 *
 * (c) Scribe Inc. <source@scribe.software>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$projectRootPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in($projectRootPath . DIRECTORY_SEPARATOR . 'src')
;

return new Sami($iterator, [
    'theme'                => 'default',
    'title'                => 'scribe/cache-bundle',
    'build_dir'            => $projectRootPath . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'api',
    'cache_dir'            => $projectRootPath . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'sami',
    'default_opened_level' => 4,
]);

/* EOF */
