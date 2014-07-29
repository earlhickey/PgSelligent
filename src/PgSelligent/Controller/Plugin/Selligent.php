<?php

namespace PgSelligent\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use PgSelligent\Client\Selligent as SelligentClient;

/**
 *
 * Selligent Controller Plugin
 *
 * @author pG
 * @package Selligent
 * @since 04-03-2014
 *
 */
class Selligent extends AbstractPlugin
{
    protected $service;

    public function subscribe($recipient)
    {
        return $this->getService()->subscribe($recipient);
    }

    public function unsubscribe($recipient)
    {
        return $this->getService()->unsubscribe($recipient);
    }

    public function setListId($listId)
    {
        return $this->getService()->setListId($listId);
    }

    public function getService()
    {
        return $this->service;
    }

    public function setService(SelligentClient $service)
    {
        $this->service = $service;
        return $this;
    }
}
