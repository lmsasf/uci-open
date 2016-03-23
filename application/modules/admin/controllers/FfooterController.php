<?php
class Admin_FFooterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('admin');
    }
    /**
     * Listado de datos para el footer
     */
    public function indexAction()
    {
        $this->view->headTitle('Pages Design :: List');
        // action body
    }

    /**
     * Levanta el formulario de edición de textos
     * @throws Exception
     */
    public function editfooterAction(){

        $Footer = new Table_Footer();

        try{
            $id = $this->getRequest()->getParam('id', null);
            $accion = $this->getRequest()->getParam('accion', null);

            if(!is_null($id) && is_null($accion)) {
                $this->view->headTitle('Frontend Pages Design :: Edit');
                // busco los datos
                $this->view->assign('footer', $Footer->fetchRow($Footer->select()->where('id = ?', $id)));
                $this->view->assign('accion', 'edit');
            }
            if(is_null($id)) { // nuevo
                $this->view->headTitle('Frontend Pages Design :: Add');
                $this->view->assign('accion', 'add');
                $this->view->assign('id', null);
                $this->view->assign('footer', null);
            }

            $Area = new Table_FooterAndHeaderArea();
            $this->view->assign('areas', $Area->fetchAll($Area->select()));

        } catch (Exception $e){
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'frontend', 'admin');
        }
    }

    public function getnextsequenceAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $area = $this->getRequest()->getParam('area',null);
        $Footer = new Table_Footer();
        $nextSequence = $Footer->getNextSequence($area);
        echo Zend_Json_Encoder::encode($nextSequence);
    }
    /**
     * Ajax para eliminar textos
     */
    public function deleteAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $Sections = new Table_Footer();

        try {
            $id = $this->getRequest()->getParam('id', null);
            if( is_null($id) ){
                throw new Exception( 'Insufficient parameters' );
            }
            $resp = $Sections->delete("id = $id");
            $this->_forward('index', 'ffooter', 'admin');//echo Zend_Json_Encoder::encode(array('Id'=> $Id));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Ajax que guarda la información del formulario de textos
     * @throws Exception
     */
    public function savefooterAction(){
        $this->_helper->layout()->setLayout('empty');
        // no necesita vista para renderizarse
        $this->_helper->viewRenderer->setNoRender();
        $Footer = new Table_Footer();
        $tr = $Footer->getAdapter()->beginTransaction();
        try {
            $id 	 		= $this->getRequest()->getParam('id'		 , null);
            $accion  		= $this->getRequest()->getParam('accion'	 , null);
            $data	 		= $this->getRequest()->getParam('data'		 , null);

            if( !is_null($accion) && !is_null($data) ){ // editar o añadir
                $SectionRow = null;

                if($accion === 'edit'){
                    $FooterRow = $Footer->fetchRow($Footer->select()->where('id = ?', $id));
                } else {
                    $FooterRow = $Footer->createRow();
                }

                //$Footer->reasignarOrden($area,$order);

                foreach($data as $dato){
                    if($dato['campo']!== 'accion'){
                        $FooterRow->$dato['campo'] = $dato['valor'];
                    }
                }
                $idGr = $FooterRow->save();
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
     * Método para rellenar los datos de la grilla de textos
     */
    public function footergridAction(){
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


        $Footer= new Table_Footer();
        $footersgrid = $Footer->getFooterGrid($where, $sort, $limit, $sSearch );

        $cursor = $footersgrid['cursor'];
        $totalRegistros = $footersgrid['count'];
        $totalRegistrosWhere = $footersgrid['countWhere'];

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
        echo '"iTotalDisplayRecords": "'.$totalRegistrosWhere.'"';
        echo "}";
    }
    /**
     * Método para construcción del where para filtrar la grilla de textos
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
                $sql .= $valor .$separador ;
            }
        }

        return $sql;
    }

    public function designfooterAction(){
        $Template = new Table_SectionTemplate();
        $this->view->assign('design', $Template->fetchRow($Template->select()->where("secCode = 'FOO'")));
    }

    public function designheaderAction(){
        $Template = new Table_SectionTemplate();
        $this->view->assign('design', $Template->fetchRow($Template->select()->where("secCode = 'HEAD'")));
    }

    public function savedesignAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $Design = new Table_SectionTemplate();
        $tr = $Design->getAdapter()->beginTransaction();
        try {
            $id 	 = $this->getRequest()->getParam('id'		 , null);
            $data	 = $this->getRequest()->getParam('data'		 , null);
            $type	 = $this->getRequest()->getParam('type'		 , null);
            $action	 = $this->getRequest()->getParam('accion'	 , null);


            if( !is_null($action) && !is_null($data) ){ // editar o añadir
                $SectionRow = null;

                if($action === 'edit'){
                    $Row = $Design->fetchRow($Design->select()->where('id = ?', $id));
                } else {
                    $Row = $Design->createRow();
                }

                foreach($data as $dato){
                    if($dato['campo']!='accion')
                        $Row->$dato['campo'] = $dato['valor'];
                }

                $idGr = $Row->save();
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

    public function getfooterdesignAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Template = new Table_SectionTemplate();
        $design = $Template->fetchRow($Template->select()->where("secCode = 'FOO'"));
        echo Zend_Json_Encoder::encode($design);
    }

    public function getfooterdataAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Footer = new Table_Footer();
        $data = $Footer->getFooterData();

        echo Zend_Json_Encoder::encode($data);
    }
}