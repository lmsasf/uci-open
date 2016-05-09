<?php
require_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Db.php';	
require_once 'Zend/Config/Ini.php';

/**
 * This class gets all data from DB to create access lists
 * @package		application/models
 * @copyright	Aconcagua Software Factory 
 * @author		damills
 * @version		04/03/2010
 */
class AclModel {
	
	protected $_db;
	/**
	 * Load the ini configuration file and initializes and connects to the database
	 */
	public function __construct()
	{
		/*Load the configuration file ini */
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');		
		/*connects to the database*/
		$this->_db = Zend_Db::factory($config->resources->db);
		$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
	}
	/**
	 * Gets Roles
	 * @return array
	 */
	public function getRoles(){
		$select = $this->_db->select();
		$select->from('Role', array('roleName') );
		return $this->_db->fetchAll($select);
	}
	/**
	 * Get Resource
	 * @return array
	 */
	public function getRecursos(){
		$select = $this->_db->select();
		$select->from('Resource', array('resourceName') );
		return $this->_db->fetchAll($select);
	}	
	/**
	 * Get resources by users groups
	 * @return array:
	 */
	public function getRecursosXgrupos(){
		$select = $this->_db->select();
		$select->from(array('r0'=> 'Role'), array('r0.roleName'))
		->joinInner(array('r1'=>'RoleResource'), 'r1.idRole = r0.id', array())
		->joinInner(array('r2'=>'Resource'), 'r2.id = r1.idResource', array('r2.resourceName'))
		;
		$resultado = $this->_db->fetchAll($select);
		$acl = array();
		foreach ($resultado as $value){
			$acl[ $value->roleName ][] = $value->resourceName;
		}
		return $acl;
	
	}
	
}