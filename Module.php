<?php

namespace PgSelligent;

use Zend\ModuleManager\Feature;

class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Create controller plugin selligent
     * Use in controller as $this->selligent()->functionName($data);
     */
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'selligent' => function ($sm) {
                    $plugin = new Controller\Plugin\Selligent;
                    $plugin->setService($sm->getServiceLocator()->get('PgSelligent\Client\Selligent'));
                    return $plugin;
                },
            ),
        );
    }

}
