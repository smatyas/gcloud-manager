<?php
/**
 * This file is part of the gcloud-manager package.
 *
 * (c) Mátyás Somfai <somfai.matyas@gmail.com>
 * Created at 2016.06.04.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Knp\Provider\ConsoleServiceProvider;

$app = new Silex\Application();

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'gcloud-manager',
    'console.version'           => '0.1.0',
    'console.project_directory' => __DIR__.'/..'
));

return $app;
