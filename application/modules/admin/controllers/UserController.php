<?php
class Admin_UserController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->layout()->setLayout('admin');
	}
	/**
	 * User list
	 */
	public function indexAction()
	{
				
	}
	
	public function rolesAction()
	{

	}
	
	/**
	 * Edit users
	 */
	public function editAction() {
		
		$usuarios = new Table_User();
		$persons = new Table_Person();

	    $this->view->assign('persons', $persons->fetchAll($persons->select()->order('perFirstName')));
	    
	    $roles = new Table_Role();
	    $this->view->assign('roles', $roles->fetchAll($roles->select()->order('roleName')));
		// get parameters
		try{
			$id = $this->getRequest()->getParam('id', null);
			$accion = $this->getRequest()->getParam('accion', null);			

			if(!is_null($id)) {
				$this->view->assign('User', $usuarios->fetchRow('id='. $id ));
				$this->view->assign('accion', 'edit');
				$this->view->assign('id', $id);
			}
			if(is_null($id)){ // new user
				$this->view->assign('accion', 'add');
				$this->view->assign('id', null);
				$this->view->assign('User', null);
			}
					
		} catch (Exception $e){
			$mens = $e->getCode(); 
			if($mens == 23000)
				$this->view->assign('error', 'This users already exists');
			else 
				$this->view->assign('error', $e->getMessage());
			$this->_forward('index', 'user');
		}
	    
	}
	
	public function saveusuarioAction(){
		$this->_helper->layout()->setLayout('empty');
		// don't need a view to render
		$this->_helper->viewRenderer->setNoRender();
		$User = new Table_User();
		$tr = $User->getAdapter()->beginTransaction();
		try {
			$id 	 		= $this->getRequest()->getParam('id'		 , null);
			$accion  		= $this->getRequest()->getParam('accion'	 , null);
			$data	 		= $this->getRequest()->getParam('data'		 , null);
			
			if(!is_null($data) ){ 
				$UsuarioRow = null;
				 
				if(!empty($id) || $accion === 'edit'){
					$UsuarioRow = $User->fetchRow($User->select()->where('id = ?', $id));
				} else { 
					$UsuarioRow = $User->createRow();
				}
					
				$campos = array();
				
				foreach($data as $dato){					
					if($dato['campo']!== 'accion' && $dato['campo'] !== 'usrPassword' && $dato['campo'] !== '' ){
						$UsuarioRow->$dato['campo'] = $dato['valor'];
					}
					if($dato['campo'] == 'usrPassword'){
						$UsuarioRow->$dato['campo'] = md5($dato['valor']);						
					}					
				}

				$id = $UsuarioRow->save();
				
				$tr->commit();
				echo Zend_Json_Encoder::encode(array('id'=> $id));				
				
			} else {
				throw new Exception("Invalid parameters");
			}
	
		} catch (Exception $e){
			$mens = $e->getCode();
			if($mens == 23000)
				throw new Exception('This users already exists');//$this->view->assign('error', 'This users already exists');
			else
				$this->view->assign('mensaje', $e->getMessage());
			$this->_forward('index', 'user');
		}	
			
	}
	/**
	 * Delete
	 * @throws Exception
	 */
	
	public function delAction(){ 
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$usuario = new Table_User();
		try {
			$id = $this->getRequest()->getParam('id', null);	
			if( is_null($id) ){
				throw new Exception("invalid parameters.");
			}			
			$res= $usuario->delete("id = $id ");
			if($res){
				$this->view->assign('tipo', 'success');
				$this->view->assign('prefrase', 'Success');
				$this->view->assign('mensaje', 'User successfully deleted');
				$this->_forward('index', 'user','admin');
			}else{
				throw new Exception('Could not delete the specified record');
			}
			
			} catch (Exception $e){
				$this->view->assign('mensaje', $e->getMessage());
				$this->view->assign('tipo', 'error');
				$this->view->assign('prefrase', 'Error');
				$this->_forward('index', 'user','admin');				
			}
	}
	
	/**
	 * Method to populate the data grid users
	 */
		
	public function ajaxsourceAction(){		
		$this->_helper->layout()->setLayout('empty');
		// don't need a view to render
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
		//search
		$sSearch = $this->getRequest()->getParam('sSearch', '');
				
		$Usuario= new Table_User();
		$usuarios = $Usuario->getUsuarios($where, $sort, $limit, $sSearch );		
		printJsonGrid( $usuarios, $sEcho );
	}
	

}