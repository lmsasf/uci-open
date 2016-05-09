<?php
class Table_Person extends Zend_Db_Table_Abstract {

	protected $_name    = 'Person';
	protected $_primary = 'id';

	/**
     * Method to fill the grid persons
     */
	
	public function getPersonsGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='') {
		try {
			//parameters validations
			$where       = is_null($where) ? 'AND 1=1 ' : $where;
			
			if( !is_array($sort) || !is_array($limit) || !is_string($sSearch) || !is_string($where)) { // verify that the arguments are valid
				throw new Exception('Invalid parameters');	
			} else {
				if( !array_key_exists('columna', $sort) || !array_key_exists('direccion', $sort) || !array_key_exists('limit', $limit) || !array_key_exists('offset', $limit) ) {
					throw new Exception('Invalid parameters');
				}
			}
		
			$columsSortable = array();
			$columsSortable[1] = "UPPER(CONCAT_WS( ' ', perFirstName, perLastName)) ";
			$columsSortable[2] = 'perEmail';
			$columsSortable[3] = 'perEmail1';
			$columsSortable[4] = 'perUrlPersonal';
			$columsSortable[5] = 'perPhone';
			$columsSortable[6] = 'perCity';
			$columsSortable[7] = 'perState';
			$columsSortable[8] = 'perCountry';
			$columsSortable[9] = 'perUrlPersonal';
			
			if(!empty($sSearch)) {
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
									, UPPER(CONCAT_WS( ' ', perFirstName, perLastName)) As Name
									, perEmail
									, perEmail1
									, perUrlPersonal
									, perPhone
									, perCity
									, perState
									, perCountry
									
							FROM Person
							WHERE 1=1
							$where
							";
			
			$cache = Zend_Registry::get('cache');
			if ( ($totalCount = $cache->load('getPersonsGrid')) === false )
			{
				$sqlCount = "SELECT count(1) as total
								FROM Person 
								WHERE 1=1 ";
				$res = $this->getDefaultAdapter()->fetchRow($sqlCount);
				$totalCount = $res['total'];
				$cache->save($totalCount);
			}
	
			$sqlCountWhere = "SELECT count(1) as total
								FROM Person
								WHERE 1=1 $where ";
			$res = $this->getDefaultAdapter()->fetchRow($sqlCountWhere);
			$totalCountWhere = $res['total'];
	
			$pag = $limit['limit'];
			$start = $limit['offset'];
			if($pag == -1 || $pag == '-1' ) {
				$sql .= $where. " ORDER BY $sSort";
			} else {
				$sql .= $where. " ORDER BY $sSort LIMIT $pag OFFSET $start";
			}
					
			$rs = array('cursor'=>$this->getDefaultAdapter()->query($sql) , 'count'=>$totalCount, 'countWhere'=>$totalCountWhere );
			return $rs;
		} catch (Exception $e) {
			throw new Exception( $e->getMessage() );
		}
	}
}