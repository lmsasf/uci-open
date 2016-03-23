<?php

/**
* @copyright	Aconcagua Software Factory 
* @desc         Listas de control de acceso
* @author	damills 03/2010 - Modificada por mjose 07/2012
* @version	06/08/2012
*/

// Modelo ACL //
//require_once APPLICATION_PATH . '/../library/Core/DAL/common/user/AclDAL.php';

// DbTables //
/*require_once APPLICATION_PATH . '/../library/Core/DAL/common/DbTable/pcg/pcg_od_rolesModel.php';
require_once APPLICATION_PATH . '/../library/Core/DAL/common/DbTable/pcg/pcg_od_recursoModel.php';
require_once APPLICATION_PATH . '/../library/Core/DAL/common/DbTable/pcg/pcg_om_seg_usuario_grupoModel.php';
require_once APPLICATION_PATH . '/../library/Core/DAL/common/DbTable/pcg/pcg_od_seg_usuarioModel.php';*/
//$aclmodel = new Table_AclModel();
require_once APPLICATION_PATH . '/../application/models/Table/AclModel.php';
class Asf_Controller_Helper_Acl
{
    public $acl;

    public function __construct()
    {
        $this->acl = new Zend_Acl();
    }

    /**
    * @desc Setea en las ACL el listado de roles activos para el sistema
    * @return unknown_type
    */
   
    
    public function setRoles()
    {    	
    	try
    	{
    		$model = new AclModel();
    		$roles = $model->getRoles();
    		//meto el grupo anonimo por defecto
    		$this->acl->addRole(new Zend_Acl_Role('anonimus'));
    	
    		// recorro los grupos cargados en la DB
    	
    		foreach ($roles as $rol)
    		{
    			$this->acl->addRole(new Zend_Acl_Role( $rol->roleName ) );
    		}
    	}
    	catch (Exception $e)
    	{
    		throw new Exception('Ocurri&oacute; el siguiente error al traer los grupos:'. $e->getMessage());
    	}
    }

    /**
    * @desc Setea en las ACL el listado de recursos para el sistema
    * @return unknown_type
    */
  
    
    public function setResources()
    {
    	try
    	{
    		$model = new AclModel();
    		$recursos = $model->getRecursos();
    		foreach($recursos as $recurso)
    		{
    			$this->acl->addResource(new Zend_Acl_Resource( $recurso->resourceName ));
    		}
    	}
    	catch (Exception $e)
    	{
    		throw new Exception('Ocurri&oacute; el siguiente error al traer los recursos:'. $e->getMessage());
    	}
    	
    }

    /**
    * @desc Setea en las ACL el listado de privilegios
    * @return unknown_type
    */
    public function setPrivilages()
    {         
    	$model = new AclModel();
    	$reglas = $model->getRecursosXgrupos();
    	foreach ($reglas as $key => $value)
    	{
    		if(count($value) > 0 )
    		{
    			$this->acl->allow($key,null,$value);
    		}
    	}
    }

    /**
    * @desc Setea en las ACL los datos necesarios para verificación de permisos
    * @return unknown_type
    */
    public function setAcl()
    {
        $cache = Zend_Registry::get('cache');

        if ( ($rules = $cache->load('acl')) === false )
        { 
            // no hay cache
            $this->setRoles();
            $this->setResources();
            $this->setPrivilages();
            $rules = $this->acl;
            $cache->save($rules);
        }
    }
}

?>