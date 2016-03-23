<?php
class Table_ContactUSSettings extends Zend_Db_Table_Abstract {
	protected $_name    = 'ContactUSSettings';
	protected $_primary = 'Id';

	public function getFields(){
		$sql = 'DESCRIBE ContactUSSettings';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
}
