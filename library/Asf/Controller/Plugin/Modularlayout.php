<?php

/**
 * 
 * Setear un layout por módulo
 * @author damills
 *
 */
class Asf_Controller_Plugin_Modularlayout extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        Zend_Layout::getMvcInstance()->setLayout($request->getModuleName());
    }
}

?>