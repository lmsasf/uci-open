<?php
/**
 * Author mapping table
 * @author damills
 *
 */
class Table_Author extends Zend_Db_Table_Abstract {

	protected $_name    = 'Author';
	protected $_primary = array('idPer', 'idOCW');
	
	/**
	 * 
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getAuthorsRows(){
		$sql ="SELECT
					r0.id,
					CONCAT_WS(' ', r0.perLastName, r0.perFirstName ) authorName,
					GROUP_CONCAT( r2.degDescription ORDER BY r1.pdeSequence DESC SEPARATOR ', ') degree, r3.idOCW
				FROM Person r0
					LEFT JOIN PersonDegree r1 ON r0.id = r1.idPer
					LEFT JOIN Degree r2 ON r2.id = r1.idDeg
					LEFT JOIN Author r3 ON r3.idPer = r0.id
				GROUP by r0.id
				ORDER by  authorName";
		$select = $this->select()->setIntegrityCheck(false);
		$select->from(  array('r0'=>'Person') , 
						array('r0.id', new Zend_Db_Expr("CONCAT_WS(' ', r0.perLastName, r0.perFirstName ) authorName")
								, new Zend_Db_Expr("GROUP_CONCAT( cast(concat(r2.degDescription,'_',r2.id) as char(100)) ORDER BY r1.pdeSequence DESC SEPARATOR ', ') degree")
							  )
					)
				->joinLeft(array('r1'=>'PersonDegree'), "r0.id = r1.idPer", '')
				->joinLeft(array('r2'=>'Degree'), "r2.id = r1.idDeg",'')
				->group("r0.id")
				->order('authorName');
		return $this->fetchAll( $select );
	}
	/**
	 * Get the authors of all OCW
	 * @return multitype:
	 */
	public function getOCWAuthorFilter(){
		$select = $this->select()->setIntegrityCheck(false);
		$select ->from(array('r0'=>'Person'), array( new Zend_Db_Expr("'Author' as filterGroup"), new Zend_Db_Expr("CONCAT_WS(', ', perLastName, perFirstName , CONCAT('(', degDescription,')')) as filterName"), 'r0.id as idFilter' ))
				->joinLeft (array('r2'=> 'PersonDegree'), 'r2.idPer = r0.id')
				->joinLeft (array('r3'=> 'Degree'), 'r3.id = r2.idDeg')
				->joinInner(array('r1'=> 'Author'), 'r1.idPer = r0.id')
				->group('r0.id')
				->order(new Zend_Db_Expr("CONCAT_WS(', ', perLastName, perFirstName)"))
		;
		return $this->fetchAll($select)->toArray();
	}
	
	public function getAuthorOcw($id = null){
		if(is_null($id)){
			throw new Exception('invalid OCW id');
		}
		$select = $this->select()->setIntegrityCheck(false);
		$select->from(  array('r0'=>'Person') , 
						array('r0.id', new Zend_Db_Expr("CONCAT_WS(' ', r0.perLastName, r0.perFirstName ) authorName")
								, new Zend_Db_Expr("GROUP_CONCAT( r2.degDescription ORDER BY r1.pdeSequence DESC SEPARATOR ',') degreeDisplay") // agrego esto para usar esta funcion en el frontend
								, new Zend_Db_Expr("GROUP_CONCAT(cast(concat(r2.degDescription,'_',r2.id) as char(100)) ORDER BY r1.pdeSequence ASC SEPARATOR ',') degree")
								, new Zend_Db_Expr("GROUP_CONCAT(cast(concat(r4.idDeg) as char(100)) SEPARATOR ',') iddegree")
							  )
					)
				->joinLeft(array('r1'=>'PersonDegree'), "r0.id = r1.idPer", '')
				->joinLeft(array('r2'=>'Degree'), "r2.id = r1.idDeg",'')
				->joinInner(array('r3'=>'Author'), "r3.idPer = r0.id and r3.idOCW = $id ", array('r3.idOCW'))
				->joinLeft(array('r4'=>'AuthorOCW'), "r4.idDeg = r2.id and r4.idPer = r3.idPer and r4.idOCW = $id ",'')
				->group("r0.id")
				->order('r3.autSequence', 'r1.pdeSequence');
		return $this->fetchAll( $select );
	}
	
}