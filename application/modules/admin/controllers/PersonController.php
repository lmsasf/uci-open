<?php
class Admin_PersonController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('admin');
    }
	/**
	 * Listado de personas
	 */
    public function indexAction()
    {
    	$this->view->headTitle('Person :: List');
        // action body
    }
    
    /**
     * Levanta el formulario de edición de personas
     * @throws Exception
     */
    public function editpersonAction(){
    	
    	$Person = new Table_Person();
    	$PersonDegree = new Table_PersonDegree();
    	$PersonDepartment = new Table_PersonDepartment();
    	$Sponsor = new Table_Sponsor();
    	// Obtener parámetros
    	try{
    		$Id = $this->getRequest()->getParam('id', null);
    		$accion = $this->getRequest()->getParam('accion', null);
    		$where = "1=1";
    		
    		$University = new Table_University();
	    	$university = $University->fetchAll('1=1', 'uniName');
	    	$this->view->assign('universitys', $university);
	    	 
	    	 
    		if(!is_null($Id) && is_null($accion)) { // viene del listado para editar
    			$this->view->headTitle('Person :: Edit');
    			// busco los datos del cliente
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
    			
    			//verifico si es sponsor
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
    		if(is_null($Id)) { // nueva Persona
    			$this->view->headTitle('Person :: Add');
    			$this->view->assign('accion', 'add');
    			$this->view->assign('id', null);
    			$this->view->assign('Person', null);
    		}
    	
    		// busco los datos generales 
    		$Degree = new Table_Degree();
    		$this->view->assign('Degrees', $Degree->fetchAll($where, 'degShortDescription asc'));    		
    			
    	} catch (Exception $e){
    		$this->view->assign( 'error', $e->getMessage() );
    		$this->_forward('index', 'person', 'admin');
    	}    	
    }
    
    /**
	 * Ajax para eliminar personas
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
			$this->_forward('index', 'person', 'admin');//echo Zend_Json_Encoder::encode(array('Id'=> $Id));
		} catch (Exception $e) {
			$this->view->assign('error', 'An error occurred while removing the person');
			$this->_forward('index', 'person', 'admin');
		}	
    }
    
    /**
     * Ajax que guarda la información del formulario de personas
     * @throws Exception
     */
    public function savepersonAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	$Person = new Table_Person();
    	$Sponsor = new Table_Sponsor();
    	// Obtener parámetros
    	$tr = $Person->getAdapter()->beginTransaction();
    	try {
    		$Id 	 		= $this->getRequest()->getParam('id'		 , null);
    		$accion  		= $this->getRequest()->getParam('accion'	 , null);
    		$data	 		= $this->getRequest()->getParam('data'		 , null);
    		$degrees 		= $this->getRequest()->getParam('degrees'	 , null);
    		$datasponsor	= $this->getRequest()->getParam('datasponsor', null);
    		$departments 	= $this->getRequest()->getParam('departments', null);
			//d($degrees); exit();    		
    		if( !is_null($accion) && !is_null($data) ){ // editar o añadir
    			$PersonRow = null;
    			
    			if($accion === 'edit'){
    				$PersonRow = $Person->fetchRow($Person->select()->where('id = ?', $Id));
    				//d($PersonRow); exit;
    			} else {
    				$PersonRow = $Person->createRow();
    			}
    			
    			//echo "<pre>";var_dump($data);exit;
    			
    			foreach($data as $dato){
    				if(isset($dato['campo'])){
    					if($dato['campo']!== 'accion'){
    						$PersonRow->$dato['campo'] = $dato['valor'];
    					}
    				}
    			}
    			$idPer = $PersonRow->save();
    			
    			//sponsor
    			if(!is_null($datasponsor)){ //echo "<pre>";var_dump($datasponsor);exit;
    				$Sponsor->delete( "personId = $idPer" );
    				$spoIsOrganization = null; 
    				$spoCompanyName = null; 
    				$spoCompanyURL = null;
    				$spoImageURL = null;
    				$spoEmail = null;
    				$spoPhone = null;
    				$spoAddress = null;
    				if($datasponsor[0]["valor"] == 'on'){
	    				//echo "<pre>";var_dump($datasponsor);exit;
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
	    			
	    			// los vuelvo ainsertar
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
	    			
	    			// los vuelvo ainsertar
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
    /*
    function savedepartmentAction(){
    	//$data	= $this->getRequest()->getParam('data', null);
    	//echo $data[0]["valor"];echo "<pre>";var_dump($data);exit;
    	
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$Deppartment = new Table_Department();
    	$PersonDeppartment = new Table_PersonDepartment();
    	try{
    		$data	= $this->getRequest()->getParam('data', null);
    		    		
    		if(!empty($data)){
    			foreach($data as $dato){
					if($dato['campo']=='perId'){
						$idPer = $dato['valor'];
					}
    				if($dato['campo']=='idUniversity'){
						$idUni = $dato['valor'];
						$uni = $dato['texto'];
    				}
    				if($dato['campo']=='idSchool'){
						$idSchool = $dato['valor'];
						$school = $dato['texto'];
    				}	
    				if($dato['campo']=='idDepartment'){
						$idDep = $dato['valor'];
						$dep = $dato['texto'];
    				}		
				}
				//verifico si ya existe
				$perdep = $PersonDeppartment->fetchRow(' idPer = ' . $idPer . ' and idDep = ' . $idDep );
				if(empty($perdep)){
					$persondeppartment = $PersonDeppartment->createRow();
					
					$persondeppartment->idPer = $idPer;
					$persondeppartment->idDep = $idDep;
					$persondeppartment->save();
					echo Zend_Json_Encoder::encode(array('idDep'=> $idDep, 'dep'=>$dep, 'idSchool'=>$idSchool, 'school'=>$school, 'idPer'=>$idPer, 'idUni'=>$idUni, 'uni'=> $uni));
				}else{
					$this->view->assign('error', 'The deppartment already exists'); 
				}
    		}  		
    		
    				
    	} catch (Exception $e){
    		$this->view->assign( 'error', $e->getMessage() );
    		$this->_forward('index', 'university', 'admin');
    	}
    	
    }*/

    /**
     * Método para rellenar los datos de la grilla de personas
     */
    public function persongridAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
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
		//search de la grilla
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
    			$PK = array_shift($row); // se asume que la primer fila tiene PK
    			//$json = Zend_Json_Encoder::encode($row);
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
     * Método para construcción del where para filtrar la grilla de personas
     */
    private function buildWherelp($filters){
		$sql = '';
		foreach ($filters AS $campo => $opciones ) {
			$valores = $opciones['values'];
			$operador = $opciones['op'];
			// conversion de operadores relacionales
			// EQ 	NE 	GT 	LT 	GE 	LE
			switch ($operador) {
				case 'EQ':
					// igual, la lista de valores es igual al campo (Sirve para comparar un sólo valor)
					// ya que un campo no puede tener mas de un valor
					$operador = '= ALL';
					break;
				case 'NE':
					// No es igual o diferente a todos los elementos listados
					$operador = '!= ALL';
					break;
				case 'GT':
					// El campo es mayor al del listado de valores
					$operador = '> ANY';
					break;
				case 'LT':
					// El campo es menor al del listado de valores
					$operador = '< ANY';
					break;
				case 'GE':
					// El campo es mayor o igual al del listado de valores
					$operador = '>=';
					break;
				case 'LE':
					// El campo es menor al del listado de valores
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
					// detectar si es fecha
					$patron= "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
					//Detecto si es fecha y agrego TO_DATE('27-06-2009','DD-MM-YYYY')
					$valor = preg_match($patron, $valor) ? "'".convFechaSQL($valor)."'" : ( is_numeric($valor) ? $valor : ( $operador !=='LIKE' ? "'" . $valor . "'": strtolower($valor) ) );
					// si el operador es LIKE el separador es % y debo añadirselos si la cadena tiene espacios
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