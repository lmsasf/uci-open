<?php

namespace Asf\Controller; //php 5.3 compatible

abstract class Action extends \Zend_Controller_Action
{
    protected $_services = null;
    

    public function init()
    {
        parent::init();
        $this->_services = \Asf\Service\ServiceBroker::getInstance();
    }
}
