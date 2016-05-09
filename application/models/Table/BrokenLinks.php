<?php
class Table_BrokenLinks extends Zend_Db_Table_Abstract {
	protected $_name    = 'BrokenLinks';
	protected $_primary = array('id');
	
	
	/**
	 * 
	 * @param string $where
	 * @param array $sort
	 * @param array $limit
	 * @param string $sSearch
	 * @throws Exception
	 * @return multitype:unknown Ambigous <Zend_Db_Statement_Interface, Zend_Db_Statement, PDOStatement>
	 */
	public function getLinksGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
	{
		try {
			//parameters validation
			$where       = is_null($where) ? 'AND 1=1 ' : $where;
				
			if( !is_array($sort) || !is_array($limit) || !is_string($sSearch) || !is_string($where)){ // verify valid arguments
				throw new Exception('Invalid parameters');
			} else {
				if( !array_key_exists('columna', $sort) || !array_key_exists('direccion', $sort) || !array_key_exists('limit', $limit) || !array_key_exists('offset', $limit) ){
					throw new Exception('Invalid parameters');
				}
			}
				
			$columsSortable = array();
			$columsSortable[1] = "ocwTitle";
			$columsSortable[2] = "field";
			$columsSortable[3] = "link";
				
			if(!empty($sSearch)){
				$implode_array_d1 = implode(',', $columsSortable);
				$implode_array_d2 = implode(',', array_reverse($columsSortable));
				$sSearch = str_replace(' ', '%', $sSearch);
				$sSearch = strtolower($sSearch);
				$concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
				$where .= ' AND '.$concat_ws;
			}
	
			$sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];
				
			$sql         = "SELECT    BrokenLinks.id
									, BrokenLinks.ocwID
									, OCW.ocwTitle
									, BrokenLinks.field
									, BrokenLinks.link
							From BrokenLinks
							Inner Join OCW ON OCW.id = BrokenLinks.ocwID
							WHERE 1=1 " . $where;
				
			$cache = Zend_Registry::get('cache');
			if ( ($totalCount = $cache->load('getLinksGrid')) === false )
			{
				$sqlCount = "SELECT count(1) as total
								FROM BrokenLinks
								WHERE 1=1 ";
				$res = $this->getDefaultAdapter()->fetchRow($sqlCount);
				$totalCount = $res['total'];
				$cache->save($totalCount);
			}
	
			$sqlCountWhere = "SELECT count(1) as total
								FROM BrokenLinks
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