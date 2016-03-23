<?php

class Table_Ads extends Zend_Db_Table_Abstract {

    protected $_name = 'Ads';
    protected $_primary = 'idAds';
    
    CONST OCW_TYPE_COURSE = 1;
    CONST OCW_TYPE_LECTURE = 3;
    CONST OCW_TYPE_COLLECTION = 4;
    CONST OCW_TYPE_CONFERENCE = 6;    
    
    /**
     * Obtiene los campos que hay en la tabla OCW
     * se usa para hacer los inserts
     * @return array
     */
    public function getFields() {
        $sql = 'DESCRIBE OCW';
        $info = $this->getAdapter()->fetchAll($sql);
        $campos = array();
        foreach ($info as $campo) {
            $campos[] = $campo['Field'];
        }
        return $campos;
    }

    /**
     * Obtiene los datos para mostrar en grilla de OCW
     * @param string $where cadena a aplicar en el where de la consulta
     * @param array $sort array('columna'=>1, 'direccion'=>'ASC')
     * @param array $limit array('limit'=>-1, 'offset'=>0)
     * @param string $sSearch cadena a buscar
     * @throws Exception
     * @return array Array que contiene un cursor, cantidad de registros y cantidad de registros filtrados
     */
    public function getAdsGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='') {
        try{
            //validaciones de parámetros
            $where       = is_null($where) ? 'AND 1=1 ' : $where;

            if( !is_array($sort) || !is_array($limit) || !is_string($sSearch) || !is_string($where)){ // verifico que los argumentos sean validos
                throw new Exception('Invalid parameters');
            } else {
                if( !array_key_exists('columna', $sort) || !array_key_exists('direccion', $sort) || !array_key_exists('limit', $limit) || !array_key_exists('offset', $limit) ){
                    throw new Exception('Invalid parameters');
                }
            }

            $columsSortable = array();
            $columsSortable[1] = "r0.adsName";
            $columsSortable[2] = "r0.adsRedirectURL";
            $columsSortable[3] = "r0.adsImageURL";
            $columsSortable[4] = "r0.adsBeginDate";
            $columsSortable[5] = "r0.adsEndDate";
            $columsSortable[6] = "r1.idAdsType";
            $columsSortable[7] = "r2.id";

            if(!empty($sSearch)){
                $implode_array_d1 = implode(',', $columsSortable);
                $implode_array_d2 = implode(',', array_reverse($columsSortable));
                $sSearch = str_replace(' ', '%', $sSearch);
                $sSearch = strtolower($sSearch);
                $concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
                $where .= ' AND '.$concat_ws;
            }

            $sSort  = $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];

            $sql    = "SELECT   r0.idAds
                                , r0.idAds
                                , r0.adsName
                                , r0.adsRedirectURL
                                , r0.adsImageURL
                                , r0.adsBeginDate
                                , r0.adsEndDate
                                , r1.adsTypName
                                , r2.typName

                    From Ads r0
                    left join AdsType r1 on r1.idAdsType = r0.idAdsType
                    left join OCWTypes r2 on r2.id = r0.idOCWTypes
                    WHERE 1=1
                    $where
                    ";

            $cache = Zend_Registry::get('cache');

            if ( ($totalCount = $cache->load('getAdsGrid')) === false ){
                $sqlCount =     "SELECT count(1) as total
                                        FROM Ads r0
                                        left join AdsType r1 on r1.idAdsType = r0.idAdsType
                                        left join OCWTypes r2 on r2.id = r0.idOCWTypes
                                        WHERE 1=1 ";
                $res = $this->getDefaultAdapter()->fetchRow($sqlCount);
                $totalCount = $res['total'];
                $cache->save($totalCount);
            }

            $sqlCountWhere =    "SELECT count(1) as total
                                FROM Ads r0
                                left join AdsType r1 on r1.idAdsType = r0.idAdsType
                                left join OCWTypes r2 on r2.id = r0.idOCWTypes
                                WHERE 1=1 $where ";
            $res = $this->getDefaultAdapter()->fetchRow($sqlCountWhere);
            $totalCountWhere = $res['total'];

            $pag = $limit['limit'];
            $start = $limit['offset'];
            if($pag == -1 || $pag == '-1' ){
                $sql .= $where. " ORDER BY $sSort";
            } else {
                $sql .= $where. " ORDER BY $sSort LIMIT $pag OFFSET $start";
            }
            //$sql .=" $where   ORDER BY $sSort LIMIT $pag OFFSET $start";

            $rs = array('cursor'=>$this->getDefaultAdapter()->query($sql) , 'count'=>$totalCount, 'countWhere'=>$totalCountWhere );

            return $rs;

        } catch (Exception $e) {
            throw new Exception( $e->getMessage() );
        }
    }

    public function getOCWPageGrid() {
        try {
            $sqlCount = "SELECT count(1) as total From ocw.Ads";
            $res = $this->getDefaultAdapter()->fetchRow($sqlCount);
            $totalCount = $res['total'];

            $sql = "SELECT a.idAds, '', adsName, a.adsRedirectURL, a.adsImageURL, a.adsBeginDate, a.adsEndDate,   
                    (Select adsTypName From AdsType where idAdsType = a.idAdsType) name_ads, 
                    (Select typName From OCWTypes where id = a.idOCWTypes) name_ocw, a.idAdsType, a.idOCWTypes, a.adsSection, a.adsActive FROM Ads as a";

            $rs = array('cursor' => $this->getDefaultAdapter()->query($sql), 'count' => $totalCount, 'countWhere' => $totalCount);

            return $rs;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Obtiene la Info de la tabla OCW, algunos campos 
     * @param integer $id
     * @return Mixed Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     */
    public function getOCWinfo($id = null) {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('r0' => 'OCW'), array('r0.ocwTitle', 'r0.ocwDescription'))
                ->joinLeft(array('r1' => 'University'), 'r1.id = r0.idUniversity', array('r1.uniName'))
                ->joinLeft(array('r2' => 'School'), 'r2.id = r0.idSchool', array('r2.schName'))
                ->joinLeft(array('r3' => 'Department'), 'r3.id = r0.idDepartment', array('r3.depName'))
        ;
        if (!is_null($id)) { // añado el where
            $select->where('r0.id = ? ', $id);
            return $this->fetchRow($select);
        } else {
            return $this->fetchAll($select);
        }
    }

    /**
     * Buscador
     * @param string $search
     * @param string $filter
     * @param string $category
     * @param string $language
     * @return Ambigous <Zend_Db_Select, Zend_Db_Table_Select>
     */
    public function getOCWSearch($search = null, $filter = 'all', $category = 'all', $language = 'all') {

        $fieldsearch = "LOWER(r0.ocwTitle), LOWER(r0.ocwKeywords), LOWER(r0.ocwDescription)";
        $fieldsearchreverse = "LOWER(r0.ocwDescription), LOWER(r0.ocwKeywords), LOWER(r0.ocwTitle)";

        $sSearch = str_replace(' ', '%', $search);
        $sSearch = addslashes(strtolower($sSearch));
        $concat_ws = "CONCAT_WS(' '," . $fieldsearch . ',' . $fieldsearchreverse . ") LIKE '%$sSearch%'";

        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('r0' => 'OCW'), array('r0.id', 'r0.ocwTitle', 'r0.ocwTitleEncode', 'r0.ocwGolive', 'r0.ocwDescription', 'r0.ocwKeywords', 'r0.idType')
                )
                ->joinInner(array('r1' => 'OCWTypes'), 'r1.id=r0.idType AND r1.visibility=1', array('r1.typName'))
                ->joinInner(array('r8' => 'Language'), "r8.id = r0.idLanguage", array('r8.lanName'))
                ->joinLeft(array('r2' => 'University'), 'r2.id = r0.idUniversity', array('uniName'))
                ->joinLeft(array('r3' => 'School'), 'r3.id = r0.idSchool', array('schName'))
                ->joinLeft(array('r4' => 'Department'), 'r4.id = r0.idDepartment', array('depName'))
                ->joinLeft(array('r5' => 'Author'), 'r5.idOCW = r0.id', '')
                ->joinLeft(array('r6' => 'Person'), 'r6.id = r5.idPer', array(new Zend_Db_Expr("GROUP_CONCAT( CONCAT_WS(' ', r6.perFirstName, r6.perLastName ) ORDER BY r5.autSequence DESC SEPARATOR ', ') AS author")))
                ->joinLeft(array('r9' => 'Course'), 'r0.id = r9.idOCW', array('r9.ocwBypassUrlCourse'))
                ->joinLeft(array('r10' => 'Lecture'), 'r0.id = r10.idOCW', array('r10.ocwBypassUrlLecture'))
                ->joinLeft(array('r11' => 'File'), 'r0.id = r11.idOCW', array('r11.ocwUrlFile'))
                ->where($concat_ws)
                ->where("r0.ocwGolive = ?", 1)
                ->group("r0.id")
                ->order('r0.ocwTitle');

        if ($filter != 'all') {
            $select->where('r1.typName = ?', $filter);
        }
        if ($category != 'all') {
            $select->joinInner(array('r7' => 'OCWCategory'), 'r7.idOCW = r0.id AND r7.idCat=' . $category, array('r7.idCat'));
        }

        if ($language != 'all') {
            $select->where('r8.lanName = ?', $language);
        }
        return $select;
    }

    /**
     * Remueve un OCW del cache
     * @param integer $idOCW
     * @throws Exception
     * @return boolean
     */
    public function removeFromCache($idOCW) {
        if (is_null($idOCW) || empty($idOCW)) {
            return false;
        }
        $ocw = $this->fetchRow("id=$idOCW");

        $file = $ocw->ocwTitleEncode . '.html';
        $package = $ocw->ocwTitleEncode . '.imscc';
        $output = array();
        $output2 = array();
        $runCommand = 'find ' . CACHE_PUBLIC . ' -type f -name "' . $file . '*" -exec rm -fv {} \;';
        exec($runCommand, $output);
        // eliminar el paquete si ya existe
        $root = $_SERVER['DOCUMENT_ROOT'];
        $root = $root . '/packages/';
        $runCommand = 'find ' . $root . ' -type f -name "*' . $package . '" -exec rm -fv {} \;';
        exec($runCommand, $output2);
        // Tomar como salida cierta la eliminación del cache no del paquete
        if (empty($output)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Remueve los INDEX del cache
     * @param integer $idType
     * @return boolean
     */
    public function removeCacheIndex($idType) {
        if (!is_null($idType)) {
            switch ($idType) {
                case 1: // course
                    $runCommand = 'rm -f ' . CACHE_PUBLIC . '/courses.html';
                    break;
                case 3:
                    $runCommand = 'rm -f ' . CACHE_PUBLIC . '/lectures.html';
                    break;
                case 4:
                    $runCommand = 'rm -f ' . CACHE_PUBLIC . '/collections.html';
                    break;
                case 6:
                    $runCommand = 'rm -f ' . CACHE_PUBLIC . '/conferences.html';
                    break;
            }
            exec($runCommand, $output);
            return true;
        } else {
            throw new Exception('idType null');
        }
    }

    public function getOcwRss($filter = 'all', $limit = 100) {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('r0' => 'OCW'), array('r0.id', 'r0.ocwTitle', 'r0.ocwTitleEncode', 'r0.ocwGolive', 'r0.ocwDescription', 'r0.ocwKeywords', 'r0.idType', 'r0.thumbnail', 'r0.ocwLicense')
                )
                ->joinInner(array('r1' => 'OCWTypes'), 'r1.id=r0.idType AND r1.visibility=1', array('r1.typName'))
                ->joinInner(array('r8' => 'Language'), "r8.id = r0.idLanguage", array('r8.lanName'))
                ->joinLeft(array('r2' => 'University'), 'r2.id = r0.idUniversity', array('uniName'))
                ->joinLeft(array('r3' => 'School'), 'r3.id = r0.idSchool', array('schName'))
                ->joinLeft(array('r4' => 'Department'), 'r4.id = r0.idDepartment', array('depName'))
                ->joinLeft(array('r5' => 'Author'), 'r5.idOCW = r0.id', '')
                ->joinLeft(array('r6' => 'Person'), 'r6.id = r5.idPer', array(new Zend_Db_Expr("GROUP_CONCAT( CONCAT( IF(r6.perEmail IS NOT NULL, r6.perEmail, ''), ' (',CONCAT_WS(' ', r6.perFirstName, r6.perLastName ) , ') ' ) ORDER BY r5.autSequence DESC SEPARATOR ', ') AS author")))
                ->joinLeft(array('r9' => 'Course'), 'r0.id = r9.idOCW', array('r9.ocwBypassUrlCourse'))
                ->joinLeft(array('r10' => 'Lecture'), 'r0.id = r10.idOCW', array('r10.ocwBypassUrlLecture'))
                ->joinLeft(array('r11' => 'File'), 'r0.id = r11.idOCW', array('r11.ocwUrlFile'))
                ->where("r0.ocwGolive = ?", 1) // publicadas
                ->group("r0.id")
                ->order('r0.id DESC');

        if ($filter != 'all' && $filter != 'full') {
            $select->where('r1.typName = ?', $filter);
        }
        $select->where('r1.typName != ?', 'File');
        $select->joinInner(array('r7' => 'OCWCategory'), 'r7.idOCW = r0.id ', array('r7.idCat'));
        if ($filter != 'full') {
            $select->limit($limit);
        }
        return $this->fetchAll($select);
    }

    /**
     * Retorna OCW Pages que depende de OCWCategory y OCWTypes
     * @param String/array $idCat String $idOcwType
     * @return json array
     */
    public function getpages($idCat, $idOcwType) {
        try {
            $condicionOCW = 'ot.id = ' . $idOcwType;

            if (is_array($idCat)) {
                $in_cat = implode(',', $idCat);
                $condicionSQL = 'oc.idCat in (' . $in_cat . ') AND ' . $condicionOCW;
            } else {
                $condicionSQL = 'oc.idCat in (' . $idCat . ') AND ' . $condicionOCW;
            }

            $sql = "SELECT o.id, o.ocwTitle, oc.idCat
                    FROM OCW o
                    inner join OCWCategory oc on oc.idOCW = o.id 
                    inner join OCWTypes ot on ot.id = o.idType
                    where  
                    1=1 and                                        
                    " . $condicionSQL;

            $res = $this->getDefaultAdapter()->fetchAll($sql);

            return $res;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getAdsIndex($ocwTypeId){

        $adsAllCategories = $this->select()->where('adsActive = 1')
            ->where('adsSection = 1')
            ->where('idOCWtypes = ' . $ocwTypeId)
            ->where('(adsBeginDate <= (select date_format(sysdate(), "%Y-%m-%d"))')
            ->where('adsEndDate >= (select date_format(sysdate(), "%Y-%m-%d")) OR adsEndDate is NULL)');

        $ads_actives = $this->fetchAll($adsAllCategories);

        $result = array();
        if(empty($ads_actives) == false){
            $result = $this->getArrayAds($ads_actives);
        }

        if(count($result) > 1){
            $asdRandom = array_rand($result, 2);
            $adsSelected = [];
            foreach ($result as $k => $value) {
                if(in_array($k, $asdRandom)){
                    $adsSelected[] = $value;
                }
            }
        }else{
            $adsSelected = $result;
        }

        return $adsSelected;
        
    }
    
    
    public function getAdsView($ocwTypeId , $idPage, $id_categories){
        $ids = explode(",", $id_categories);
       
        $inIds = "";
        foreach ($ids as $value) {
            if ($inIds == ""){
                $inIds = "'$value'";
            }else{
                $inIds = $inIds . "," . "'$value'";
            }
        }

       // ADS que se seleccionaron para todas las Categories
        $adsAllCategories = $this->select() ->where('adsActive = 1')
            ->where('adsAllCategories = 1')
            ->where('idOCWtypes = '. $ocwTypeId)
            ->where('(adsBeginDate <= (select date_format(sysdate(), "%Y-%m-%d"))')
            ->where('adsEndDate >= (select date_format(sysdate(), "%Y-%m-%d")) OR adsEndDate is NULL)');

        $ads_all = $this->fetchAll($adsAllCategories);

        $result_ads_all = array();
        if(empty($ads_all) == false){
            $result_ads_all = $this->getArrayAds($ads_all);
        }

        //ADS que se seleccionaron para OCW especificas
        $ads_active = $this  ->select()
            ->setIntegrityCheck(false)
            ->from(array('a0'=>'Ads'))
            ->joinInner(array('a1'=>'AdsOCW'), 'a0.idAds = a1.idAds', array())
            ->joinInner(array('a2'=>'OCW'), 'a1.idOCW = a2.id',array())
            ->where('adsActive = 1')
            ->where('idOCWtypes = '. $ocwTypeId)
            ->where('(adsBeginDate <= (select date_format(sysdate(), "%Y-%m-%d"))')
            ->where('adsEndDate >= (select date_format(sysdate(), "%Y-%m-%d")) OR adsEndDate is NULL)')
            ->where('a2.id = ?', $idPage);

        $ads_actives = $this->fetchAll($ads_active);

        $result_ads_ocw = array();
        if(empty($ads_actives) == false){
            $result_ads_ocw = $this->getArrayAds($ads_actives);
        }

        //ADS para Categorias sin seleccion de OCW
        $ads_categories = $this ->select()
            ->setIntegrityCheck(false)
            ->from(array('a0'=>'Ads'))
            ->joinInner(array('a1'=>'AdsCategory'), 'a0.idAds = a1.idAds', array())
            ->joinInner(array('a2'=>'Category'), 'a1.idCat = a2.id', array())
            ->where('a0.adsActive = 1')
            ->where('idOCWtypes = '. $ocwTypeId)
            ->where('(a0.adsBeginDate <= (select date_format(sysdate(), "%Y-%m-%d"))')
            ->where('a0.adsEndDate >= (select date_format(sysdate(), "%Y-%m-%d")) OR adsEndDate is NULL)')
            ->where('a0.adsWithPages = 0')
            ->where('a2.id IN ('.$inIds.')');

        $ads_Categorias = $this->fetchAll($ads_categories);
        
        $result_ads_cat = array();
        if(empty($ads_Categorias) == false){
            $result_ads_cat = $this->getArrayAds($ads_Categorias);
        }
        
        //Armado de un solo array
        foreach ($result_ads_ocw as $rao){
            $result_ads_all[] = $rao;
        }

        foreach ($result_ads_cat as $rac){
            $result_ads_all[] = $rac;
        }

        $ads_all_unique = array_unique($result_ads_all, SORT_REGULAR);
        
        return $ads_all_unique;
    }
    
    private function getArrayAds($ads_actives){
        $result = array();
        foreach ($ads_actives as $value) {
            $obj['idAds'] = $value["idAds"];
            $obj['userId'] = $value["userId"];
            $obj['adsRedirectURL'] = $value["adsRedirectURL"];
            $obj['adsImageURL'] = $value["adsImageURL"];
            $obj['adsBeginDate'] = $value["adsBeginDate"];
            $obj['adsEndDate'] = $value["adsEndDate"];
            $obj['adsActive'] = $value["adsActive"];
            $obj['adsSection'] = $value["adsSection"];
            $obj['adsName'] = $value["adsName"];
            $obj['idOCWTypes'] = $value["idOCWTypes"];
            $obj['idAdsType'] = $value["idAdsType"];

            $result[] = $obj;
        }
        
        return $result;
        
    }
    
}