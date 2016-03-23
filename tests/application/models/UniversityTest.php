<?php
/**
 * Test Unitario de OCWTypes
 * @author damills
 *
 */
class UniversityTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		// instancio la tabla
		$this->Table = new Table_University();
		// instancio y limpio el cache
		$frontendOptions = array ('lifetime' => 3600,'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => CACHE_PATH);
		$cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
		$cache->clean();
		Zend_Registry::set('cache', $cache);
	}
	
	public function testGetUniversityGrid1(){
		//Zend_Db_Statement_Interface
		$result = $this->Table->getUniversityGrid();
		if($result instanceof Zend_Db_Statement_Interface){
			$result = true;
		}		
	}
	
	public function testGetUniversityGrid2(){
		//Zend_Db_Statement_Interface
		$result = $this->Table->getUniversityGrid('AND u.id > 1');
		if($result instanceof Zend_Db_Statement_Interface){
			$result = true;
		}
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException5(){
		$rs = $this->Table->getUniversityGrid(45456);
	}	
	
	public function testFecthAll(){
		$result = $this->Table->fetchAll();
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}
	// getOCWUniversityFilter
	
	public function testgetOCWUniversityFilter(){
		$result = false;
		$result = $this->Table->getOCWUniversityFilter();
		if(is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}	

}