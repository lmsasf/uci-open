<?php
/**
 * Mapea la tabla Language
 * @author damills
 *
 */
class Table_Language extends Zend_Db_Table_Abstract {

	protected $_name    = 'Language';
	protected $_primary = 'id';
	
	/**
	 * obtiene los lenguajes de todos los OCW
	 * @return multitype:
	 */
	public function getOCWLanguagesFilter(){
// 		$sql ="SELECT 
// 					'Language' as filterGroup
// 					, r0.lanName
// 					, 0 as idFilter
// 				FROM Language r0 
// 				INNER JOIN OCW r1 ON r1.idLanguage = r0.id
// 				GROUP BY r0.id
// 			 	ORDER BY r0.lanName";
		$select = $this->select()->setIntegrityCheck( false );
		$select ->from(array('r0'=> 'Language'), array(new Zend_Db_Expr("'Language' as filterGroup"), 'r0.lanName As filterName', 'r0.id AS idFilter'))
				->joinInner(array('r1'=> 'OCW'), 'r1.idLanguage = r0.id', '')
				->group('r0.id')
				->order('r0.lanName')
		;
		return $this->fetchAll($select)->toArray();
	}
}