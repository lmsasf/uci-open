<?php
class Admin_FsectiontextController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('admin');
    }
    /**
     * Texts List
     */
    public function indexAction()
    {
        $this->view->headTitle('Section Text :: List');
        // action body
    }

    /**
     * form text editing
     * @throws Exception
     */
    public function edittextAction(){

        $Section = new Table_SectionTexts();

        try{
            $id = $this->getRequest()->getParam('id', null);
            $accion = $this->getRequest()->getParam('accion', null);
            $where = "1=1";

            if(!is_null($id) && is_null($accion)) {
                $this->view->headTitle('Frontend Sections Text :: Edit');
                $this->view->assign('section', $Section->fetchRow($Section->select()->where('Id = ?', $id)));
                $this->view->assign('accion', 'edit');
            }
            if(is_null($id)) {
                $this->view->headTitle('Frontend Sections Text :: Add');
                $this->view->assign('accion', 'add');
                $this->view->assign('id', null);
                $this->view->assign('section', null);
            }

        } catch (Exception $e){
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'frontend', 'admin');
        }
    }

    /**
     * Ajax to remove texts
     */
    public function deleteAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $Sections = new Table_SectionTexts();

        try {
            $id = $this->getRequest()->getParam('id', null);
            if( is_null($id) ){
                throw new Exception( 'Insufficient parameters' );
            }
            $resp = $Sections->delete("id = $id");
            $this->_forward('index', 'fsectiontext', 'admin');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Ajax que guarda la información del formulario de textos
     * @throws Exception
     */
    public function savetextAction(){
        $this->_helper->layout()->setLayout('empty');
        // don't need view to render
        $this->_helper->viewRenderer->setNoRender();
        $Section = new Table_SectionTexts();
        $tr = $Section->getAdapter()->beginTransaction();
        try {
            $id 	 		= $this->getRequest()->getParam('id'		 , null);
            $accion  		= $this->getRequest()->getParam('accion'	 , null);
            $data	 		= $this->getRequest()->getParam('data'		 , null);

            if( !is_null($accion) && !is_null($data) ){ // add or edit
                $SectionRow = null;

                if($accion === 'edit'){
                    $SectionRow = $Section->fetchRow($Section->select()->where('id = ?', $id));
                } else {
                    $SectionRow = $Section->createRow();
                }
                foreach($data as $dato){
                    if($dato['campo']!== 'accion'){
                        $SectionRow->$dato['campo'] = $dato['valor'];
                    }
                }
                $idGr = $SectionRow->save();
                $tr->commit();
                echo Zend_Json_Encoder::encode(array('id'=> $idGr));

                $sectionActive = $Section->fetchRow($Section->select()->where('id = ?', $idGr));
                if($sectionActive['secActive']==1) {
                    $Section->inactivateOthers($sectionActive['id'], $sectionActive['section']);
                }
            } else {
                throw new Exception("Insufficient parameters");
            }

        } catch (Exception $e){
            $tr->rollBack();
            echo( $e->getMessage() );
        }
    }

    /**
     * Method to fill the grid data
     */
    public function sectiongridAction(){
        $this->_helper->layout()->setLayout('empty');
        // don't need view to render
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


        $Section= new Table_SectionTexts();
        $sectionsgrid = $Section->getSectionTextGrid($where, $sort, $limit, $sSearch );

        $cursor = $sectionsgrid['cursor'];
        $totalRegistros = $sectionsgrid['count'];
        $totalRegistrosWhere = $sectionsgrid['countWhere'];

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
        echo '"iTotalDisplayRecords": "'.$totalRegistrosWhere.'"';
        echo "}";
    }
    /**
     * Where construction method to filter the grid text
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
                $sql .= $valor .$separador ;
            }
        }

        return $sql;
    }

}