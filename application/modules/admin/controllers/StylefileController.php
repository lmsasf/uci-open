<?php
class Admin_stylefileController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->setLayout('admin');
    }

    /**
     * Stylefiles list
     */
    public function indexAction() {
        $this->view->headTitle('Style :: List');

    }
    
    /**
     * Editions stylefiles form
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
     * Ajax to restore stylefiles
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
     * Ajax keeps information form style files
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

            if( !is_null($accion) && !is_null($data) ){ // add or edit
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
                    // copy original to /bk
                    $command = 'cp -R ' . "./frontend/css/".$filez . ' ' ."./frontend/css/bk/".$filez;
                    shell_exec($command);
                }

                // save css.
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

                // save on table
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
     * Method that gets data from StyleFileLog
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
     * Method to fill the data grid style files
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
		//search
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
     * Where construction method to filter the grid stylefiles
     */
    private function buildWherelp($filters){
		$sql = '';
		foreach ($filters AS $campo => $opciones ) {
			$valores = $opciones['values'];
			$operador = $opciones['op'];
			// conversion of relational operators
			// EQ 	NE 	GT 	LT 	GE 	LE
			switch ($operador) {
				case 'EQ':
					// equal, the list of values is equal to the field (used to compare a single value)
					// because a field can not have more than one value
					$operador = '= ALL';
					break;
				case 'NE':
					// It is not the same or different for all listed items
					$operador = '!= ALL';
					break;
				case 'GT':
					// The field is greater than the list of values
					$operador = '> ANY';
					break;
				case 'LT':
					// The field is less than the list of values
					$operador = '< ANY';
					break;
				case 'GE':
					// The field is greater than or equal to the list of values
					$operador = '>=';
					break;
				case 'LE':
					// The field is less than or equal to the list of values
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
					// detect if date
					$patron= "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
					// detect if date and add TO_DATE('27-06-2009','DD-MM-YYYY')
					$valor = preg_match($patron, $valor) ? "'".convFechaSQL($valor)."'" : ( is_numeric($valor) ? $valor : ( $operador !=='LIKE' ? "'" . $valor . "'": strtolower($valor) ) );
					// if the operator is LIKE the separator is % and must add it if the string has spaces
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