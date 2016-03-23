<?php
/**
 * Test Unitario de Person
 * @author damills
 *
 */
class PersonTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		// instancio la tabla
		$this->Table = new Table_Person();
		// instancio y limpio el cache
		$frontendOptions = array ('lifetime' => 3600,'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => CACHE_PATH);
		$cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
		$cache->clean();
		Zend_Registry::set('cache', $cache);

	}
	public function testgetPersonsGrid_sin_params(){
		//getOcwGrid($where = null, $sort=array(), $limit = array(), $sSearch='')
		$rs = $this->Table->getPersonsGrid();
		$cursor = $rs['cursor'];
		$count	= $rs['count'];
		$countWhere = $rs['countWhere'];
		
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
	}
	
	public function testgetPersonsGrid_con_params(){
		//getOcwGrid($where = null, $sort=array(), $limit = array(), $sSearch='')
		$rs = $this->Table->getPersonsGrid('AND Person.id>1',array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0), 'algo buscar');
		$cursor = $rs['cursor'];
		$count	= $rs['count'];
		$countWhere = $rs['countWhere'];
	
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException1(){
		$rs = $this->Table->getPersonsGrid(12);
	}
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException2(){
		$rs = $this->Table->getPersonsGrid('', null);
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException3(){
		$rs = $this->Table->getPersonsGrid('', array('jj') );
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException4(){
		$rs = $this->Table->getPersonsGrid('', array('columna'=>1, 'direccion'=>'ASC'), 45);
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException5(){
		$rs = $this->Table->getPersonsGrid('', array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10));
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException6(){
		$rs = $this->Table->getPersonsGrid('', array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0 ), 878 );
	}

}