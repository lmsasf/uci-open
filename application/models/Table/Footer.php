<?php
class Table_Footer extends Zend_Db_Table_Abstract {

    protected $_name    = 'FE_Footer';
    protected $_primary = 'id';


    /**
     * Obtiene los datos para mostrar en grilla de Frontend Sections Text
     * @param string $where cadena a aplicar en el where de la consulta
     * @param array $sort array('columna'=>1, 'direccion'=>'ASC')
     * @param array $limit array('limit'=>-1, 'offset'=>0)
     * @param string $sSearch cadena a buscar
     * @throws Exception
     * @return array Array que contiene un cursor, cantidad de registros y cantidad de registros filtrados
     */
    public function getFooterGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
    {
        try {
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
            $columsSortable[0] = "id";
            $columsSortable[1] = "fooArea";
            $columsSortable[2] = "fooDescription";
            $columsSortable[3] = "fooSequence";
            $columsSortable[4] = "fooUrl";
            $columsSortable[5] = "fooInclude";
            $columsSortable[6] = "headerInclude";

            if(!empty($sSearch)){
                $implode_array_d1 = implode(',', $columsSortable);
                $implode_array_d2 = implode(',', array_reverse($columsSortable));
                $sSearch = str_replace(' ', '%', $sSearch);
                $sSearch = strtolower($sSearch);
                $concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
                $where .= ' AND '.$concat_ws;
            }

            $sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];

            $sql         = "SELECT    Footer.id, Footer.id
									, Area.areaDescription as fooArea
									, Footer.fooDescription
									, Footer.fooSequence
									, Footer.fooUrl
									, CASE Footer.fooInclude
									    WHEN 1 THEN 'Yes'
									    ELSE 'No'
									  END fooInclude
									, CASE Footer.headerInclude
									    WHEN 1 THEN 'Yes'
									    ELSE 'No'
									  END headerInclude
							FROM FE_Footer Footer
							INNER JOIN FE_FooterAndHeaderArea Area ON Area.id = Footer.idArea
							WHERE 1 = 1 " . $where;

            $cache = Zend_Registry::get('cache');
            if ( ($totalCount = $cache->load('getFooterGrid')) === false )
            {
                $sqlCount = "SELECT count(1) as total
								FROM FE_Footer
								WHERE 1 = 1 ";
                $res = $this->getDefaultAdapter()->fetchRow($sqlCount);
                $totalCount = $res['total'];
                $cache->save($totalCount);
            }

            $sqlCountWhere = "SELECT count(1) as total
								FROM FE_Footer
								WHERE 1 = 1 ". $where;
            //echo $sqlCountWhere;
            $res = $this->getDefaultAdapter()->fetchRow($sqlCountWhere);
            $totalCountWhere = $res['total'];

            $pag = $limit['limit'];
            $start = $limit['offset'];
            if($pag == -1 || $pag == '-1' ){
                $sql .= $where. " ORDER BY $sSort";
            } else {
                $sql .= $where. " ORDER BY $sSort LIMIT $pag OFFSET $start";
            }

            $rs = array('cursor'=>$this->getDefaultAdapter()->query($sql) , 'count'=>$totalCount, 'countWhere'=>$totalCountWhere );
            return $rs;
        }catch (Exception $e){
            throw new Exception( $e->getMessage() );
        }
    }

    public function getNextSequence($area){
        $sql = "SELECT MAX(fooSequence)+1 sequence
                FROM FE_Footer
                WHERE idArea = '$area'";
        $next = $this->getDefaultAdapter()->fetchRow($sql);
        return $next['sequence'];
    }

    public function reasignarOrden($area,$orden){
        $sql = "UPDATE FE_Footer
                SET fooSequence = fooSequence + 1
                WHERE idArea = '$area'
                    AND fooSequence >= $orden";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getFooterData(){
        $sql="SELECT
                    Footer.id
                    , Area.areaDescription as fooArea
                    , Area.id as idArea
                    , Area.areaSequence
                    , Footer.fooDescription
                    , Footer.fooSequence
                    , Footer.fooImageUrl
                    , Footer.fooUrl
                    , CASE Footer.fooInclude
                        WHEN 1 THEN 'Yes'
                        ELSE 'No'
                      END fooInclude
              FROM FE_Footer Footer
              INNER JOIN FE_FooterAndHeaderArea Area ON Footer.idArea = Area.id
              WHERE fooInclude = 1
              ORDER BY Area.areaSequence,Footer.fooSequence";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getHeaderData(){
        $sql="SELECT *
              FROM FE_Footer Foo
              INNER JOIN FE_FooterAndHeaderArea Area ON Foo.idArea = Area.id
              WHERE headerInclude = 1
                AND (fooImageUrl IS NOT NULL AND fooImageUrl != '')
              ORDER BY areaSequence desc,fooSequence asc";
        return $this->getAdapter()->fetchAll($sql);
    }

}