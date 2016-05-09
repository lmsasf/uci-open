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
	 * List of Categories
	 */
	public function indexAction()
	{
		$this->view->headTitle('Categories :: List');
		$Category = new Table_Category();
		$this->view->assign( 'tree', $Category->treeAsHtml() );
	}
	/**
	 * Lift the addition of people form
	 * @throws Exception
	 */
	public function editcategoryAction(){
		 
		$Category = new Table_Category();
		// Get parameters
		try{
			$Id = $this->getRequest()->getParam('id', null);

			$accion = $this->getRequest()->getParam('accion', null);
			if(!is_null($Id) && is_null($accion)) {

				$this->view->headTitle('Category :: Edit');
				// Seeking customer data
				$rows = $Category->fetchRow($Category->select()->where('id = ?', $Id));
				if(!$rows){
					throw new Exception('Invalid ID: ');
				}
				$this->view->assign('Category', $rows);
				$this->view->assign('accion', 'edit');
			}
			if(is_null($Id)) { // new Person
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
			$this->_forward('index', 'category', 'admin');
		}
	}	
	/**
	 * Add or edit a category
	 * @throws Exception
	 */
	public function savecategoryAction(){
		$this->_helper->layout()->setLayout('empty');
		// don't need a view to render
		$this->_helper->viewRenderer->setNoRender();
		$Category = new Table_Category();
		// get parameters
		try {
			$Id 	 = $this->getRequest()->getParam('id'		, null);
			$accion  = $this->getRequest()->getParam('accion'	, null);
			$data	 = $this->getRequest()->getParam('data'		, null);

			if( !is_null($accion) && !is_null($data) ){ // add or edit
				$catRow = null;
				 
				if($accion === 'edit'){
					$catRow = $Category->fetchRow($Category->select()->where('id = ?', $Id));
					foreach($data as $dato){
						if($dato['campo']!== 'accion'){
							$catRow->$dato['campo'] = $dato['valor'];
						}
					}
					$idCat = $catRow->save();
				} else { // new
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
	 * Saves changes the category tree
	 * @throws Exception
	 */
	public function savetreeAction(){
		$this->_helper->layout()->setLayout('empty');
		// don't need a view to render
		$this->_helper->viewRenderer->setNoRender();
		$Category = new Table_Category();
		// get parameters
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
	 * Delete categories and subcategories related
	 * @throws Exception
	 */
	public function delcategoryAction(){
		$this->_helper->layout()->setLayout('empty');
		// don't need a view to render
		$this->_helper->viewRenderer->setNoRender();
		$Category = new Table_Category();
		// get parameters
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
