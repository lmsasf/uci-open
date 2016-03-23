<?php
class Table_StyleFile extends Zend_Db_Table_Abstract {

    protected $_name    = 'StyleFile';
    protected $_primary = 'id';

    /**
     * Obtiene los datos para mostrar en grilla de degree
     * @param string $where cadena a aplicar en el where de la consulta
     * @param array $sort array('columna'=>1, 'direccion'=>'ASC')
     * @param array $limit array('limit'=>-1, 'offset'=>0)
     * @param string $sSearch cadena a buscar
     * @throws Exception
     * @return array Array que contiene un cursor, cantidad de registros y cantidad de registros filtrados
     */
    public function getDegreesGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
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
            $columsSortable[1] = "styleDescription";
            $columsSortable[2] = "styleShortDescription";

            if(!empty($sSearch)){
                $implode_array_d1 = implode(',', $columsSortable);
                $implode_array_d2 = implode(',', array_reverse($columsSortable));
                $sSearch = str_replace(' ', '%', $sSearch);
                $sSearch = strtolower($sSearch);
                $concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
                $where .= ' AND '.$concat_ws;
            }

            $sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];

            $sql         = "SELECT    id
									, id 
									, styleDescription
									, styleShortDescription
							From StyleFile
							WHERE 1=1 " . $where;

            $cache = Zend_Registry::get('cache');
            if ( ($totalCount = $cache->load('getDegreesGrid')) === false )
            {
                $sqlCount = "SELECT count(1) as total
								FROM StyleFile 
								WHERE 1=1 ";
                $res = $this->getDefaultAdapter()->fetchRow($sqlCount);
                $totalCount = $res['total'];
                $cache->save($totalCount);
            }

            $sqlCountWhere = "SELECT count(1) as total
								FROM StyleFile
								WHERE 1=1 ". $where;

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

}