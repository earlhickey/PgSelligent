<?php
/**
 * 
 * @author pG
 *
 */

namespace Selligent\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Selligent\Client\Selligent;

class SelligentClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['selligent'])) {
            throw new \RuntimeException(
                'No config was found for Selligent Module. Did you copy the `selligent.global.php` file to your autoload folder?'
            );
        }

        return new Selligent($config['selligent']);
    }
}