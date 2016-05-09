<?php
class Table_Contact extends Zend_Db_Table_Abstract {

	protected $_name    = 'Contact';
	protected $_primary = 'id';


	public function getFields(){
		$sql = 'DESCRIBE Contact';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}

	/**
	 * Gets the data to show grid Contact
	 * @param string $where - string to apply into the where of the query
	 * @param array $sort - array('column'=>1, 'direction'=>'ASC')
	 * @param array $limit - array('limit'=>-1, 'offset'=>0)
	 * @param string $sSearch - search string
	 * @throws Exception
	 * @return array - Array que contiene un cursor, cantidad de registros y cantidad de registros filtrados
	 */
	public function getContactGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
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
			
			$columsSortable[1] = "LOWER(conFirstName)";
			$columsSortable[2] = "LOWER(conLastName)";
			$columsSortable[3] = "LOWER(conEmail)";
			$columsSortable[4] = "LOWER(conRole)";
			$columsSortable[5] = "LOWER(conCountry)";
			$columsSortable[6] = "LOWER(conInquiriType)";
			$columsSortable[7] = "LOWER(conComents)";
			$columsSortable[8] = "LOWER(conDate)";
			$columsSortable[9] = "LOWER(conRead)";
					
			if(!empty($sSearch)){
				$implode_array_d1 = implode(',', $columsSortable);
				$implode_array_d2 = implode(',', array_reverse($columsSortable));
				$sSearch = str_replace(' ', '%', $sSearch);
				$sSearch = strtolower($sSearch);
				$concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
				$where .= ' AND '.$concat_ws;
			}
			 
			$sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];	
			
			$sql         = "SELECT    r0.id
									, r0.id 
									, r0.conFirstName
									, r0.conLastName
									, r0.conEmail
									, r0.conRole
									, r3.`name`
									, r0.conInquiriType
									, r0.conComents
									, r0.conDate
									, r0.conRead
							From Contact r0 
							LEFT JOIN Countries r3 ON r0.conCountry = r3.code
							WHERE 1=1 " . $where;
			$cache = Zend_Registry::get('cache');
			if ( ($totalCount = $cache->load('getContactGrid')) === false )
			{
				$sqlCount = "SELECT count(1) as total
								From Contact r0 
								LEFT JOIN Countries r3 ON r0.conCountry = r3.code
								WHERE 1=1 ";
				$res = $this->getDefaultAdapter()->fetchRow($sqlCount);
				$totalCount = $res['total'];
				$cache->save($totalCount);
			}
	
			$sqlCountWhere = "SELECT count(1) as total
								From Contact r0 
								LEFT JOIN Countries r3 ON r0.conCountry = r3.code
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