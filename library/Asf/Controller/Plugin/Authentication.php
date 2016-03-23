<?php
/**
 * 
 * @author damills
 *
 */
class Asf_Controller_Plugin_Authentication extends Zend_Controller_Plugin_Abstract
{
    private $_whitelist;

    public function __construct()
    {
        $this->_whitelist = array
        (
            'admin/auth/login',
            'admin/auth/logout',
        	'admin/auth/index',
        	'admin/auth/error' // sólo para propositos de testing
        );
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());
        $route = $module . '/' .$controller . '/' . $action; 

        if (in_array($route, $this->_whitelist)){ return; }
		if ( $module === 'default'){ return;  } // el módulo default no tiene seguridad
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { return; }
        $request->setActionName('login');
        $request->setControllerName('auth');
        $request->setModuleName('admin');
        self::setRequest($request);
        //self::setDispatched(false);
    }

    public function  routeShutdown (Zend_Controller_Request_Abstract $request)
    {

    }
}
?>