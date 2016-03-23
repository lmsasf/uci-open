<?php
class FileTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		// instancio la tabla
		$this->Table = new Table_File();
		// instancio y limpio el cache
		$frontendOptions = array ('lifetime' => 3600,'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => CACHE_PATH);
		$cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
		$cache->clean();
		Zend_Registry::set('cache', $cache);
	}
	
	public function testFecthAll(){
		$result = $this->Table->fetchAll();
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}
	
	public function testGetFields(){
		$result = $this->Table->getFields();
		if(is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}
}