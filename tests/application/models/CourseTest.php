<?php
class CourseTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		$this->Table = new Table_Course();

		// instancio y limpio el cache
		$frontendOptions = array ('lifetime' => 3600,'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => CACHE_PATH);
		$cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
		$cache->clean();
		Zend_Registry::set('cache', $cache);
	}

	public function testgetFields(){
		$result = $this->Table->getFields();
		if (is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}
	//getCourses( $idCourse = null, $ocwTitleEncode = null, $golive = 1 )
	public function testGetCourses(){
		$result = $this->Table->getCourses();
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');		

		$curso = $this->Table->fetchRow('1=1', 'RAND()');
		$result = $this->Table->getCourses($curso->id);
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
		
		$OCW = new Table_OCW();
		$ocw = $OCW->fetchRow('id = '. $curso->idOCW);
		$result = $this->Table->getCourses(null, $ocw->ocwTitleEncode);
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');		
		
		
	}
}