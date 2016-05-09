<?php
class Admin_AdsController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('admin');
    }

    /**
     * Ads List
     */
    public function indexAction()
    {
    	$this->view->headTitle('Promotions :: List');
        // action body
    	$us = new Table_User();
    	$usuario = $us::getIdentity();
    	$this->view->assign('usuario',$usuario);
    }

    /**
     * Delete a OCW
     * @throws Exception
     */
    public function savepublishAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$Ocw = new Table_OCW();

    	try {
            $Id = $this->getRequest()->getParam('Id', null);
            if( is_null($Id) ){
                throw new Exception( 'Insufficient parameters' );
            }
            $row = $Ocw->fetchRow( 'id='.$Id );
            $row->ocwGolive = $this->getRequest()->getParam('value', 0);
            $Ocw->removeFromCache($Id);
            $Ocw->removeCacheIndex($row->idType);
            $Id = $row->save();
            if($Id){
                echo Zend_Json_Encoder::encode(array('Id'=> $Id));
            } else {
                throw new Exception('Failed to update the state, this is an error associated with the database, please refresh the page and try again');
            }
    	} catch (Exception $e) {
            echo $e->getMessage();
    	}
    }

    /**
     * Save ADS
     * @throws Exception
     */
    public function saveAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        try {
            $campos = $this->getRequest()->getParam('data');
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $dbAdapter->beginTransaction();

            $Ads = new Table_Ads();
            $AdsOCW = new Table_AdsOCW();
            $AdsCategory = new Table_AdsCategory();
            /*For update*/
            if($campos[10]) {
                if (!empty($campos[11])){
                    $adsAllCategories = 0;
                }
                else {
                    $adsAllCategories = 1;
                }
                if(empty($campos[6])) {
                    $data = array(
                        'adsName' => $campos[0],
                        'idAdsType' => $campos[1],
                        'idOCWTypes' => $campos[2],
                        'adsRedirectURL' => $campos[3],
                        'adsImageURL' => $campos[4],
                        'adsBeginDate' => $campos[5],
                        'adsEndDate' => NULL,
                        'adsActive' => $campos[7],
                        'adsSection' => $campos[8],
                        'adsAllCategories' => $adsAllCategories,
                    );
                }else {
                    $data = array(
                        'adsName' => $campos[0],
                        'idAdsType' => $campos[1],
                        'idOCWTypes' => $campos[2],
                        'adsRedirectURL' => $campos[3],
                        'adsImageURL' => $campos[4],
                        'adsBeginDate' => $campos[5],
                        'adsEndDate' => $campos[6],
                        'adsActive' => $campos[7],
                        'adsSection' => $campos[8],
                        'adsAllCategories' => $adsAllCategories,
                    );
                }

                $AdsOCW->delete("idAds =".$campos[10]);

                if(!empty($campos[9])) {
                    foreach ($campos[9] as $value) {
                        $newAdsOCW = $AdsOCW->createRow();
                        $newAdsOCW->idAds = $campos[10];
                        $newAdsOCW->idOCW = $value;
                        $newAdsOCW->save();
                    }
                    $data['adsWithPages'] = 1;
                }else{
                    $data['adsWithPages'] = 0;
                }

                $AdsCategory->delete("idAds =".$campos[10]);
                if(!empty($campos[11])) {
                    foreach ($campos[11] as $value) {
                        $newAdsCat = $AdsCategory->createRow();
                        $newAdsCat->idAds = $campos[10];
                        $newAdsCat->idCat = $value;
                        $newAdsCat->save();
                    }
                }

                $dbAdapter->update('Ads', $data, 'idAds = '.$campos[10]);

            }else{
                /*New ads*/
                $newAds = $Ads->createRow();
                $newAds->adsName = $campos[0];
                $newAds->idAdsType = $campos[1];
                $newAds->idOCWTypes = $campos[2];
                $newAds->adsRedirectURL = $campos[3];
                $newAds->adsImageURL = $campos[4];
                $newAds->adsBeginDate = $campos[5];
                $newAds->adsActive = $campos[7];
                $newAds->adsSection = $campos[8];

                if(empty($campos[6])) {
                    $newAds->adsEndDate = NULL;
                } else {
                    $newAds->adsEndDate = $campos[6];
                }

                if(!empty($campos[11])) {
                    $newAds->adsAllCategories = 0;
                } else {
                    $newAds->adsAllCategories = 1;
                }

                if(empty($campos[9])) {
                    $newAds->adsWithPages = 0;
                }else{
                    $newAds->adsWithPages = 1;
                }

                $newAds->save();

                $idAds_generate = $newAds->idAds;

                if(!empty($campos[9])) {
                    foreach ($campos[9] as $value) {
                        $newAdsOCW = $AdsOCW->createRow();
                        $newAdsOCW->idAds = $idAds_generate;
                        $newAdsOCW->idOCW = $value;
                        $newAdsOCW->save();
                    }
                }

                if(!empty($campos[11])) {
                    foreach ($campos[11] as $value) {
                        $newAdsCat = $AdsCategory->createRow();
                        $newAdsCat->idAds = $idAds_generate;
                        $newAdsCat->idCat = $value;
                        $newAdsCat->save();
                    }
                }
            }

            $dbAdapter->commit();

        } catch (Exception $e) {
            $dbAdapter->rollBack();
            echo($e->getMessage());
        }
    }

    public function deleteAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
        $Ads = new Table_Ads();
        $AdsOCW = new Table_AdsOCW();
        $AdsCategory = new Table_AdsCategory();

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $dbAdapter->beginTransaction();

    	try {
            $adsId = $this->getRequest()->getParam('id',null);

            if (is_null($adsId)){
                throw new Exception('ADS Id is null'.$adsId);
            }

            $ads_edit = $Ads->fetchRow($Ads->select()->where('idAds = ?', $adsId));

            if($ads_edit->adsAllCategories == 0){
                $AdsCategory->delete("idAds =".$adsId);
                $AdsOCW->delete("idAds =".$adsId);
            }

            $Ads->delete("idAds = $adsId");

            $arrayImageToDelete = explode("/",$ads_edit->adsImageURL);
            $imageToDelete =  $arrayImageToDelete[count($arrayImageToDelete)-1];
            $path = $_SERVER["DOCUMENT_ROOT"] . "/upload/images/" . $imageToDelete;

            if (file_exists($path)) {
                unlink($path);
            }

            $dbAdapter->commit();
            $this->_redirect('/admin/ads');
    	} catch (Exception $e) {
            $dbAdapter->rollBack();
            echo $e->getMessage();
    	}
    }

    /**
     * Bring the categories for a given OCWType
     */
    public function getcategoryfortypeAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $OCWTypes = $this->getRequest()->getParam('OCWTypes', null);
        $Categories = new Table_Category();
        $categories = $Categories->getTreeCategory($OCWTypes);

        echo Zend_Json_Encoder::encode($categories);
    }


    /**
     * Bring the OCWPage for a Category of a determined OCWType
     */
    public function getpagesAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $OCWTypes = $this->getRequest()->getParam('OCWTypes', null);
        $categories   = $this->getRequest()->getParam('categories', null);

        $Categories = new Table_Category();
        $validate_categories = $Categories->getSubCategory($categories);

        $ads = new Table_Ads();
        $newAds = $ads->getpages($validate_categories, $OCWTypes);
        echo Zend_Json_Encoder::encode($newAds);
    }

    public function newadsAction(){
        try{
            $this->view->headTitle('Promotions :: Add');
            $us = new Table_User();
            $usuario = $us::getIdentity();
            $this->view->assign('usuario',$usuario);
            $Id = $this->getRequest()->getParam('id_row', null);
            $change_select = $this->getRequest()->getParam('change_select', null);

            #If is new
            if(!$Id) {
                $OCWTypes = new Table_OCWTypes();
                $ocwTypes = $OCWTypes->select() ->where('id in(1,3,4)')
                    ->where('visibility=1')
                    ->order('typName ASC');

                $types = $OCWTypes->fetchAll($ocwTypes);

                $AdsTypes = new Table_AdsType();
                $adstypes = $AdsTypes->fetchAll('adsTypVisibility=1');

                $Categories = new Table_Category();

                $boolTypeNew = 0;

                $this->view->assign('types', $types);
                $this->view->assign('adstypes', $adstypes);
                $this->view->assign('categories', $Categories);
                $this->view->assign('boolType', $boolTypeNew);

            } else {

                #If is for edit
                $idADS = $this->getRequest()->getParam('id_row', null);
                $this->view->assign('id_row', $idADS);

                $Ads = new Table_Ads();
                $ads_edit = $Ads->fetchRow($Ads->select()->where('idAds = ?', $idADS));
                $idOcwType = $ads_edit->idOCWTypes;
                $this->view->assign('ads', $ads_edit);

                $category_edit = '';
                $adsCat_ids = [];
                $ocw_editList = '';
                $ocw_ids = [];
                $ocw_select = '';

                $Categories = new Table_Category();
                $categories_id = $Categories->getTreeCategory($idOcwType);

                if($ads_edit->adsAllCategories == 0) {

                    $AdsCategory = new Table_AdsCategory();
                    $adscat_edit = $AdsCategory->fetchAll($AdsCategory->select()->where('idAds = ?', $idADS));

                    foreach ($adscat_edit as $value) {
                        $adsCat_ids[] = $value->idCat;
                    }

                    $id_adsCat = implode(',', $adsCat_ids);

                    $category_edit = $Categories->getCategoryEdit($id_adsCat);

                    $id_categorias_seleccionas = array();
                    foreach ($category_edit as $ce) {
                        $id_categorias_seleccionas []= $ce['id'];
                    }

                    $validate_categories = $Categories->getSubCategory($id_categorias_seleccionas);
                    $ocw_select_desplegable = $Ads->getpages($validate_categories, $idOcwType);

                    $listocw = array( 'id' => '', 'idCat' => '', 'ocwTitle' => '');
                    $ocwcatselect = array();

                    foreach($ocw_select_desplegable as $value) {
                        $idCat = array();
                        foreach($ocw_select_desplegable as $v) {
                            if($value['id'] == $v['id']) {
                                $idCat = $v['idCat'];
                            }
                        }
                        $listocw['idCat'] = $idCat;
                        $listocw['id'] = $value['id'];
                        $listocw['ocwTitle'] = $value['ocwTitle'];
                        array_push($ocwcatselect, $listocw);
                    }

                    $ocw_select = array_unique($ocwcatselect, SORT_REGULAR);

                    $AdsOCW = new Table_AdsOCW();
                    $adsocw_edit = $AdsOCW->fetchAll($AdsOCW->select()->where('idAds = ?', $idADS));

                    foreach ($adsocw_edit as $value) {
                        $ocw_ids[] = $value->idOCW;
                    }

                    if(!empty($ocw_ids)) {

                        $id_ocw = implode(',', $ocw_ids);

                        $OCWCategory = new Table_OCWCategory();
                        $consulta = $OCWCategory->select()
                            ->setIntegrityCheck(false)
                            ->from(array('c0'=>'OCWCategory'),array('c0.idOCW', 'c0.idCat'))
                            ->joinInner(array('c1'=>'OCW'), 'c0.idOCW = c1.id', array('c1.ocwTitle'))
                            ->where("idOCW in ($id_ocw)");

                        $ocwcategories = $OCWCategory->fetchAll($consulta);

                        $ocw_cat = array();
                        foreach ($ocwcategories as $value) {
                            $obj['idOCW'] = $value["idOCW"];
                            $obj['idCat'] = $value["idCat"];
                            $obj['ocwTitle'] = $value["ocwTitle"];
                            $ocw_cat[] = $obj;
                        }

                        $armado_ocw_cat = array();
                        foreach($ocw_cat as $k => $oc){
                            if(in_array($oc['idCat'], $adsCat_ids)){
                                $armado_ocw_cat[] = $oc;
                            }
                            else{
                                $aCategoria = $Categories->getCategoryForSubCat($oc['idCat']);
                                if(in_array($aCategoria, $adsCat_ids)){
                                    $oc['idCat'] = $aCategoria;
                                    $armado_ocw_cat[] = $oc;
                                }
                            }
                        }

                        $list = array( 'id' => '', 'idCat' => [], 'ocwTitle' => '');
                        $ocwcatList = array();
                        foreach($armado_ocw_cat as $value){
                            $arrayCat = array();
                            foreach($armado_ocw_cat as $v){
                                if($value['idOCW'] == $v['idOCW']){
                                    array_push($arrayCat, $v['idCat']);
                                }
                            }
                            $list['idCat'] = $arrayCat;
                            $list['id'] = $value['idOCW'];
                            $list['ocwTitle'] = $value['ocwTitle'];
                            array_push($ocwcatList, $list);
                        }

                        $ocwcatList = array_unique($ocwcatList, SORT_REGULAR);

                        $ocw_editList = array();
                        foreach($ocwcatList as $ocl){
                            $ocl['idCat'] = implode(',', $ocl['idCat']);
                            $ocw_editList[] = $ocl;
                        }
                    }
                }

                $includecategoriesids = array();

                foreach($categories_id[0] as $cat) {
                    $includecategoriesids[] = $cat["id"];
                }

                $this->view->assign('includecategoriesids', $includecategoriesids);     //Categories related to existing OCW - select menu
                $this->view->assign('allcategories', $categories_id[1]);                //All the Categories - select menu
                $this->view->assign('pagecategories', $category_edit);                  //Categories - chosen list
                $this->view->assign('pagecategories_ids', $adsCat_ids);
                $this->view->assign('pages', $ocw_select);                              //OCW Page -  select menu
                $this->view->assign('ocwpages', $ocw_editList);                         //OCW Page - chosen list
                $this->view->assign('ocwpages_ids', $ocw_ids);

                $boolTypeNew = 1;
                $this->view->assign('boolType', $boolTypeNew);

                $OCWTypes = new Table_OCWTypes();

                $ocwTypes_select = $OCWTypes->select()->where('id = ?', $ads_edit['idOCWTypes']);
                $types_select = $OCWTypes->fetchRow($ocwTypes_select);
                $this->view->assign('typeSelect', $types_select);

                $ocwTypes = $OCWTypes->select()->where('id in(1,3,4)')
                    ->where('visibility=1')
                    ->where('id != ?', $ads_edit['idOCWTypes'])
                    ->order('typName ASC');

                $types = $OCWTypes->fetchAll($ocwTypes);
                $this->view->assign('types', $types);

                $AdsTypes = new Table_AdsType();
                $adsTypes_select = $AdsTypes->select()->where('idAdsType = ?', $ads_edit['idAdsType'])
                    ->where('adsTypVisibility=1');
                $ads_select = $AdsTypes->fetchRow($adsTypes_select);
                $this->view->assign('adsSelect', $ads_select);

                $adsTypes = $AdsTypes->select()->where('idAdsType != ?', $ads_edit['idAdsType'])
                    ->where('adsTypVisibility=1');

                $ads = $AdsTypes->fetchAll($adsTypes);

                $this->view->assign('adstypes', $ads);
            }
        } catch (Exception $e) {
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'ads', 'admin');
        }
    }

    /**
    * Method to fill the data grid ocw
    */
    public function adsgridAction(){
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();
        $filters = Zend_Json_Decoder::decode($this->getRequest()->getParam('filters', '{}'));
        $where = '';

        if(!is_null($filters)) {
            $where = $this->buildWherelp($filters) . $where;
        }

        $i = 0;

        $sEcho = $this->getRequest()->getParam('sEcho', 4);
        $iSortCol_0 = $this->getRequest()->getParam('iSortCol_0', 1);
        $sSortDir_0 = $this->getRequest()->getParam('sSortDir_0', 'asc');
        $sort = array('columna'=> $iSortCol_0 , 'direccion'=> $sSortDir_0);

        //Pagging
        $iDisplayLength=$this->getRequest()->getParam('iDisplayLength', 50); // limit
        $iDisplayStart = $this->getRequest()->getParam('iDisplayStart', 0); // offset
        $limit = array('limit'=>$iDisplayLength, 'offset'=> $iDisplayStart);

        //search
        $sSearch = $this->getRequest()->getParam('sSearch', '');

        $Ads= new Table_Ads();
        $adss = $Ads->getAdsGrid($where, $sort, $limit, $sSearch);

        $cursor = $adss['cursor'];
        $totalRegistros = $adss['count'];
        $totalRegistrosWere = $adss['countWhere'];

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
     * Where construction method to filter the grid ads
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
                    // Equal, the list of values is equal to the field (used to compare a single value)
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
                    // The field is less than the list of values
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
                    // Detect if date and add TO_DATE('27-06-2009','DD-MM-YYYY')
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