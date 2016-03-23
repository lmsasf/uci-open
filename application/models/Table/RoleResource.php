<?php
/**
 * Mapeo de tabla Author
 * @author damills
 *
 */
class Table_RoleResource extends Zend_Db_Table_Abstract {

	protected $_name    = 'RoleResource';
	protected $_primary = array('idRole','idResource');
	
}