<?php
class Entity_OCW {

	protected $_ocwTitleEncode = null;
	protected $_ocwID = null;
	protected $_db;
	
	public $id;
	public $title;
	public $titleEncode;
	public $description;
	public $keywords;
	public $type;
	public $typeName;
	public $thumbnail;
	public $licence;
	public $language;
	public $university;
	public $school;
	public $department;
	public $notes;
	public $autors = array();
	public $ownData;
	public $joins;
	public $category;
	public $template;
	public $draft;
	
	
	public function __construct($ocw)
	{
		if(!is_null($ocw) && is_string($ocw)) {
			$this->_ocwTitleEncode = $ocw;
			$this->_ocwID = null;
			$this->_db = new Table_OCW();
			$this->set();
		} elseif ( !is_null($ocw) && is_integer($ocw) ) {
			$this->_ocwTitleEncode = null;
			$this->_ocwID = $ocw;
			$this->_db = new Table_OCW();
			$this->set();
		} else {
			throw new Exception("invalid title encode");
		}
	}
	
	private function set(){
		$select = $this->_db->select()->setIntegrityCheck(false);
		$select->from(  array('r0'=>'OCW') ,
				array('r0.id'
						, 'r0.ocwTitle'
						, 'r0.ocwTitleEncode'
						, 'r0.ocwGolive'
						, 'r0.ocwDescription'
						, 'r0.ocwKeywords'
						, 'r0.idType'
						, 'r0.thumbnail'
						, 'r0.ocwLicense'
						, 'r0.ocwNotes'
						, 'r0.ocwTemplate'
						, 'r0.ocwDraft'
					)
		)
		->joinInner(array('r1'=>'OCWTypes'), 'r1.id=r0.idType', array('r1.typName'))
		->joinInner(array('r8'=>'Language'), "r8.id = r0.idLanguage", array('r8.lanName'))
		->joinLeft(array( 'r2' =>'University' ), 'r2.id = r0.idUniversity', array('uniName') )
		->joinLeft(array( 'r3' =>'School' ), 'r3.id = r0.idSchool', array('schName') )
		->joinLeft(array( 'r4' =>'Department' ), 'r4.id = r0.idDepartment', array('depName') )
		->where("r0.ocwGolive = ?", 1); // published
		
		if(!is_null($this->_ocwTitleEncode) && is_string($this->_ocwTitleEncode) ){
			$select->where('r0.ocwTitleEncode = ?', $this->_ocwTitleEncode );
		} else {
			$select->where('r0.id = ?', $this->_ocwID );
		}
		$pd = $this->_db->fetchRow($select);
		
		if(is_null($pd)){
			throw new Exception('Invalid ocw title, no resultset ' . $select->assemble() );
		}
		// Joins
		$OCWJoin 			= new Table_OCWJoin();
		$Author				= new Table_Author();
		$ownData			= null;
		
		if($pd->idType == 5 || $pd->idType == 7){
			$ownData = null;
		} else {
			$ownClassName		= 'Table_'.$pd->typName;
			$ownClass			= new $ownClassName;
			$data 				= $ownClass->fetchRow('idOCW = '. $pd->id);
			if ($data){
				$ownData			= $data->toArray();
			} 
		}
		$Category			= new Table_Category();
		$OCWCategory		= new Table_OCWCategory();
		
		$catid				= $OCWCategory->fetchAll( $OCWCategory->select()->where('idOCW = ?', $pd->id),'occSequence ASC' );
		$paths				= array();
		
		foreach ( $catid as $cat ) {
			$tmp = $Category->getPath( $cat->idCat );
			if(is_array($tmp)){
				$paths[] = $tmp[0];
			}
		}
		
		$this->id 			= $pd->id;
		$this->title 		= $pd->ocwTitle;
		$this->titleEncode 	= $pd->ocwTitleEncode;
		$this->description	= $pd->ocwDescription;
		$this->keywords		= $pd->ocwKeywords;
		$this->type			= $pd->idType;
		$this->typeName		= $pd->typName;
		$this->thumbnail	= $pd->thumbnail;
		$this->licence		= $pd->ocwLicense;
		$this->language		= $pd->lanName;
		$this->university	= $pd->uniName;
		$this->school		= $pd->schName;
		$this->department	= $pd->depName;
		$this->notes		= $pd->ocwNotes;
		$this->template		= $pd->ocwTemplate;
		$this->draft		= $pd->ocwDraft;
		$this->autors		= $Author->getAuthorOcw($pd->id)->toArray();
		$this->ownData		= $ownData; 
		$this->joins		= $this->getJoins($pd->id);
		$this->category		= $paths;
		
	}
	private function getJoins($ocwID){
		$OCWJoin = new Table_OCWJoin();
		$result = $OCWJoin->getJoins($ocwID);
		$return = array();
		foreach ($result as $ocw){
			$return[] = new Entity_OCW( (int)$ocw->id );
		}
		return $return;
		
	}
	/**
	 * Check whether a url is online
	 * @param string $url
	 * @param number $timeout
	 * @return boolean
	 */
	public function isAlive($url ="", $timeout = 30){
		
		if(!is_string($url) || empty($url)){
			return false;
		}
		$ch = curl_init();
	

		$opts = array(CURLOPT_RETURNTRANSFER => true, 
				CURLOPT_URL => $url,           
				CURLOPT_NOBODY => true, 		 
				CURLOPT_TIMEOUT => $timeout);  
		curl_setopt_array($ch, $opts);
	
		curl_exec($ch); 
	
		$retval = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($retval >= 400){ 
			$retval = false;
		} else {
			$retval = true;
		}
		curl_close($ch); 
		return $retval;
	}		
}