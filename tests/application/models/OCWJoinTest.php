<?php
/**
 * Test Unitario de OCWJoin
 * @author damills
 *
 */
class OCWJoinTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		// instancio la tabla
		$this->Table = new Table_OCWJoin();
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
	
	// getJoins($idParent, $idType=null)
	public function testGetJoins(){
		// obtengo aleatoreamente un OCW
		$OCW = new Table_OCW();
		// cualquier OCW que sea de un tipo visible, estos son los que NO son HEADERS ni LABELS, estos types
		// no soportan por código Joins
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');
		// envío el parent sin idtype
		$result = false;
		$result = $this->Table->getJoins($random->id);
		// puede que el resultado sea NULL por que no tenga registros
		if($result instanceof Zend_Db_Table_Rowset_Abstract || is_null($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');		
		
	}
	public function testGetJoins2(){
		// obtengo aleatoreamente un OCW
		$OCW = new Table_OCW();
		$OCWTypes = new Table_OCWTypes();
		// cualquier OCW que sea de un tipo visible, estos son los que NO son HEADERS ni LABELS, estos types
		// no soportan por código Joins
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');
		$type = $OCWTypes->fetchRow('1=1', 'RAND()');
		// envío el parent con idtype
		$result = false;
		$result = $this->Table->getJoins($random->id, $type->id);
		// puede que el resultado sea NULL por que no tenga registros
		if($result instanceof Zend_Db_Table_Rowset_Abstract || is_null($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	
	}	
	
	// getNotJoins($idParent)
	
	public function testGetNotJoins(){
		$OCW = new Table_OCW();
		// cualquier OCW que sea de un tipo visible, estos son los que NO son HEADERS ni LABELS, estos types
		// no soportan por código Joins
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');
		$result = false;
		$result = $this->Table->getNotJoins($random->id);
		// puede que el resultado sea NULL por que no tenga registros
		if($result instanceof Zend_Db_Table_Rowset_Abstract || is_null($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');		
	}
	// getTypesJoins($ocwTitleEncode, $idType )
	public function testGetTypesJoins(){
		// obtengo aleatoreamente un OCW
		$OCW = new Table_OCW();
		$OCWTypes = new Table_OCWTypes();
		// cualquier OCW que sea de un tipo visible, estos son los que NO son HEADERS ni LABELS, estos types
		// no soportan por código Joins
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');
		$type = $OCWTypes->fetchRow('1=1', 'RAND()');
		// envío el parent con idtype
		$result = false;
		$result = $this->Table->getTypesJoins($random->ocwTitleEncode, $type->id);
		if(is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	
	}
	//filterOCW($arrayType=array(), $arrayExclude = array(), $arrayUniversity=array(), $arrayCategory=array(), $arrayAuthor=array(), $arrayLanguage=array(), $keywords='')
	/**
	 * Sin params
	 */
	public function testFilterOCW(){
		$result = false;
		$result = $this->Table->filterOCW();
		if(is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}	
	/**
	 * Con params
	 */
	public function testFilterOCW2(){
		$result = false;
		$OCW = new Table_OCW();
		$OCWTypes = new Table_OCWTypes();
		$University = new Table_University();
		$Category = new Table_Category();
		$Author = new Table_Author();
		$Language = new Table_Language();
		
		$random = $OCW->fetchRow('idType in( SELECT id FROM OCWTypes WHERE visibility = 1)', 'RAND()');
		$type = $OCWTypes->fetchRow('1=1', 'RAND()');
		$univ = $University->fetchRow('1=1', 'RAND()');
		$cat = $Category->fetchRow('1=1', 'RAND()');
		$auth = $Author->fetchRow('1=1', 'RAND()');
		$lang = $Language->fetchRow('1=1', 'RAND()');
		
		$arrayUniversity	=	array($univ->id);
		$arrayType 			= 	array($type->id);
		$arrayExclude 		= 	array($random->id);
		$arrayCategory 		= 	array($cat->id);
		$arrayAuthor		=	array($auth->idPer);
		$arrayLanguage		=	array($lang->id);
		$result = $this->Table->filterOCW($arrayType, $arrayExclude, $arrayUniversity, $arrayCategory, $arrayAuthor, $arrayLanguage, 'test');
		
		if(is_array($result)){
			$result = true;
		}
		$this->assertTrue( $result , 'Todo OK');
	}	
}