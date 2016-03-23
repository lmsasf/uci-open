<?php
class Admin_FhomeController extends Zend_Controller_Action
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
        $this->view->headTitle('Home Design :: List');
        // action body
        $Template = new Table_SectionTemplate();
        $design = $Template->fetchRow($Template->select()->where("secCode = 'HOME'"));
        $this->view->assign('design', $design['secTemplate']);
    }

    public function designhomeAction()
    {
        $Template = new Table_SectionTemplate();
        $this->view->assign('design', $Template->fetchRow($Template->select()->where("secCode = 'HOME'")));
    }

    public function edithomeAction()
    {
        $Home = new Table_Home();

        try{
            $id = $this->getRequest()->getParam('id', null);
            $accion = $this->getRequest()->getParam('accion', null);

            if(!is_null($id) && is_null($accion)) {
                $this->view->headTitle('Frontend Home Design :: Edit');
                // busco los datos
                $this->view->assign('home', $Home->fetchRow($Home->select()->where('id = ?', $id)));
                $this->view->assign('accion', 'edit');
            }
            if(is_null($id)) { // nuevo
                $this->view->headTitle('Frontend Home Design :: Add');
                $this->view->assign('accion', 'add');
                $this->view->assign('id', null);
                $this->view->assign('home', null);
            }

            $this->view->assign( 'blocksDisabled', $Home->getBlocksDisabled() );

            $Template = new Table_SectionTemplate();
            $design = $Template->fetchRow($Template->select()->where("secCode = 'HOME'"));
            $this->view->assign('design', $design['secTemplate']);

        } catch (Exception $e){
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'frontend', 'admin');
        }
    }

    public function savehomeAction(){
        $this->_helper->layout()->setLayout('empty');
        // no necesita vista para renderizarse
        $this->_helper->viewRenderer->setNoRender();
        $Home = new Table_Home();
        $tr = $Home->getAdapter()->beginTransaction();
        try {
            $id 	 		= $this->getRequest()->getParam('id'		 , null);
            $accion  		= $this->getRequest()->getParam('accion'	 , null);
            $data	 		= $this->getRequest()->getParam('data'		 , null);

            if( !is_null($accion) && !is_null($data) ){ // editar o añadir
                $SectionRow = null;

                if($accion === 'edit'){
                    $HomeRow = $Home->fetchRow($Home->select()->where('id = ?', $id));
                } else {
                    $HomeRow = $Home->createRow();
                }

                foreach($data as $dato){
                    if($dato['campo']!== 'accion' && $dato['campo']!== 'hImageURL'){
                        $HomeRow->$dato['campo'] = $dato['valor'];
                    }
                }
                $idGr = $HomeRow->save();
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

    public function homegridAction(){
        $this->_helper->layout()->setLayout('empty');
        // no necesita vista para renderizarse
        $this->_helper->viewRenderer->setNoRender();
        $filters = Zend_Json_Decoder::decode($this->getRequest()->getParam('filters', '{}'));
        $where = '';
        //if(!is_null($filters)){
            //$where = $this->buildWherelp($filters) . $where;
        //}

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

        $Home= new Table_Home();
        $homegrid = $Home->getHomeGrid($where, $sort, $limit, $sSearch );

        $cursor = $homegrid['cursor'];
        $totalRegistros = $homegrid['count'];
        $totalRegistrosWhere = $homegrid['countWhere'];

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

    public function savedesignAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $Template = new Table_SectionTemplate();
        $tr = $Template->getAdapter()->beginTransaction();
        try {
            $filez 	 		= $this->getRequest()->getParam('filez'		 , null);
            $editor  		= $this->getRequest()->getParam('editor'	 , null);
            $id 	 		= $this->getRequest()->getParam('id'		 , null);
            $data	 		= $this->getRequest()->getParam('data'		 , null);
            $action	 		= $this->getRequest()->getParam('accion'	 , null);

            if( !is_null($action) && !is_null($data) ){ // editar o añadir
                $SectionRow = null;

                if($action === 'edit'){
                    $Row = $Template->fetchRow($Template->select()->where('id = ?', $id));
                } else {
                    $Row = $Template->createRow();
                }

                foreach($data as $dato){
                    if($dato['campo']!== 'accion'&&$dato['campo']!== 'filez'){
                        $Row->$dato['campo'] = $dato['valor'];
                    }
                }


                if(!is_null($filez)) {
                    /********************save file*********************/
                    /*$command = 'cat ' . "./frontend/css/bk/" . $filez;
                    $out = shell_exec($command);

                    if (empty($out)) {
                        // copiar el original al /bk
                        $command = 'cp -R ' . "./frontend/" . $filez . ' ' . "./frontend/css/bk/" . $filez;
                        shell_exec($command);
                    }*/

                    // grabar
                    $fh = fopen("./frontend/" . $filez, 'w') or die("Error al abrir fichero de salida");
                    fwrite($fh, $editor);
                    fclose($fh);

                    /*************************************************/
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

    public function restorefileAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $Template = new Table_SectionTemplate();

        try {
            $id 	 		= $this->getRequest()->getParam('id'		 , null);

            if(!is_null($id)){
                $Row = $Template->fetchRow($Template->select()->where('id = ?', $id));
                $filez = $Row['secCustomfile'];

                if(!is_null($filez)) {
                    /********************save file*********************/
                    // copiar el original del /bk
                    $command = 'cp ' . "./frontend/css/bk/" . $filez . ' ' . "./frontend/" . $filez;
                    shell_exec($command);
                    /*************************************************/
                    echo Zend_Json_Encoder::encode(true);
                }

            } else {
                throw new Exception("Insufficient parameters");
            }

        } catch (Exception $e){
            echo( $e->getMessage() );
        }
    }

    public function deleteAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $Sections = new Table_Home();

        try {
            $id = $this->getRequest()->getParam('id', null);
            if( is_null($id) ){
                throw new Exception( 'Insufficient parameters' );
            }
            $resp = $Sections->delete("id = $id");
            $this->_forward('index', 'fhome', 'admin');//echo Zend_Json_Encoder::encode(array('Id'=> $Id));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function customizeAction(){
        $Custom = new Table_SectionTemplate();

        try{
            $id = $this->getRequest()->getParam('id', null);
            $this->view->headTitle('Frontend Home Design :: Custom');

            if(!is_null($id)) {
                // busco los datos
                $this->view->assign('customs', $Custom->fetchRow($Custom->select()->where('id = ?', $id)));
                $this->view->assign('accion', 'edit');
            }
            if(is_null($id)) { // nuevo
                $this->view->assign('accion', 'add');
                $this->view->assign('id', null);
                $this->view->assign('customs', null);
            }

        } catch (Exception $e){
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'frontend', 'admin');
        }
    }
}