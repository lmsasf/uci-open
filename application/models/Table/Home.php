<?php
class Table_Home extends Zend_Db_Table_Abstract
{

    protected $_name = 'FE_Home';
    protected $_primary = 'id';

    public function getHomeGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
    {
        try {
            //parameters validations
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
            $columsSortable[1] = "homeSquare";
            $columsSortable[2] = "homeType";
            $columsSortable[3] = "homeTitle";
            $columsSortable[4] = "homeText";
            $columsSortable[5] = "homeOrder";
            $columsSortable[6] = "homeUrl";
            $columsSortable[7] = "homeActive";

            if(!empty($sSearch)){
                $implode_array_d1 = implode(',', $columsSortable);
                $implode_array_d2 = implode(',', array_reverse($columsSortable));
                $sSearch = str_replace(' ', '%', $sSearch);
                $sSearch = strtolower($sSearch);
                $concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
                $where .= ' AND '.$concat_ws;
            }

            $sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];

            $sql         = "SELECT    id,id
                                    , CASE homeType
                                        WHEN 'text' THEN 'Text'
                                        WHEN 'slide_i' THEN 'Carousel Image'
                                        WHEN 'slide_v' THEN 'Carousel Video'
                                        WHEN 'banner' THEN 'Banner'
                                      END homeType
                                    , homeSquare
                                    , homeTitle
                                    , concat(left(homeText,30),'...') homeText
                                    , homeOrder
                                    , CASE
                                        WHEN homeImageUrl IS NULL OR homeImageUrl = '' THEN homeUrl
                                        WHEN homeUrl IS NULL OR homeUrl = '' THEN homeImageUrl
                                      END homeUrl
                                    , homeActive
							FROM FE_Home
							WHERE 1 = 1 " . $where;

            $cache = Zend_Registry::get('cache');
            if ( ($totalCount = $cache->load('getHomeGrid')) === false )
            {
                $sqlCount = "SELECT count(1) as total
								FROM FE_Home
								WHERE 1 = 1 ";
                $res = $this->getDefaultAdapter()->fetchRow($sqlCount);
                $totalCount = $res['total'];
                $cache->save($totalCount);
            }

            $sqlCountWhere = "SELECT count(1) as total
								FROM FE_Home
								WHERE 1 = 1 ". $where;
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

    public function getHomeData(){
        $sql="SELECT *
              FROM FE_Home
              WHERE homeActive = 1
              ORDER BY homeSquare, homeOrder";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getSlideCount(){
        $sql="SELECT count(*) count
              FROM FE_Home
              WHERE homeActive = 1
                AND homeType LIKE 'slide_%'";
        return $this->getAdapter()->fetchRow($sql);
    }

    public function getBlocksDisabled(){
        $sql="SELECT homeSquare
              FROM FE_Home
              WHERE HomeSquare != 2
              GROUP BY homeSquare";
        return $this->getAdapter()->fetchAll($sql);
    }
}