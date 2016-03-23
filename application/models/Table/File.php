<?php
/**
 * Clase que mapea a la tabla File
 */
class Table_File extends Zend_Db_Table_Abstract {
	protected $_name    = 'File';
	protected $_primary = 'id';
	
	public function getFields(){
		$sql = 'DESCRIBE File';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
}