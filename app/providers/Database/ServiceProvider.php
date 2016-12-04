<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Providers\Database;

use Phosphorum\Listeners\Database;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\Database\ServiceProvider
 *
 * @package Phosphorum\Providers\Database
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'db';

    /**
     * {@inheritdoc}
     * Database connection is created based in the parameters defined in the configuration file.
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $config = container('config')->database;
                $em     = container('eventsManager');

                $driver  = $config->drivers->{$config->default};
                $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $driver->adapter;

                $config = $driver->toArray();
                unset($config['adapter']);

                /** @var \Phalcon\Db\Adapter\Pdo $connection */
                $connection = new $adapter($config);

                $em->attach('db', new Database());

                $connection->setEventsManager($em);

                return $connection;
            }
        );
    }
}
