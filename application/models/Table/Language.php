<?php
/**
 * Maps the table Language
 * @author damills
 *
 */
class Table_Language extends Zend_Db_Table_Abstract {

	protected $_name    = 'Language';
	protected $_primary = 'id';
	
	/**
	 * Gets the languages from all OCW
	 * @return multitype:
	 */
	public function getOCWLanguagesFilter() {
		$select = $this->select()->setIntegrityCheck( false );
		$select ->from(array('r0'=> 'Language'), array(new Zend_Db_Expr("'Language' as filterGroup"), 'r0.lanName As filterName', 'r0.id AS idFilter'))
				->joinInner(array('r1'=> 'OCW'), 'r1.idLanguage = r0.id', '')
				->group('r0.id')
				->order('r0.lanName')
		;
		return $this->fetchAll($select)->toArray();
	}
}