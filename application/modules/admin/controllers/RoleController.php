<?php
class Admin_RoleController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->layout()->setLayout('admin');
	}
	/**
	 * Listado de roles
	 */
	public function indexAction()
	{
				
	}
	
	public function rolesAction()
	{

	}
	
	/**
	 * Editar roles
	 */
	public function editAction(){
		
		$roles = new Table_Role();
		$resources = new Table_Resource();		
		$roleresource = new Table_RoleResource();
	    $this->view->assign('resources', $resources->fetchAll($resources->select()->order('nickName')));
	    $this->view->assign('roleresources', $roleresource->fetchAll($roleresource->select()));
		// Obtener parámetros
		try{
			$id = $this->getRequest()->getParam('id', null);
			$accion = $this->getRequest()->getParam('accion', null);			
						
			if(!is_null($id) /*&& is_null($accion)*/) { 
				$this->view->assign('Role', $roles->fetchRow('id='. $id ));
				$this->view->assign('accion', 'edit');
				$this->view->assign('id', $id);
			}
			if(is_null($id)){ // nuevo rol
				$this->view->assign('accion', 'add');
				$this->view->assign('id', null);
				$this->view->assign('Role', null);
			}
					
		} catch (Exception $e){
			$mens = $e->getCode(); 
			if($mens == 23000)
				$this->view->assign('error', 'This role already exists');
			else 
				$this->view->assign('error', $e->getMessage());
			$this->_forward('index', 'user');
		}
	    
	}
	
	public function saveroleAction(){
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$Role = new Table_Role();
		$RoleResource = new Table_RoleResource();
		$tr = $Role->getAdapter()->beginTransaction();
		try {
			$id 	 		= $this->getRequest()->getParam('id'		 , null);
			$accion  		= $this->getRequest()->getParam('accion'	 , null);
			$data	 		= $this->getRequest()->getParam('data'		 , null);
			$resource	 	= $this->getRequest()->getParam('resource'	 , null);
			
			//echo "<pre>";var_dump($resource);exit;
			
			if(!is_null($data) ){ 
				$RoleRow = null;
				 
				if(!empty($id) || $accion === 'edit'){
					$RoleRow = $Role->fetchRow($Role->select()->where('id = ?', $id));
				} else { 
					$RoleRow = $Role->createRow();
				}
					
				$campos = array();
				
				foreach($data as $dato){					
					if($dato['campo']!== 'accion' && $dato['campo'] !== '' ){
						$RoleRow->$dato['campo'] = $dato['valor'];
					}					
				}

				$id = $RoleRow->save();
				
				$RoleResource->delete( "idRole  = $id " );
				if(!empty($resource)){
					$RoleResRow = null;
					foreach($resource as $datores){
						if($datores['campo']== 'resource' ){
							$RoleResRow = $RoleResource->createRow();
							$RoleResRow->idResource = $datores['valor'];
							$RoleResRow->idRole = $id;
							$RoleResRow->access = 1;
							$RoleResRow->save();
						}
					}
				}
				
				$tr->commit();
				$cache  = Zend_Registry::get('cache');
				$cache->clean();
				
				//$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, "");
				
				
				echo Zend_Json_Encoder::encode(array('id'=> $id));				
				
			} else {
				throw new Exception("Invalid parameters");
			}
	
		/*} catch (Exception $e){
			$tr->rollBack();
			echo( $e->getMessage() );
		}*/
			
		} catch (Exception $e){
			$mens = $e->getCode();
			if($mens == 23000)
				throw new Exception('This role already exists');//$this->view->assign('error', 'This users already exists');
			else
				$this->view->assign('mensaje', $e->getMessage());
			$this->_forward('index', 'role');
		}	
			
	}
	/**
	 * Borrado de roles
	 * @throws Exception
	 */
	
	public function delAction(){ 
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$role = new Table_Role();
		try {
			$id = $this->getRequest()->getParam('id', null);	
			if( is_null($id) ){
				throw new Exception("invalid parameters.");
			}			
			$res= $role->delete("id = $id ");
			if($res){
				$this->view->assign('tipo', 'success');
				$this->view->assign('prefrase', 'Success');
				$this->view->assign('mensaje', 'User successfully deleted');
				$this->_forward('index', 'role','admin');
			}else{
				throw new Exception('Could not delete the specified record');
			}
			
			} catch (Exception $e){
				$this->view->assign('mensaje', $e->getMessage());
				$this->view->assign('tipo', 'error');
				$this->view->assign('prefrase', 'Error');
				$this->_forward('index', 'role','admin');				
			}
	}
	
	/**
	 * Método para rellenar los datos de la grilla de usuarios
	 */
		
	public function ajaxsourceAction(){		
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
				
		$Role= new Table_Role();
		$roles = $Role->getRoles($where, $sort, $limit, $sSearch );		
		printJsonGrid( $roles, $sEcho );
	}
	

}