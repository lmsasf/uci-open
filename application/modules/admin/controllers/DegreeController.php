<?php
class Admin_DegreeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('admin');
    }
	/**
	 * Listado de grados
	 */
    public function indexAction()
    {
    	$this->view->headTitle('Degree :: List');
        // action body
    }
    
    /**
     * Levanta el formulario de edición de grados
     * @throws Exception
     */
    public function editdegreeAction(){
    	
    	$Degree = new Table_Degree();
    	
    	try{
    		$Id = $this->getRequest()->getParam('id', null);
    		$accion = $this->getRequest()->getParam('accion', null);
    		$where = "1=1";
    		
    		if(!is_null($Id) && is_null($accion)) {
    			$this->view->headTitle('Degree :: Edit');
    			// busco los datos del grado
    			$this->view->assign('Degree', $Degree->fetchRow($Degree->select()->where('id = ?', $Id)));
    			$this->view->assign('accion', 'edit');
    		}
    		if(is_null($Id)) { // nuevo grado
    			$this->view->headTitle('Degree :: Add');
    			$this->view->assign('accion', 'add');
    			$this->view->assign('id', null);
    			$this->view->assign('Degree', null);
    		}
    	    			
    	} catch (Exception $e){
    		$this->view->assign( 'error', $e->getMessage() );
    		$this->_forward('index', 'degree', 'admin');
    	}    	
    }
    
    /**
	 * Ajax para eliminar grados
	 */
	public function deleteAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$Degree = new Table_Degree();
    	   	
    	try {
			$Id = $this->getRequest()->getParam('Id', null);
			if( is_null($Id) ){
				throw new Exception( 'Insufficient parameters' );
			}
			$resp = $Degree->delete("id = $Id");
			$this->_forward('index', 'degree', 'admin');//echo Zend_Json_Encoder::encode(array('Id'=> $Id));
		} catch (Exception $e) {
			echo $e->getMessage();
		}	
    }
    
    /**
     * Ajax que guarda la información del formulario de grados
     * @throws Exception
     */
    public function savedegreeAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	$Degree = new Table_Degree();    	
    	$tr = $Degree->getAdapter()->beginTransaction();
    	try {
    		$Id 	 		= $this->getRequest()->getParam('id'		 , null);
    		$accion  		= $this->getRequest()->getParam('accion'	 , null);
    		$data	 		= $this->getRequest()->getParam('data'		 , null);
    		    		
    		if( !is_null($accion) && !is_null($data) ){ // editar o añadir
    			$DegreeRow = null;
    			
    			if($accion === 'edit'){
    				$DegreeRow = $Degree->fetchRow($Degree->select()->where('id = ?', $Id));
    				//d($PersonRow); exit;
    			} else {
    				$DegreeRow = $Degree->createRow();
    			}
    			foreach($data as $dato){
    					if($dato['campo']!== 'accion'){
    						$DegreeRow->$dato['campo'] = $dato['valor'];
    					}
    			}
    			$idGr = $DegreeRow->save();
    			$tr->commit();
    			echo Zend_Json_Encoder::encode(array('id'=> $idGr));
    		} else {
    			throw new Exception("Insufficient parameters");
    		}
    
    	} catch (Exception $e){
    		$tr->rollBack();
    		echo( $e->getMessage() );
    	}
    }    
    
    /**
     * Método para rellenar los datos de la grilla de grados
     */
    public function degreegridAction(){
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
		
		
    	$Degree= new Table_Degree();
    	$degrees = $Degree->getDegreesGrid($where, $sort, $limit, $sSearch );
    
    	$cursor = $degrees['cursor'];
		$totalRegistros = $degrees['count'];
		$totalRegistrosWere = $degrees['countWhere'];	
		
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
     * Método para construcción del where para filtrar la grilla de grados
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