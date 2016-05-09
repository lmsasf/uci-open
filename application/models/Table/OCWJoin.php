<?php
class Table_OCWJoin extends Zend_Db_Table_Abstract {
	
	protected $_name    = 'OCWJoin';
	protected $_primary = array('idOCWParent', 'idOCWChild');
	
	/**
	 * Gets the OCW from the table
	 * @param integer $idParent
	 * @param boolean idType
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getJoins($idParent, $idType=null){
		$selectjoin = $this->select()
			->setIntegrityCheck(false)
			->from(array('r0'=> 'OCWJoin'))
			->joinInner( array('r1'=> 'OCW') , 'r0.idOCWChild = r1.id', array('r1.id','r1.ocwTitle','r1.ocwTitleEncode','r1.idType', 'r1.ocwDescription'))
			->joinInner( array('r5'=> 'OCWTypes')  , 'r1.idType = r5.id', array('r5.typName'))
			->joinInner( array('r6'=> 'Language')  , 'r6.id = r1.idLanguage', array('r6.lanName'))
			->joinLeft( array('r7'=> 'Course'), 'r7.idOCW = r1.id', array('r7.ocwBypassUrlCourse'))
			->joinLeft( array('r8'=> 'Lecture'), 'r8.idOCW = r1.id', array('r8.ocwBypassUrlLecture'))
			->joinLeft( array('r2'=> 'University') , 'r1.idUniversity = r2.id', array('r2.id as idUni', 'r2.uniName'))
			->joinLeft( array('r3'=> 'School') , 'r1.idSchool = r3.id', array('r3.id as idSch', 'r3.schName'))
			->joinLeft( array('r4'=> 'Department') , 'r1.idDepartment = r4.id', array('r4.id as idDep', 'r4.depName'))
			->joinLeft( array('r9'=> 'File') , 'r1.id = r9.idOCW', array('r9.ocwUrlFile'))
			->where('r0.idOCWParent = ?', $idParent)
			->where('r1.ocwGolive = ?', 1)
			->order('r0.joiSequence')
		;
		if(!is_null($idType)){
			$selectjoin->where('r1.idType = ?', $idType);
		}
		$joins = $this->fetchAll( $selectjoin ) ;
		return $joins;
	}
	/**
	 * Returns all OCW who are not
	 * en la tabla Join para un padre
	 * @param integer $idParent
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getNotJoins($idParent){
		$select = $this->select()->setIntegrityCheck(false)
					->from(array('r0'=>'OCW'), array('r0.id', 'r0.ocwTitle'))
					->joinInner(array('r5'=>'OCWTypes'), 'r0.idType = r5.id', array('r5.typName', 'r0.idType'))
					->joinLeft( array('r2'=> 'University') , 'r0.idUniversity = r2.id', array('r2.id as idUni', 'r2.uniName'))
					->joinLeft( array('r3'=> 'School') , 'r0.idSchool = r3.id', array('r3.id as idSch', 'r3.schName'))
					->joinLeft( array('r4'=> 'Department') , 'r0.idDepartment = r4.id', array('r4.id as idDep', 'r4.depName'))
					->where('r0.ocwGolive = ?', 1)
					->where(new Zend_Db_Expr(" NOT EXISTS (SELECT r1.idOCWChild FROM OCWJoin r1  WHERE (r0.id=r1.idOCWChild OR r1.idOCWParent=r0.id ) and r1.idOCWParent=$idParent)"))
					->order("r0.ocwTitle")
		;
		$return = $this->fetchAll( $select ) ;
		return $return;		
	}
	/**
	 * @param array $arrayType
	 * @param array $arrayExclude
	 * @param array $arrayUniversity
	 * @param array $arrayCategory
	 * @param array $arrayAuthor
	 * @param array $arrayLanguage
	 * @param string $keywords
	 * @return array:
	 */
	public function filterOCW($arrayType=array(), $arrayExclude = array(), $arrayUniversity=array(), $arrayCategory=array(), $arrayAuthor=array(), $arrayLanguage=array(), $keywords=''){
		$select = $this->select()->setIntegrityCheck(false);
		// Se excluyen de los resultados
		$select->from(array('r0'=>'OCW'), array('r0.id', 'r0.ocwTitle', 'r0.idType'))
				->joinInner(array('r2'=>'OCWTypes'), 'r0.idType = r2.id AND r2.visibility <> 0', array('r2.typName'))
				->joinLeft(array('r6'=>'OCWCategory'), 'r6.idOCW = r0.id',array('r6.idCat'))
				->joinLeft(array('r7'=>'Author'), 'r7.idOCW = r0.id', array('r7.idPer'))
				->joinLeft(array('r3'=>'University'), 'r0.idUniversity = r3.id', array('r3.uniName'))
				->joinLeft(array('r4'=>'School'), 'r0.idSchool = r4.id', array('r4.schName'))
				->joinLeft(array('r5'=>'Department'), 'r0.idDepartment = r5.id', array('r5.depName'))
				->where('r0.ocwGolive = ?', 1)
		;
		if(!empty($arrayType) && is_array($arrayType)){
			$type = implode(',', $arrayType);
			$select->where("r0.idType in($type)");
		}
		if(!empty($arrayExclude) && is_array($arrayExclude)){
			$exclude = implode(',', $arrayExclude);
			$select->where("r0.id not in($exclude)");
		}
		if(!empty($arrayUniversity) && is_array($arrayUniversity)){
			$university = implode(',', $arrayUniversity);
			$select->where("r0.idUniversity in($university)");
		}
		if(!empty($arrayCategory) && is_array($arrayCategory)){
			$category = implode(',', $arrayCategory);
			$select->where("r6.idCat in($category)");
		}
		if(!empty($arrayAuthor) && is_array($arrayAuthor)){
			$author = implode(',', $arrayAuthor);
			$select->where("r7.idPer in($author)");
		}
		if(!empty($arrayLanguage) && is_array($arrayLanguage)){
			$language = implode(',', $arrayLanguage);
			$select->where("r0.idLanguage in($language)");
		}
		if(!empty($keywords) && is_string($keywords)){
			$select->where("LOWER(r0.ocwTitle) LIKE '$keywords' OR LOWER(r0.ocwKeywords) LIKE '$keywords' " );
		}
		$select->group('r0.id')->order('r0.ocwTitle');
		//echo $select->assemble();
		return $this->fetchAll($select)->toArray();
	}
	/**
	 * Get the list of types that is associated in an OCW Joins
	 * @param string $ocwTitleEncode Titulo codificado
	 * @return array
	 */
	public function getTypesJoins($ocwTitleEncode, $idType ){
		$sql= "SELECT r2.id, r2.typName
				FROM OCWJoin r0
				INNER JOIN OCW r1 ON r0.idOCWChild = r1.id
				INNER JOIN OCWTypes r2 ON r1.idType = r2.id
				WHERE 1 AND 
				r0.idOCWParent = (SELECT id FROM OCW WHERE ocwTitleEncode ='$ocwTitleEncode' AND idType= $idType)
				AND r2.visibility = 1
				group by r2.id
				ORDER by typName";
		return $this->getAdapter()->fetchAll($sql);
		
	}
}