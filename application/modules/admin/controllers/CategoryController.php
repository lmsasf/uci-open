<?php
class Admin_CategoryController extends Zend_Controller_Action
{
	/**
	 * Init
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->layout()->setLayout('admin');
	}
	/**
	 * Listado de Categorias
	 */
	public function indexAction()
	{
		$this->view->headTitle('Categories :: List');
		$Category = new Table_Category();
		$this->view->assign( 'tree', $Category->treeAsHtml() );
	}
	/**
	 * Levanta el formulario de dición de personas
	 * @throws Exception
	 */
	public function editcategoryAction(){
		 
		$Category = new Table_Category();
		// Obtener parámetros
		try{
			$Id = $this->getRequest()->getParam('id', null);

			$accion = $this->getRequest()->getParam('accion', null);
			if(!is_null($Id) && is_null($accion)) { // viene del listado para editar

				$this->view->headTitle('Category :: Edit');
				// busco los datos del cliente
				$rows = $Category->fetchRow($Category->select()->where('id = ?', $Id));
				if(!$rows){
					throw new Exception('Invalid ID: ');
				}
				$this->view->assign('Category', $rows);
				$this->view->assign('accion', 'edit');
			}
			if(is_null($Id)) { // nueva Persona
				$this->view->headTitle('Category :: Add');
				$this->view->assign('accion', 'add');
				$this->view->assign('id', null);
				$this->view->assign('Category', null);
			}
		} catch (Exception $e ){
			if(APPLICATION_ENV == 'testing'){
				echo $e->getMessage();
			}else{
				$this->view->assign( 'error', $e->getMessage() );
			}
			//echo $e->getMessage();
			$this->_forward('index', 'category', 'admin');
		}
	}	
	/**
	 * Agregar o editar una categoria
	 * @throws Exception
	 */
	public function savecategoryAction(){
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$Category = new Table_Category();
		// Obtener parámetros
		try {
			$Id 	 = $this->getRequest()->getParam('id'		, null);
			$accion  = $this->getRequest()->getParam('accion'	, null);
			$data	 = $this->getRequest()->getParam('data'		, null);
			//d($degrees); exit();
			if( !is_null($accion) && !is_null($data) ){ // editar o añadir
				$catRow = null;
				 
				if($accion === 'edit'){
					$catRow = $Category->fetchRow($Category->select()->where('id = ?', $Id));
					foreach($data as $dato){
						if($dato['campo']!== 'accion'){
							$catRow->$dato['campo'] = $dato['valor'];
						}
					}
					$idCat = $catRow->save();
				} else { // nueva la agrego como root
					foreach($data as $dato){
						if($dato['campo']!== 'accion'){
							$name = $dato['valor'];
						}
					}	
					$ret   = 	$Category->addCategory($name, 0);	
					$idCat = 	$ret['id'];
				}
				echo Zend_Json_Encoder::encode(array('id'=> $idCat));
			} else {
				throw new Exception("Insufficient parameters");
			}
		} catch (Exception $e){
			echo( '<div id="error">' . $e->getMessage() . '</div>' );
		}
	}	
	/**
	 * Guarda los cambios del arbol de categorias
	 * @throws Exception
	 */
	public function savetreeAction(){
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$Category = new Table_Category();
		// Obtener parámetros
		try {
			$data	 = $this->getRequest()->getParam('data'		, null);
			if( !is_null($data) ){ 
				$ret= $Category->rebuildTree($data);
				echo Zend_Json_Encoder::encode(array('rebuild'=> $ret));
			} else {
				throw new Exception("Insufficient parameters");
			}
	
		} catch (Exception $e){
			echo( '<div id="error">' . $e->getMessage() . '</div>' );
		}
	}
	/**
	 * Elimina categoriay sub categorias asociadas
	 * @throws Exception
	 */
	public function delcategoryAction(){
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$Category = new Table_Category();
		// Obtener parámetros
		try {
			$idCat	 = $this->getRequest()->getParam('id'		, null);
			if( !is_null($idCat) ){
				$ret= $Category->delCategory($idCat);
				echo Zend_Json_Encoder::encode(array('delete'=> $ret));
			} else {
				throw new Exception("Insufficient parameters");
			}
	
		} catch (Exception $e){
			echo( '<div id="error">'. $e->getMessage() . '</div>' );
		}
	}	
	
}
