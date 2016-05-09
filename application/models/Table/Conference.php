<?php
/**
 * Class that maps to the Conference Table
 */
class Table_Conference extends Zend_Db_Table_Abstract {
	protected $_name    = 'Conference';
	protected $_primary = 'id';
	
	public function getFields(){
		$sql = 'DESCRIBE Conference';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
	
}