<?php
require_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Db.php';	
require_once 'Zend/Config/Ini.php';

/**
 * Esta clase obtiene de la db todos los datos para crear las listas de acceso
 * @package		application/models
 * @copyright	Aconcagua Software Factory 
 * @author		damills
 * @version		04/03/2010
 */
class AclModel {
	
	protected $_db;
	/**
	 * Carga el archivo de configuracion ini e inicializa y conecta a la Base de Datos
	 */
	public function __construct()
	{
		/*Carga el archivo de configuracion ini */
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');		
		/*conecta a la Base de Datos*/
		$this->_db = Zend_Db::factory($config->resources->db);
		$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
	}
	/**
	 * Obtiene los Roles
	 * @return array
	 */
	public function getRoles(){
		$select = $this->_db->select();
		$select->from('Role', array('roleName') );
		return $this->_db->fetchAll($select);
	}
	/**
	 * Obtiene los recursos
	 * @return array
	 */
	public function getRecursos(){
		$select = $this->_db->select();
		$select->from('Resource', array('resourceName') );
		return $this->_db->fetchAll($select);
	}	
	/**
	 * Obtiene los recursos por grupo de usuarios
	 * @return array:
	 */
	public function getRecursosXgrupos(){
		$select = $this->_db->select();
		$select->from(array('r0'=> 'Role'), array('r0.roleName'))
		->joinInner(array('r1'=>'RoleResource'), 'r1.idRole = r0.id', array())
		->joinInner(array('r2'=>'Resource'), 'r2.id = r1.idResource', array('r2.resourceName'))
		//->order('r0.grNombre')
		;
		$resultado = $this->_db->fetchAll($select);
		$acl = array();
		foreach ($resultado as $value){
			$acl[ $value->roleName ][] = $value->resourceName;
		}
		return $acl;
	
	}
	
}