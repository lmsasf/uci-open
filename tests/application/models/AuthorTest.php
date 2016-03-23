<?php
class AuthorTest  extends Zend_Test_PHPUnit_ControllerTestCase {
	
	private $Table;
	
	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		$this->Table = new Table_Author();
		
	}
	public function testGetAuthorsRows(){
		$result = $this->Table->getAuthorsRows();
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}	
	
	public function testGetAuthorOcw(){
		$OCW = new Table_OCW();
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');		
		$result = $this->Table->getAuthorOcw($random->id);
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}	
	
	public function testGetAuthorOcw2(){
		try {
			$result = $this->Table->getAuthorOcw();
		} catch ( Exception $e){
			$this->assertTrue( true , 'Todo OK');
		}
	}

	public function testgetOCWAuthorFilter(){
		$result = $this->Table->getOCWAuthorFilter();
		if(is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}
	
}