<?php
/**
 * Clase que mapea a la tabla Collection
 */
class Table_Collection extends Zend_Db_Table_Abstract {
	protected $_name    = 'Collection';
	protected $_primary = 'id';
	
	public function getFields(){
		$sql = 'DESCRIBE Collection';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}

	// public function getCollection($idCollection = null, $ocwTitleEncode = null, $golive = 1, $admin = null){
	// 	$select = $this->select()->setIntegrityCheck(false);
	// 	$select->where("ocwTitleEncode = ?", $ocwTitleEncode);
	// 	return $this->fetchRow($select);
	// }
	
}