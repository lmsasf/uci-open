<?php
class Table_Testimonials extends Zend_Db_Table_Abstract {

	protected $_name    = 'Testimonials';
	protected $_primary = 'idTes';


	public function getFields(){
		$sql = 'DESCRIBE Testimonials';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
			$campos[] = $campo['Field'];
		}
		return $campos;
	}

	/**
	 * Gets the data to show grid testimonial
	 * @param string $where - string to apply into the where of the query
	 * @param array $sort - array('column'=>1, 'direction'=>'ASC')
	 * @param array $limit - array('limit'=>-1, 'offset'=>0)
	 * @param string $sSearch - string to search
	 * @throws Exception
	 * @return array - Array containing a cursor, number of records and number of filtered records
	 */
	public function getTestimonialGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='')
	{
		try {
			//parameters validation
			$where       = is_null($where) ? 'AND 1=1 ' : $where;
			
			if( !is_array($sort) || !is_array($limit) || !is_string($sSearch) || !is_string($where)){ // verified arguments are valid
				throw new Exception('Invalid parameters');	
			} else {
				if( !array_key_exists('columna', $sort) || !array_key_exists('direccion', $sort) || !array_key_exists('limit', $limit) || !array_key_exists('offset', $limit) ){
					throw new Exception('Invalid parameters');
				}
			}
			
			$columsSortable = array();
			
			$columsSortable[1] = "LOWER(ocwTitle)";
			$columsSortable[2] = "LOWER(case when r0.tesVisible = 0 then 'No' else 'Yes' end)";
			$columsSortable[3] = "LOWER(r2.typName)";
			$columsSortable[4] = "LOWER(tesName)";
			$columsSortable[5] = "LOWER(tesCountry)";
			$columsSortable[6] = "LOWER(tesEmail)";
			$columsSortable[7] = "LOWER(tesQuestion1)";
			$columsSortable[8] = "LOWER(tesQuestion2)";
			$columsSortable[9] = "LOWER(tesQuestion3)";
			$columsSortable[10] = "LOWER(tesTestimonial)";
			$columsSortable[11] = "LOWER(case when r0.tesMarketing = 0 then 'No' else 'Yes' end)";
			$columsSortable[12] = "LOWER(case when r0.tesContact = 0 then 'No' else 'Yes' end)";
			$columsSortable[13] = "tesDate";
					
			if(!empty($sSearch)){
				$implode_array_d1 = implode(',', $columsSortable);
				$implode_array_d2 = implode(',', array_reverse($columsSortable));
				$sSearch = str_replace(' ', '%', $sSearch);
				$sSearch = strtolower($sSearch);
				$concat_ws = "CONCAT_WS(' ',".$implode_array_d1 .','. $implode_array_d2 .") LIKE '%$sSearch%'" ;
				$where .= ' AND '.$concat_ws;
			}
			 
			$sSort	= $columsSortable[$sort['columna']] . ' ' . $sort['direccion'];	
			
			$sql         = "SELECT    r0.idTes
									, r0.idTes 
									, r1.ocwTitle
									, CASE WHEN r0.tesVisible = 0 THEN 'No' else 'Yes' END tesVisible
									, r2.typName
									, r0.tesName
									, r3.`name`
									, r0.tesEmail
									, r0.tesQuestion1
									, r0.tesQuestion2
									, r0.tesQuestion3
									, r0.tesTestimonial
									, CASE WHEN r0.tesMarketing = 0 THEN 'No' ELSE 'Yes' END tesMarketing
									, CASE WHEN r0.tesContact = 0 THEN 'No' ELSE 'Yes' END tesContact
									, tesDate
							From Testimonials r0 
								INNER JOIN OCW r1 on r0.idOCW = r1.id 
								INNER JOIN OCWTypes r2 on r2.id = r1.idType
								LEFT JOIN Countries r3 ON r0.tesCountry = r3.code
							WHERE 1=1 " . $where;
			$cache = Zend_Registry::get('cache');
			if ( ($totalCount = $cache->load('getTestimonialGrid')) === false )
			{
				$sqlCount = "SELECT count(1) as total
								From Testimonials r0 inner join OCW r1 on r0.idOCW = r1.id inner join OCWTypes r2 on r2.id = r1.idType LEFT JOIN Countries r3 ON r0.tesCountry = r3.code
								WHERE 1=1 ";
				$res = $this->getDefaultAdapter()->fetchRow($sqlCount);
				$totalCount = $res['total'];
				$cache->save($totalCount);
			}
	
			$sqlCountWhere = "SELECT count(1) as total
								From Testimonials r0 inner join OCW r1 on r0.idOCW = r1.id inner join OCWTypes r2 on r2.id = r1.idType LEFT JOIN Countries r3 ON r0.tesCountry = r3.code
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