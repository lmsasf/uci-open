<?php
class Table_PersonDepartment extends Zend_Db_Table_Abstract {

	protected $_name    = 'PersonDepartment';
	protected $_primary = array('idPer','idDep');

}