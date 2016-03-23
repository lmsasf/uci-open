<?php

include_once('Zend/Session/Namespace.php'); //descomentar en producción si es necesario
//require_once APPLICATION_PATH.'/../library/Core/DAL/common/user/AclDAL.php';

/**
* @copyright	Aconcagua Software Factory 
* @desc         Plugin de control de acceso
* @author	damills 03/2010 - Modificada por mjose 07/2012
* @version	06/08/2012
*/
class Asf_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    private $_whitelist;

    public function __construct()
    {
        // ACCTIONES PERMITIDAS POR LISTA BLANCA //
        $this->_whitelist = array
        (
        		'admin_auth_login',
        		'admin_index_index',
        		'admin_auth_logout',
        		'admin_auth_noauth',
        		'admin_ocw_getlicense',
        		'admin_auth_error' //Sólo para testing
        );
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $exist      = false;
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        //$acl = Zend_Registry::get('acl');
        // Comprueba existencia de recurso //
        if($dispatcher->isDispatchable($request))
        {
            $class  = $dispatcher->loadClass($dispatcher->getControllerClass($request));
            $method = $dispatcher->formatActionName($request->getActionName());
            $exist  = is_callable(array($class, $method));
        }
        if ( $request->getModuleName() === 'default'){ return;  } // el módulo default no tiene seguridad
        if($exist)
        {
            $permitido  = false;
            //$AclDAL     = new AclDAL();

            // Obtengo a donde se quiere acceder y se compone el recurso //
            $recurso = $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();
            // Se agrega recurso a la lista //
            //$AclDAL->insertRecurso($recurso); // Se agrega el recurso si no existe //

            // Si se encuentra en White list lo dejamos pasar //
            if (in_array($recurso, $this->_whitelist))
            {
                return true;
            }
            else
            {
                $permitido = false;
            }

           
            require_once APPLICATION_PATH . '/../application/models/Table/User.php';
            $us = new Table_User();
           // $usuario =  Table_User::getIdentity();
            
            $usuario = $us::getIdentity();//UsuarioModel::getIdentity();
           
            //verificar los recursos del usuario
           // $acl = Zend_Registry::get('acl');
           
            $cache  = Zend_Registry::get('cache');
            $acl    = $cache->load('acl');
                     
            if(is_object($usuario)){ // hago esta verificación por que si se pierde la session $usuario no es un objeto válido
            	if( !$acl->isAllowed($usuario->roleName,null,$recurso) ){
            		//echo $recurso;exit;
            		//echo "<h1>NO AUTORIZADO</h1>";//echo 'problema';exit;
            		$request->setModuleName('admin');
            		$request->setControllerName('auth');
            		$request->setActionName('noauth');
            	}
            } else {
            	$request->setModuleName('admin');
            	$request->setControllerName('auth');
            	$request->setActionName('noauth');
            }
        }
    }
}

?>