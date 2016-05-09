<?php
/**
 * Maps to the Collection table
 */
class Table_Collection extends Zend_Db_Table_Abstract {
	protected $_name    = 'Collection';
	protected $_primary = 'id';
	
	public function getFields() {
		$sql = 'DESCRIBE Collection';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
}