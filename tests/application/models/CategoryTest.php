<?php
/**
 * Test Unitario de Categories
 * @author damills
 *
 */
class CategoryTest  extends Zend_Test_PHPUnit_ControllerTestCase {

	private $Table;

	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		$this->Table = new Table_Category();

	}
	/**
	 * testGetTree
	 * Solo puedo comprobar que lo que retorne sea
	 * una instancia de Zend_Db_Table_Rowset_Abstract
	 * 
	 */
	public function testGetTree(){
		try{
			if( $this->hayCategorias() ) {
				$result = $this->Table->getTree();
				if($result instanceof Zend_Db_Table_Rowset_Abstract){
					$result = true;
				}
				$this->assertTrue( $result , 'Result OK');
			} else {
				$this->markTestSkipped(
						'No hay categorias para testear.'
				);
			}
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	/**
	 * testGetTreeWithPaths
	 * Solo puedo comprobar que lo que retorne sea
	 * un array
	 *
	 */
	public function testGetTreeWithPaths(){
		try{
			if( $this->hayCategorias() ) {
				$result = $this->Table->getTreeWithPaths();
				if(is_array($result)){
					$result = true;
				}
				$this->assertTrue( $result , 'Result OK');
			} else {
				$this->markTestSkipped(
						'No hay categorias para testear.'
				);
			}
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	
	}
	/**
	 * testTreeAsHtml
	 * Verifico que el retorno sea un html con cierta clase
	 */
	public function testTreeAsHtml(){
		try {
			if( $this->hayCategorias() ) {
				$result = $this->Table->treeAsHtml();
				$result = strpos($result, 'dd-list') > 0 ? true : false;
				$this->assertTrue( $result , 'Result OK');
			}else{
				$this->markTestSkipped(
						'No hay categorias para testear.'
				);
			}			
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
		
	}
	/**
	 * testAddCategory
	 */
	public function testAddCategory(){
		try {
			// agrego la categoria
			$idCat = $this->Table->addCategory("Test Category", 0);
			$this->assertTrue( is_numeric( $idCat['id']) , 'Result OK');
			// borro la categoria
			$idCat = $this->Table->delCategory($idCat["id"]);
			$this->assertTrue( is_numeric( $idCat['id']) , 'Result OK');
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
		
	/**
	 * Agregar una categoría no válida 
	 * @expectedException        Exception
	 */
	public function testExceptionAddCategory(){
		$idCat = $this->Table->addCategory(null);
	}
	/**
	 * Agregar una categoría no válida
	 * @expectedException        Exception
	 */
	public function testExceptionAddCategory2(){
		$idCat = $this->Table->addCategory("Test Category", -1);
	}
	
	/**
	 * Borrar una categoria no valida
	 * @expectedException        Exception
	 */
	public function testExceptionDelCategory(){
		$idCat = $this->Table->delCategory(null);
	}	
	/**
	 * Borrar una categoria no valida
	 * @expectedException        Exception
	 */
	public function testExceptionDelCategory2(){
		$idCat = $this->Table->delCategory(0);
	}		
	/**
	 * Mover una categoria no valida
	 * @expectedException        Exception
	 */
	public function testExceptionMoveCategory(){
		$idCat = $this->Table->moveCategory(null, null, null);
	}
	/**
	 * Mover una categoria no valida
	 * @expectedException        Exception
	 */	
	public function testExceptionMoveCategory2(){
		$idCat = $this->Table->moveCategory(0, -1, -1);
	}
	/**
	 * Reconstruir el arbol
	 */
	public function testRebuildTree(){
		$json = '[{"id":"1","name":"","children":[{"id":"2","name":"","children":[{"id":"4","name":""},{"id":"3","name":""},{"id":"5","name":""},{"id":"12","name":"","children":[{"id":"55","name":"","children":[{"id":"14","name":"","children":[{"id":"6","name":"","children":[{"id":"7","name":""}]}]}]}]}]}]}]';
		$data = Zend_Json_Decoder::decode($json);
		$respuesta = $this->Table->rebuildTree($data);
		$this->assertTrue( $respuesta , 'Result OK');
	}	

	public function testGetPath(){
		try {
			$result =   $this->Table->getPath(null);
		} catch (Exception $e ){
			$this->assertTrue( true , 'Result OK');
		}
		$category = $this->Table->fetchRow('1=1', 'RAND()');
		$result =   $this->Table->getPath($category->id);
		$this->assertTrue( is_array($result) , 'Result OK');
	}
	// treeAsArray($idType)
	public function testTreeAsArray(){
		$OCWTypes = new Table_OCWTypes();
		$type = $OCWTypes->fetchRow('visibility=1','RAND()');
		$result = $this->Table->treeAsArray($type->id);
		$this->assertTrue( is_array($result) , 'Result OK');
		try {
			$result = $this->Table->treeAsArray(null);
		}catch (Exception $e){
			$this->assertTrue( true , 'Result OK');
		}
	}
	/**
	 * Comprueba si hay alguna categoria
	 */
	private function hayCategorias(){
		try {
			$result= $this->Table->fetchAll('1=1')->count();
			if($result > 0 ){
				return true;
			}else{
				return false;
			}			
		}catch (Exception $e){
			throw new Exception("Error inesperado al contar las categorias ERROR:". $e->getMessage());
		}
	}

	
	
	
}