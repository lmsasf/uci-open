<?php
class AdminCategoryControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

	public function setUp()
	{
			
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		$this->markTestSkipped('revisar completo');
	}
	/**
	 *
	 */
	public function testIndexAction()
	{
		$params = array('action' => 'index', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->dispatch($url);

		// assertions
		$this->assertModule($urlParams['module']);
		$this->assertController($urlParams['controller']);
		$this->assertAction($urlParams['action']);
		//$this->assertQueryContentContains("div#welcome h3", "This is your project's main page");
	}
	
	public function testEditCategory(){

		$Table	= new Table_Category();
		$select = $Table->select();
		$select->from($Table, array('id'))
				->order('RAND()');
		$id = $Table->fetchRow($select);
		$params = array('action' => 'editcategory', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->getRequest()->setParam('id', $id->id);
// 		//d($this->getRequest()->getParams());
		$this->dispatch($url);
// 		// assertions
		$this->assertModule($urlParams['module']);
		$this->assertController($urlParams['controller']);
		$this->assertAction($urlParams['action']);
		
	}
	
	public function testEditCategory1(){
		$params = array('action' => 'editcategory', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->dispatch($url);
	
		// assertions
		$this->assertModule($urlParams['module']);
		$this->assertController($urlParams['controller']);
		$this->assertAction($urlParams['action']);
	
	}
	
	public function testEditCategoryFail(){
		$params = array('action' => 'editcategory', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->getRequest()->setParam('id', 'sahdas009a');
		d( is_numeric('sahdas009a') );
		$this->dispatch($url);
		// assertions
		$this->assertModule($urlParams['module']);
		$this->assertController($urlParams['controller']);
		$this->assertAction('index');
	
	}	
	
	/**
	 * enviar a guardar sin params, debe ocurrir un error
	 */
	public function testExceptionSaveCategory(){
		$params = array('action' => 'savecategory', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->dispatch($url);	
		$this->assertQueryContentContains("div#error", "Insufficient parameters");
	}
	/**
	 * Guardar una categoria nueva
	 */
	public function testSaveCategoryAdd(){
		try {
			$params = array('action' => 'savecategory', 'controller' => 'category', 'module' => 'admin');
			$urlParams = $this->urlizeOptions($params);
			$url = $this->url($urlParams);
			$data = array(
						 array('campo'=>'catName', 'valor'=> 'PHPUNIT Test Add Category')
						,array('campo'=>'accion' , 'valor'=> 'add')
					);
			$this->getRequest()->setParam('data'  , $data);
			$this->getRequest()->setParam('accion', 'add');
			$this->getRequest()->setParam('id'    , null);
			
			$this->dispatch($url);
			$json = $this->getResponse()->getBody();
			$array = Zend_Json_Decoder::decode($json);
			if(is_array($array)){
				//editar la categoria
				$params = array('action' => 'savecategory', 'controller' => 'category', 'module' => 'admin');
				$urlParams = $this->urlizeOptions($params);
				$url = $this->url($urlParams);
				$data = array(
						array('campo'=>'catName', 'valor'=> 'PHPUNIT Test Add Category Edited')
						,array('campo'=>'accion' , 'valor'=> 'edit')
				);
				$this->getRequest()->setParam('data'  , $data);
				$this->getRequest()->setParam('accion', 'edit');
				$this->getRequest()->setParam('id'    , $array['id']);
				$this->dispatch($url);
				$json = $this->getResponse()->getBody();
				$array = Zend_Json_Decoder::decode($json);				
				// borrar la categoria y de paso testear delete
				$params = array('action' => 'delcategory', 'controller' => 'category', 'module' => 'admin');
				$urlParams = $this->urlizeOptions($params);
				$url = $this->url( $urlParams );
				$this->getRequest()->setParam( 'id', $array['id'] );
				$this->dispatch($url);				
			}else{
				throw new Exception('Fallo al guardar categoria retorno:' . $json );
			}
			

		} catch (Exception $e ){
			$this->fail($e->getMessage());
		}
	}
	/**
	 * enviar a delete sin params, debe ocurrir un error
	 */
	public function testExceptionDeleteCategory(){
		$params = array('action' => 'delcategory', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->dispatch($url);
		$this->assertQueryContentContains("div#error", "Insufficient parameters");
	}
	public function testSaveTree(){
		$json = '[{"id":1,"name":"","children":[{"id":2,"name":"","children":[{"id":4,"name":""},{"id":3,"name":""},{"id":5,"name":""},{"id":12,"name":"","children":[{"id":55,"name":"","children":[{"id":54,"name":"","children":[{"id":14,"name":""}]}]}]},{"id":11,"name":"","children":[{"id":13,"name":""}]}]},{"id":15,"name":"","children":[{"id":17,"name":""},{"id":20,"name":""},{"id":16,"name":""}]},{"id":6,"name":"","children":[{"id":7,"name":"","children":[{"id":8,"name":""}]},{"id":9,"name":""},{"id":10,"name":""}]}]},{"id":21,"name":"","children":[{"id":22,"name":""}]},{"id":23,"name":"","children":[{"id":24,"name":""},{"id":25,"name":"","children":[{"id":27,"name":""}]},{"id":26,"name":""}]},{"id":28,"name":"","children":[{"id":29,"name":"","children":[{"id":30,"name":""}]},{"id":31,"name":"","children":[{"id":32,"name":""}]}]},{"id":33,"name":""},{"id":34,"name":"","children":[{"id":35,"name":""},{"id":36,"name":""}]},{"id":37,"name":"","children":[{"id":38,"name":""}]},{"id":39,"name":"","children":[{"id":40,"name":""}]},{"id":41,"name":"","children":[{"id":42,"name":""}]},{"id":43,"name":"","children":[{"id":44,"name":""},{"id":45,"name":""},{"id":46,"name":""}]},{"id":47,"name":"","children":[{"id":52,"name":"","children":[{"id":48,"name":"","children":[{"id":49,"name":""},{"id":50,"name":""},{"id":51,"name":""}]}]}]}]';
		$data = Zend_Json_Decoder::decode($json);
		
		$params = array('action' => 'savetree', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->getRequest()->setParam('data'  , $data);
		$this->dispatch($url);
		$json = $this->getResponse()->getBody();
		$array = Zend_Json_Decoder::decode($json);
		$this->assertTrue( $array['rebuild'] , 'Todo OK');
	}
	public function testSaveTreeFail(){
		$params = array('action' => 'savetree', 'controller' => 'category', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->dispatch($url);
		$this->assertQueryContentContains("div#error", "Insufficient parameters");
	}		

}