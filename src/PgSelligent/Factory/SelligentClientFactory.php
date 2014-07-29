<?php

namespace PgSelligent\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use PgSelligent\Client\Selligent;

class SelligentClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['selligent'])) {
            throw new \RuntimeException(
                'No config was found for PgSelligent Module. Did you copy the `pg-selligent.global.php` file to your autoload folder?'
            );
        }

        return new Selligent($config['selligent']);
    }
}
