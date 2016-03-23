<?php
class Admin_BlcController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->layout()->setLayout('admin');
	}
	
	public function indexAction()
	{
		// grilla de enlaces rotos
	}
	/**
	 * Grilla de links muertos
	 */
	public function blcgridAction(){
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$filters = Zend_Json_Decoder::decode($this->getRequest()->getParam('filters', '{}'));
		$where = '';
		if(!is_null($filters)){
			$where = buildWhere($filters) . $where;
		}
		$sEcho = $this->getRequest()->getParam('sEcho', 4);
		// sorting
		$iSortCol_0 = $this->getRequest()->getParam('iSortCol_0', 1);
		$sSortDir_0 = $this->getRequest()->getParam('sSortDir_0', 'asc');
		$sort = array('columna'=> $iSortCol_0 , 'direccion'=> $sSortDir_0);
		//Pagging
		$iDisplayLength=$this->getRequest()->getParam('iDisplayLength', 50); // limit
		$iDisplayStart = $this->getRequest()->getParam('iDisplayStart', 0); // offset
		$limit = array('limit'=>$iDisplayLength, 'offset'=> $iDisplayStart);
		//search de la grilla
		$sSearch = $this->getRequest()->getParam('sSearch', '');
		$BLinks = new Table_BrokenLinks();
		$links = $BLinks->getLinksGrid($where, $sort, $limit, $sSearch );
		printJsonGrid($links, $sEcho);
	}
	
	/**
	 * Ajax para eliminar
	 */
	public function deleteAction(){
		$this->_helper->layout()->setLayout('empty');
		$this->_helper->viewRenderer->setNoRender();
		$BLinks = new Table_BrokenLinks();
		try {
			$Id = $this->getRequest()->getParam('Id', null);
			if( is_null($Id) ){
				throw new Exception( 'Insufficient parameters' );
			}
			$resp = $BLinks->delete("id = $Id");
			$this->_forward('index', 'blc', 'admin');//echo Zend_Json_Encoder::encode(array('Id'=> $Id));
		} catch (Exception $e) {
			throw new Exception( $e->getMessage() );
		}
	}	
}