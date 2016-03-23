<?php
class Admin_stylefileController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->setLayout('admin');
    }

    /**
     * Listado de stylefiles
     */
    public function indexAction() {
        $this->view->headTitle('Style :: List');

    }
    
    /**
     * Levanta el formulario de edición de stylefiles
     * @throws Exception
     */
    public function editstylefileAction() {
        $StyleFile = new Table_StyleFile();

        try {

            $Id = $this->getRequest()->getParam('id', null);

            if(!is_null($Id)) {
                $this->view->headTitle('StyleFile :: Edit');
                $this->view->assign('Degree', $StyleFile->fetchRow($StyleFile->select()->where('id = ?', $Id)));
                $this->view->assign('accion', 'edit');

            } else {
                throw new Exception( 'Insufficient parameters' );
            }
        } catch (Exception $e) {
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'stylefile', 'admin');
        }
    }

    /**
     * Ajax para restaurar stylefiles
     */
    public function restoreAction() {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $dbAdapter->beginTransaction();

        $us = new Table_User();
        $user = $us::getIdentity();
        $user_id = $user->id;

        $StyleFile = new Table_StyleFile();

        try {
            $Id = $this->getRequest()->getParam('Id', null);

            if( is_null($Id) ){
                throw new Exception( 'Insufficient parameters' );
            }

            $sql = $StyleFile->select()->where('id = ?', $Id);
            $types = $StyleFile->fetchAll($sql);

            $nameCSS = $types[0]['stylefile'];

            if (file_exists ("./frontend/css/bk/".$nameCSS )) {
                $command = 'cp -R ' . "./frontend/css/bk/".$nameCSS . ' ' ."./frontend/css/".$nameCSS;
                $out = shell_exec($command);

                $StyleFileLog = new Table_StyleFileLog();
                $newLog = $StyleFileLog->createRow();
                $newLog->userid = $user_id;
                $newLog->action = Table_StyleFileLog::STYLE_ACTION_RESTORE;
                $newLog->stylefile = $nameCSS;
                $newLog->save();
                $dbAdapter->commit();
            }else{
                $this->view->assign('changes', 'No changes for this Style File');
            }

            $this->_forward('index', 'stylefile', 'admin');
        } catch (Exception $e) {
            echo $e->getMessage();
            $dbAdapter->rollBack();
        }
    }
    
    /**
     * Ajax que guarda la información del formulario de stylefiles
     * @throws Exception
     */
    public function savedegreeAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $StyleFile = new Table_StyleFile();


        $tr = $StyleFile->getAdapter()->beginTransaction();
        try {
            $filez 	 		= $this->getRequest()->getParam('filez'		 , null);
            $editor  		= $this->getRequest()->getParam('editor'	 , null);
            $Id 	 		= $this->getRequest()->getParam('id'		 , null);
            $accion  		= $this->getRequest()->getParam('accion'	 , null);
            $data	 		= $this->getRequest()->getParam('data'		 , null);

            if( !is_null($accion) && !is_null($data) ){ // editar o añadir
                $DegreeRow = null;

                if($accion === 'edit'){
                    $DegreeRow = $StyleFile->fetchRow($StyleFile->select()->where('id = ?', $Id));
                } else {
                    $DegreeRow = $StyleFile->createRow();
                }
                foreach($data as $dato){
                    if( ($dato['campo']!== 'accion')&&($dato['campo']!== 'filez') ){
                        $DegreeRow->$dato['campo'] = $dato['valor'];
                    }
                }

                $command = 'cat ' . "./frontend/css/bk/".$filez;
                $out=shell_exec($command);

                if(empty($out)){
                    // copiar el original al /bk
                    $command = 'cp -R ' . "./frontend/css/".$filez . ' ' ."./frontend/css/bk/".$filez;
                    shell_exec($command);
                }

                // grabar css.
                $fh = fopen("./frontend/css/".$filez, 'w') or die("Error al abrir fichero de salida");
                fwrite($fh, $editor);
                fclose($fh);

                $us = new Table_User();
                $user = $us::getIdentity();
                $user_id = $user->id;

                $sql = $StyleFile->select()->where('id = ?', $Id);
                $types = $StyleFile->fetchAll($sql);
                $nameCSS = $types[0]['stylefile'];

                $StyleFileLog = new Table_StyleFileLog();
                $newLog = $StyleFileLog->createRow();
                $newLog->userid = $user_id;
                $newLog->action = Table_StyleFileLog::STYLE_ACTION_EDIT;
                $newLog->stylefile = $nameCSS;
                $newLog->save();

                // grabar en tabla
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
     * Metodo que obtiene los StyleFileLog
     */
    public function getchangeslogAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $StyleFile = new Table_StyleFile();

        try {
            $Id = $this->getRequest()->getParam('Id', null);


            if(is_null($Id) ){
                throw new Exception( 'Insufficient parameters' );
            }

            $sql = $StyleFile->select()->where('id = ?', $Id);
            $types = $StyleFile->fetchAll($sql);
            $nameCSS = $types[0]['stylefile'];

            $StyleFileLog = new Table_StyleFileLog();
            $getLog = $StyleFileLog ->select()
                                    ->setIntegrityCheck(false)
                                    ->from(array('st'=>'StyleFileLog'),array('st.action','st.stylefile','st.date'))
                                    ->joinInner(array('u'=>'User'), 'u.id = st.userid', array('u.usrName'))
                                    ->where('stylefile = ?', $nameCSS);

            $styleslogs = $StyleFileLog->fetchAll($getLog);

            $result = array();
            foreach ($styleslogs as $value) {
                $obj['action'] = $value["action"];
                $obj['stylefile'] = $value["stylefile"];
                $obj['date'] = $value["date"];
                $obj['usrName'] = $value["usrName"];

                $result[] = $obj;
            }

            echo Zend_Json_Encoder::encode($result);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Método para rellenar los datos de la grilla de stylefiles
     */
    public function degreegridAction(){
    	$this->_helper->layout()->setLayout('empty');
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

        $StyleFile= new Table_StyleFile();
    	$degrees = $StyleFile->getDegreesGrid($where, $sort, $limit, $sSearch );
    
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
     * Método para construcción del where para filtrar la grilla de stylefiles
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