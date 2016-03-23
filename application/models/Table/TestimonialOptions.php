<?php
/**
 * Clase que mapea a la tabla TestimonialOptions
 */
class Table_TestimonialOptions extends Zend_Db_Table_Abstract {
	protected $_name    = 'TestimonialOptions';
	protected $_primary = array('groupId', 'tesOption');
	
	public function getFields(){
		$sql = 'DESCRIBE TestimonialOptions';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
	
}