<?php
/**
 * RoleResource mapping table
 * @author damills
 *
 */
class Table_RoleResource extends Zend_Db_Table_Abstract {

	protected $_name    = 'RoleResource';
	protected $_primary = array('idRole','idResource');
	
}