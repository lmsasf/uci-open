<?php
/**
 * Test Unitario de OCW
 * @author damills
 *
 */
class OCWTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		// instancio la tabla
		$this->Table = new Table_OCW();
		// instancio y limpio el cache
		$frontendOptions = array ('lifetime' => 3600,'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => CACHE_PATH);
		$cache = Zend_Cache::factory('Output','File',$frontendOptions,$backendOptions);
		$cache->clean();
		Zend_Registry::set('cache', $cache);

	}
	public function testGetFields(){
		$rs = $this->Table->getFields();
		$this->assertTrue( is_array($rs), 'Result OK' );
	}
	public function testgetOcwGrid_sin_params(){
		//getOcwGrid($where = null, $sort=array(), $limit = array(), $sSearch='')
		$rs = $this->Table->getOcwGrid();
		$cursor = $rs['cursor'];
		$count	= $rs['count'];
		$countWhere = $rs['countWhere'];
		
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
	}
	
	public function testgetOcwGrid_con_params(){
		//getOcwGrid($where = null, $sort=array(), $limit = array(), $sSearch='')
		$rs = $this->Table->getOcwGrid('AND r0.id>1',array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0), 'algo buscar');
		$cursor = $rs['cursor'];
		$count	= $rs['count'];
		$countWhere = $rs['countWhere'];
	
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
	}	
	
	public function testgetOcwGrid_con_params_rol_3(){
		//getOcwGrid($where = null, $sort=array(), $limit = array(), $sSearch='')
		$rs = $this->Table->getOcwGrid('AND r0.id>1',array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0), 'algo buscar', 3);
		$cursor = $rs['cursor'];
		$count	= $rs['count'];
		$countWhere = $rs['countWhere'];
	
		$this->assertTrue($cursor instanceof Zend_Db_Statement_Interface, 'Result OK');
		$this->assertTrue(is_numeric($count), 'Result OK');
		$this->assertTrue(is_numeric($countWhere), 'Result OK');
	}
	public function testgetOcwGrid_con_params_rol_4(){
		//getOcwGrid($where = null, $sort=array(), $limit = array(), $sSearch='')
		$rs = $this->Table->getOcwGrid('AND r0.id>1',array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0), 'algo buscar', 4);
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
		$rs = $this->Table->getOcwGrid(12);
	}
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException2(){
		$rs = $this->Table->getOcwGrid('', null);
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException3(){
		$rs = $this->Table->getOcwGrid('', array('jj') );
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException4(){
		$rs = $this->Table->getOcwGrid('', array('columna'=>1, 'direccion'=>'ASC'), 45);
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException5(){
		$rs = $this->Table->getOcwGrid('', array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10));
	}	
	/**
	 * Enviar parámetros no válidos
	 * @expectedException        Exception
	 */
	public function testException6(){
		$rs = $this->Table->getOcwGrid('', array('columna'=>1, 'direccion'=>'ASC'), array('limit'=>10, 'offset'=>0 ), 878 );
	}

	//getOCWinfo
	public function testgetOCWinfo(){
		$OCW = new Table_OCW();
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');
		$result = $this->Table->getOCWinfo($random->id);
		//d($result);
		if($result instanceof Zend_Db_Table_Rowset_Abstract || $result instanceof Zend_Db_Table_Row || is_null($result) ){
			// pruebo el método sin params
			$result = $this->Table->getOCWinfo();
			if($result instanceof Zend_Db_Table_Rowset_Abstract || $result instanceof Zend_Db_Table_Row || is_null($result)){
				$result = true;
			}else{
				$result=false;
			}
		} else{
			$result=false;
		}
		$this->assertTrue( $result , 'Todo OK');
	}
	// getOcwRss
	
	public function testGetOcwRss(){
		$result = $this->Table->getOcwRss();
		//d($result);
		if($result instanceof Zend_Db_Table_Rowset_Abstract || $result instanceof Zend_Db_Table_Row || is_null($result) ){
			$result = $this->Table->getOcwRss('Collection');
			if($result instanceof Zend_Db_Table_Rowset_Abstract || $result instanceof Zend_Db_Table_Row || is_null($result) ){
				$result=true;
			}
		}
		$this->assertTrue( $result , 'Todo OK');
	}
	// removeCacheIndex($idType)
	public function testremoveCacheIndex(){
		$idType = array(1, 3, 4, 6);
		foreach ($idType as $type){
			$this->assertTrue( $this->Table->removeCacheIndex($type) , 'Todo OK'); 
		}
		try {
			$this->Table->removeCacheIndex(null);
		} catch (Exception $e ){
			$this->assertTrue( true , 'Todo OK');
		}
	}
	
	// removeFromCache($idOCW)
	public function testRemoveFromCache(){
		$select = $this->Table->select()->setIntegrityCheck(false);
		$select->from(array('r0'=> 'OCW'), array('r0.id', new Zend_Db_Expr("CONCAT(r0.ocwTitleEncode, '.html') html") ) )
				->joinInner(array('r1'=>'OCWTypes'), 'r1.id = r0.idType', array( new Zend_Db_Expr("CONCAT(LOWER(r1.typName), 's') type")))
				->where('r1.visibility = ?', 1)
				->where('r1.id <> ?', 2)
				->order('RAND()')
		;
		$ocw = $this->Table->fetchRow($select);
		// para poder borrar el cache hay que generarlo, para eso visito la página
		$url = 'http://'.$_SERVER['SERVER_NAME']. '/'. $ocw->type . '/' .$ocw->html;
		file_get_contents($url);
		// luego hago el test
		$this->assertTrue( $this->Table->removeFromCache($ocw->id) , 'Todo OK');
		// Testeo que falle pasandole null
		$this->assertTrue( !$this->Table->removeFromCache(null), 'OK');
		// No se genera cache para headers o file, debe fallar si le paso uno
		$ocw = $this->Table->fetchRow('idType = 2', 'RAND()');
		$this->assertTrue( !$this->Table->removeFromCache($ocw->id), 'OK');
		
	} 
	// getOCWSearch($search = null, $filter = 'all', $category = 'all', $language = 'all' )
	public function testgetOCWSearch(){
		// sin parámetros		
		$result = $this->Table->getOCWSearch();
		if($result instanceof Zend_Db_Select || $result instanceof Zend_Db_Table_Select ){
			$result=true;
		}
		$this->assertTrue( $result , 'Todo OK');	
		// pruebo con parámetros
		$OCWTypes = new Table_OCWTypes();
		$Category = new Table_Category();
		$Language = new Table_Language();	

		$type 	= $OCWTypes->fetchRow('1=1', 'RAND()');
		$cat 	= $Category->fetchRow('1=1', 'RAND()');
		$lan 	= $Language->fetchRow('1=1', 'RAND()');

		$result = $this->Table->getOCWSearch('hola', $type->typName, $cat->id, $lan->lanName);
		if($result instanceof Zend_Db_Select || $result instanceof Zend_Db_Table_Select ){
			$result=true;
		}		
	}
	
	
}