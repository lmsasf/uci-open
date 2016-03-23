<?php
class RoleTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		$this->Table = new Table_Role();

		// instancio y limpio el cache
		$frontendOptions = array ('lifetime' => 3600,'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => CACHE_PATH);
		$cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
		$cache->clean();
		Zend_Registry::set('cache', $cache);
	}
	// getRoles($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
	public function testGetRoles(){
		// sin params
		$result = $this->Table->getRoles();
		$cursor = $result['cursor'];
		$count	= $result['count'];
		$countWhere = $result['countWhere'];
		
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
		// hacerlo fallar
		try {
			// sort no es array
			$result = $this->Table->getRoles(null, 'algo');
		} catch (Exception $e ){
			$this->assertTrue( true , 'Todo OK');
		}
		try {
			// limit no es array
			$result = $this->Table->getRoles(null, array('columna'=>1, 'direccion'=>'ASC'), 'sasas');
		} catch (Exception $e ){
			$this->assertTrue( true , 'Todo OK');
		}
		try {
			// where no es un string o null
			$result = $this->Table->getRoles(array());
		} catch (Exception $e ){
			$this->assertTrue( true , 'Todo OK');
		}
		// segundo caso de fallo, pruebo uno de los cuatro casos
		try {
			// where no es un string o null
			$result = $this->Table->getRoles(null, array());
		} catch (Exception $e ){
			$this->assertTrue( true , 'Todo OK');
		}
		
		// con parametros y limit
		$result = $this->Table->getRoles(null, array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0), 'test');
		$cursor = $result['cursor'];
		$count	= $result['count'];
		$countWhere = $result['countWhere'];
		
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
	}
}