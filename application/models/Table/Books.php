<?php
/**
 * Clase que mapea a la tabla Books
 */
class Table_Books extends Zend_Db_Table_Abstract {
	protected $_name    = 'Books';
	protected $_primary = 'idBooks';
        
        
        
        public function getFields(){
		$sql = 'DESCRIBE Books';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
        
        
}