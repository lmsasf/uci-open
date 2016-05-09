<?php
/**
 * Class that maps to the table Lecture
 */
class Table_Lecture extends Zend_Db_Table_Abstract {
	protected $_name    = 'Lecture';
	protected $_primary = 'id';
	
	/**
	 * Get the list of table fields
	 * @return multitype:Ambigous <mixed, Ambigous <string, boolean, mixed>>
	 */
	public function getFields(){
		$sql = 'DESCRIBE Lecture';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}
	/**
	 * 
	 * @param string $idLecture
	 * @param string $ocwTitleEncode
	 * @param number $golive
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getLecture( $idLecture = null, $ocwTitleEncode = null, $golive = 1, $admin = null) {
		$select = $this->select()->setIntegrityCheck(false);
		$select ->from(array('r0'=>'OCW'), array('r0.id', 'r0.ocwTitle','r0.ocwTitleEncode','r0.thumbnail', 'r0.ocwLicense', 'r0.ocwDescription', 'r0.ocwKeywords', 'r0.ocwTemplate', 'r0.ocwDraft', 'r0.promoloc'))
					->joinInner(array('r5'=>'OCWCategory'), 'r5.idOCW = r0.id', array('r5.idCat'))
					->joinInner(array('r4'=>'OCWTypes'), 'r4.id = r0.idType', null)
					->joinInner(array('r14'=>'Lecture'), 'r14.idOCW = r0.id', array(new Zend_Db_Expr('r14.id as idLecture'), 'r14.lecControls', 'r14.lecAutoplay', 'r14.lecHeight', 'r14.lecWidth', 'r14.lecStart', 'r14.lecDuration', 'r14.lecEmbed','r14.lecVolume', 'r14.lecPublishedDate', 'r14.ocwUrlLecture', 'r14.ocwBypassUrlLecture') )
					->joinInner(array('r16'=>'Language'), 'r16.id = r0.idLanguage', array('r16.lanName'))
					->joinLeft(array('r15'=>'CreditType'), 'r15.id=r14.id', array('r15.type'))
					->joinLeft(array('r6'=>'Author'), 'r6.idOCW = r0.id', array('r6.idPer'))
					->joinLeft(array('r7'=>'Person'), 'r7.id = r6.idPer', array( new Zend_Db_Expr("CONCAT_WS(' ', TRIM(r7.perFirstName), TRIM(r7.perLastName) , GROUP_CONCAT(r9.degDescription)) as Author"),'r7.perUrlPersonal'))
					->joinLeft(array('r8'=>'AuthorOCW'), 'r8.idOCW = r0.id AND r8.idPer = r6.idPer', array())
					->joinLeft(array('r9'=>'Degree'), 'r9.id = r8.idDeg', null)
					->joinLeft(array('r1'=>'University'), 'r1.id = r0.idUniversity', array('r1.uniName'))
					->joinLeft(array('r2'=>'School'), 'r2.id = r0.idSchool', array('r2.schName'))
					->joinLeft(array('r3'=>'Department'), 'r3.id = r0.idDepartment', array('r3.depName'))
					->joinLeft(array('r10'=>'PersonDepartment'), 'r10.idPer = r7.id', array('r10.pedTitle'))
					->joinLeft(array('r11'=>'Department'), 'r11.id = r10.idDep', array(new Zend_Db_Expr('r11.depName AS authorDepartment' ) ) )
					->joinLeft(array('r12'=>'School'), 'r12.id = r11.idSchool', array(new Zend_Db_Expr('r12.schName As authorSchool' ) ) )
					->joinLeft(array('r13'=>'University'), 'r13.id = r12.idUniversity', array(new Zend_Db_Expr('r13.uniName As authorUniversity' ) ) )
				->where('r0.idType = ?', 3 )
				->group(array('r5.idCat', 'r6.idPer', 'r11.id', 'r0.id'))
				->order(array('r0.ocwTitle', 'r5.idCat', 'r6.autSequence' ))
		;
		if($admin!='y')
			$select ->where('r0.ocwGolive = ?', $golive);		
		if( !is_null($idLecture) ) {
			$select->where('r14.id = ?', $idLecture );
		}
		if( !is_null($ocwTitleEncode) ) {
			$select->where('r0.ocwTitleEncode = ?', $ocwTitleEncode );
		}
		if(is_null($idLecture) && is_null($ocwTitleEncode)) {
			return $this->fetchAll($select);
		} else {
			return $this->fetchAll($select);
		}
	}
}