<?php
/**
 * Mapeo de tabla Author
 * @author damills
 *
 */
class Table_Role extends Zend_Db_Table_Abstract {

	protected $_name    = 'Role';
	protected $_primary = 'id';
	
	public function getRoles($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
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
			$columsSortable[1] = "LOWER(roleName)";				
	
			if(!empty($sSearch)){
				$implode_array_d1 = implode(',', $columsSortable);
				$implode_array_d2 = implode(',', array_reverse($columsSortable));
				$sSearch = str_replace(' ', '%', $sSearch);
				$sSearch = strtolower($sSearch);
				$concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
				$where .= ' AND '.$concat_ws;
			}
	
			$sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];
	
			$sql         = "SELECT r0.id rid, r0.id, r0.roleName,  GROUP_CONCAT(r2.nickName SEPARATOR ' , ') as resources FROM Role r0	
							inner join RoleResource r1 on r1.idRole = r0.id
							inner join Resource r2 on r2.id = r1.idResource
							WHERE 1=1 ";
				
			$cache = Zend_Registry::get('cache');
			if ( ($totalCount = $cache->load('getRoles')) === false )
			{
				$sqlCount = "SELECT count(1) as total
									From ($sql GROUP BY r0.id) A";
				$res = $this->getDefaultAdapter()->fetchRow($sqlCount);
				$totalCount = $res['total'];
				$cache->save($totalCount);
			}
	
			$sqlCountWhere = "SELECT count(1) as total
									From ($sql $where GROUP BY r0.id) A";
			$res = $this->getDefaultAdapter()->fetchRow($sqlCountWhere);
			$totalCountWhere = $res['total'];
	
			$pag = $limit['limit'];
			$start = $limit['offset'];
			if($pag == -1 || $pag == '-1' ){
				$sql .= $where. " GROUP BY r0.id ORDER BY $sSort";
			} else {
				$sql .= $where. " GROUP BY r0.id ORDER BY $sSort LIMIT $pag OFFSET $start";
			}
			$rs = array('cursor'=>$this->getDefaultAdapter()->query($sql) , 'count'=>$totalCount, 'countWhere'=>$totalCountWhere );
			return $rs;
		} catch (Exception $e){
			throw new Exception( $e->getMessage() );
		}
	}
	
}