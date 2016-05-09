<?php
class Admin_PersonController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('admin');
    }
	/**
	 * Person list
	 */
    public function indexAction()
    {
    	$this->view->headTitle('Person :: List');
        // action body
    }
    
    /**
     * Lift the edit form
     * @throws Exception
     */
    public function editpersonAction(){
    	
    	$Person = new Table_Person();
    	$PersonDegree = new Table_PersonDegree();
    	$PersonDepartment = new Table_PersonDepartment();
    	$Sponsor = new Table_Sponsor();
    	// get parameters
    	try{
    		$Id = $this->getRequest()->getParam('id', null);
    		$accion = $this->getRequest()->getParam('accion', null);
    		$where = "1=1";
    		
    		$University = new Table_University();
	    	$university = $University->fetchAll('1=1', 'uniName');
	    	$this->view->assign('universitys', $university);
	    	 
	    	 
    		if(!is_null($Id) && is_null($accion)) {
    			$this->view->headTitle('Person :: Edit');
    			// search client data
    			$this->view->assign('Person', $Person->fetchRow($Person->select()->where('id = ?', $Id)));
    			$select = $PersonDegree->select()
	    			->setIntegrityCheck(false)
	    			->from(array('r0'=> 'PersonDegree'), array('r0.idDeg'))
	    			->joinInner( array('r1'=> 'Degree') , 'r0.idDeg = r1.id', array('r1.degDescription'))
	    			->where('r0.idPer = ?', $Id)
    				->order('r0.pdeSequence')
    				;
    			$grados = $PersonDegree->getAdapter()->fetchAll( $select ) ;
    			$this->view->assign('PersonDegree', $grados );
    			
    			
    			$select = $PersonDepartment->select()
	    			->setIntegrityCheck(false)
	    			->from(array('r0'=> 'PersonDepartment'), array('r0.idPer', 'r0.idDep', 'r0.pedSequence', 'r0.pedTitle'))
	    			->joinInner( array('r1'=> 'Person') , 'r0.idPer = r1.id')
	    			->joinInner( array('r2'=> 'Department') , 'r0.idDep = r2.id', array('r2.depName'))
	    			->joinInner( array('r3'=> 'School') , 'r2.idSchool = r3.id', array('r3.schName'))
	    			->joinInner( array('r4'=> 'University') , 'r3.idUniversity = r4.id', array('r4.uniName'))
	    			->where('r0.idPer = ?', $Id)
    				->order('r0.pedSequence')
    				;
    			$perdep = $PersonDepartment->getAdapter()->fetchAll( $select ) ;
    			$this->view->assign('PersonDepartment', $perdep );
    			
    			//verify sponsor
    			$this->view->assign('Sponsor', $Sponsor->fetchRow($Sponsor->select()->where('personId = ?', $Id)));
    			
    			if(count($grados)> 0 ){
    				$where = 'id not in(';
	    			foreach ($grados as $grado){
	    				$tmp[] = $grado["idDeg"];
	    			}
	    			$where .= implode(',', $tmp);
	    			$where .= ')';
    			}
    			$this->view->assign('accion', 'edit');
    		}
    		if(is_null($Id)) { // new person
    			$this->view->headTitle('Person :: Add');
    			$this->view->assign('accion', 'add');
    			$this->view->assign('id', null);
    			$this->view->assign('Person', null);
    		}
    	
    		// search general data
    		$Degree = new Table_Degree();
    		$this->view->assign('Degrees', $Degree->fetchAll($where, 'degShortDescription asc'));    		
    			
    	} catch (Exception $e){
    		$this->view->assign( 'error', $e->getMessage() );
    		$this->_forward('index', 'person', 'admin');
    	}    	
    }
    
    /**
	 * Ajax  to delete persons
	 */
	public function deleteAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$Person = new Table_Person();
    	   	
    	try {
			$Id = $this->getRequest()->getParam('Id', null);
			if( is_null($Id) ){
				throw new Exception( 'Insufficient parameters' );
			}
			$resp = $Person->delete("id = $Id");
			$this->view->assign('success', 'Person removed successfully');
			$this->_forward('index', 'person', 'admin');
		} catch (Exception $e) {
			$this->view->assign('error', 'An error occurred while removing the person');
			$this->_forward('index', 'person', 'admin');
		}	
    }
    
    /**
     * Ajax to save persons form
     * @throws Exception
     */
    public function savepersonAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// don't need a view to render
    	$this->_helper->viewRenderer->setNoRender();
    	$Person = new Table_Person();
    	$Sponsor = new Table_Sponsor();
    	// get parameters
    	$tr = $Person->getAdapter()->beginTransaction();
    	try {
    		$Id 	 		= $this->getRequest()->getParam('id'		 , null);
    		$accion  		= $this->getRequest()->getParam('accion'	 , null);
    		$data	 		= $this->getRequest()->getParam('data'		 , null);
    		$degrees 		= $this->getRequest()->getParam('degrees'	 , null);
    		$datasponsor	= $this->getRequest()->getParam('datasponsor', null);
    		$departments 	= $this->getRequest()->getParam('departments', null);

			if( !is_null($accion) && !is_null($data) ){ // edit or add
    			$PersonRow = null;
    			
    			if($accion === 'edit'){
    				$PersonRow = $Person->fetchRow($Person->select()->where('id = ?', $Id));
    			} else {
    				$PersonRow = $Person->createRow();
    			}

    			foreach($data as $dato){
    				if(isset($dato['campo'])){
    					if($dato['campo']!== 'accion'){
    						$PersonRow->$dato['campo'] = $dato['valor'];
    					}
    				}
    			}
    			$idPer = $PersonRow->save();
    			
    			//sponsor
    			if(!is_null($datasponsor)){
    				$Sponsor->delete( "personId = $idPer" );
    				$spoIsOrganization = null; 
    				$spoCompanyName = null; 
    				$spoCompanyURL = null;
    				$spoImageURL = null;
    				$spoEmail = null;
    				$spoPhone = null;
    				$spoAddress = null;
    				if($datasponsor[0]["valor"] == 'on'){
	    				foreach($datasponsor as $datasp){
	    					$SponsorRow = $Sponsor->createRow();
	    						
							if($datasp['campo'] == "spoIsOrganization")
								$spoIsOrganization = $datasp['valor'];
							elseif($datasp['campo'] == "spoCompanyName")
								$spoCompanyName = $datasp['valor'];
							elseif($datasp['campo'] == "spoCompanyURL")
								$spoCompanyURL = $datasp['valor'];
							elseif($datasp['campo'] == "spoImageURL")
								$spoImageURL = $datasp['valor'];
							elseif($datasp['campo'] == "spoEmail")
								$spoEmail = $datasp['valor'];
							elseif($datasp['campo'] == "spoPhone")
								$spoPhone = $datasp['valor'];
							elseif($datasp['campo'] == "spoAddress")
								$spoAddress = $datasp['valor'];

	    				}
	    				$spoIsOrganization = ($spoIsOrganization=='off') ? 0 : 1;
		    			$SponsorRow->spoIsOrganization = $spoIsOrganization;
		    			$SponsorRow->spoCompanyName = $spoCompanyName;
		    			$SponsorRow->spoCompanyURL = $spoCompanyURL;
		    			$SponsorRow->spoImageURL = $spoImageURL;
		    			$SponsorRow->spoEmail = $spoEmail;
		    			$SponsorRow->spoPhone = $spoPhone;
		    			$SponsorRow->spoAddress = $spoAddress;
		    					
		    			$SponsorRow->personId = $idPer;
		    			$SponsorRow->save();
    				}
    			}
    		   			
    			// degrees
    			$PersonDegree = new Table_PersonDegree();
    			$PersonDegree->delete( "idPer = $idPer" );
    			if(!empty($degrees)){
	    			
	    			// insert again
					foreach ( $degrees as $degree ){
						$newPersonDegree = $PersonDegree->createRow();
						$newPersonDegree->idDeg = $degree['id'];
						$newPersonDegree->pdeSequence = $degree['order'];
						$newPersonDegree->idPer = $idPer;
						$newPersonDegree->save();
					}	
    			}
				$PersonDepartment = new Table_PersonDepartment();
				$PersonDepartment->delete( "idPer = $idPer" );
    			if(!empty($departments)){
	    			
	    			// insert again
					foreach ( $departments as $department ){
						
						$newPersonDep = $PersonDepartment->createRow();
						$newPersonDep->idDep 		= $department['id'];
						$newPersonDep->pedTitle 	= $department['titleDepartment'];
						$newPersonDep->pedSequence 	= $department['order'];
						$newPersonDep->idPer 		= $idPer;
						$newPersonDep->save();
					}	
    			}
				
    			$tr->commit();
    			echo Zend_Json_Encoder::encode(array('id'=> $idPer));
    		} else {
    			throw new Exception("Insufficient parameters");
    		}
    
    	} catch (Exception $e){
    		$tr->rollBack();
    		echo( $e->getMessage() );
    	}
    }    

    /**
     * Method to fill the data grid persons
     */
    public function persongridAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// don't a view to render
    	$this->_helper->viewRenderer->setNoRender();
    	$filters = Zend_Json_Decoder::decode($this->getRequest()->getParam('filters', '{}'));
		$where = '';
		if(!is_null($filters)){
			$where = $this->buildWherelp($filters) . $where;
		}
    	
    	$i = 0;
		
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
		
		
    	$Person = new Table_Person();
    	$persons = $Person->getPersonsGrid($where, $sort, $limit, $sSearch );
    
    	$cursor = $persons['cursor'];
		$totalRegistros = $persons['count'];
		$totalRegistrosWere = $persons['countWhere'];	
		
    	echo '{"aaData": [';
    	while ($row = $cursor->fetch(Zend_Db::FETCH_NUM)) {
    
    		if ($i <= 5000) {
    			if ($i > 0) {
    				echo ",";
    			}
    			$json = '{"DT_RowId": "'.$row[0].'", ';
    			$PK = array_shift($row);
    			foreach($row AS $k => $v){
    				$json.= '"'.$k.'":'.Zend_Json_Encoder::encode($v).',';
    			}
    			$json = substr ($json, 0, -1);
    			$json .= '}';
    			echo $json;
    			$i++;
    		} else {
    			break;
    		}
    	}
    	echo "],";
		echo '"sEcho": '.$sEcho.',';
		echo '"iTotalRecords":'. '"'.$totalRegistros.'",';
		echo '"iTotalDisplayRecords": "'.$totalRegistrosWere.'"';
		echo "}";
    }
    /**
     * Where construction method to filter the grid persons
     */
    private function buildWherelp($filters){
		$sql = '';
		foreach ($filters AS $campo => $opciones ) {
			$valores = $opciones['values'];
			$operador = $opciones['op'];
			switch ($operador) {
				case 'EQ':
					$operador = '= ALL';
					break;
				case 'NE':
					$operador = '!= ALL';
					break;
				case 'GT':
					$operador = '> ANY';
					break;
				case 'LT':
					$operador = '< ANY';
					break;
				case 'GE':
					$operador = '>=';
					break;
				case 'LE':
					$operador = '<=';
					break;
				case 'IN':
					$operador = 'IN';
					break;
				case 'BETWEEN':
					$operador = 'BETWEEN';
					break;
				case 'LIKE':
					$operador = 'LIKE';
					break;
				default:
					throw new Exception('Operador de filtro avanzado no soportado');
					break;
			}
			

			if(is_array($valores)){
				if($operador === 'BETWEEN'){
					$sql .= ' AND '  . $campo . ' ' . $operador . ' ';
				}elseif($operador === 'LIKE'){
					$sql .= ' AND LOWER('  . $campo . ') ' . $operador . " '%";
				}else{
					$sql .= ' AND '  . $campo . ' ' . $operador . ' (';
				}
				$removeChars = $operador === 'BETWEEN' ?  5 : 1 ;
				foreach ($valores AS $valor ){
					$patron= "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
					$valor = preg_match($patron, $valor) ? "'".convFechaSQL($valor)."'" : ( is_numeric($valor) ? $valor : ( $operador !=='LIKE' ? "'" . $valor . "'": strtolower($valor) ) );
					$valor = $operador === 'LIKE' ? str_replace( ' ', '%', $valor ) : $valor;
					$separador = $operador === 'BETWEEN'? ' AND ' : ($operador==='LIKE' ? '%': ',');
					$sql .= $valor . $separador;
				}
				$sql = substr ($sql, 0, ( $removeChars * -1 ) );
				if($operador === 'BETWEEN'){
					$sql .= ' ';
				}elseif($operador === 'LIKE'){
					$sql .= "%' ";
				} else {
					$sql .= ') ';
				}
			}else{
				if($operador === 'BETWEEN'){
					$sql .= ' AND '  . $campo . ' ' . $operador . ' ';
				}elseif($operador === 'LIKE'){
					$sql .= ' AND LOWER('  . $campo . ') ' . $operador . " '%";
				}else{
					$sql .= ' AND '  . $campo . ' ' . $operador ;
				}
				$valor = $valores;
				$patron= "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
				$valor = preg_match($patron, $valor) ? "'".convFechaSQL($valor)."'" : ( is_numeric($valor) ? $valor : ( $operador !=='LIKE' ? "'" . $valor . "'": strtolower($valor) ) );
				$separador = $operador;
				$sql .= $valor ;
			}
		}

		return $sql;
	}	
    
}