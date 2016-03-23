<?php
/**
 * Mapea la tabla OCW
 * @author damills
 *
 */
class Table_OCW extends Zend_Db_Table_Abstract {

	protected $_name    = 'OCW';
	protected $_primary = 'id';
	
	/**
	 * Obtiene los campos que hay en la tabla OCW
	 * se usa para hacer los inserts
	 * @return array
	 */
	public function getFields(){
		$sql = 'DESCRIBE OCW';
		$info = $this->getAdapter()->fetchAll($sql);
		$campos = array();
		foreach ($info as $campo){
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
	public function getOcwGrid($where = null, $sort=array('columna'=>1, 'direccion'=>'ASC'), $limit = array('limit'=>-1, 'offset'=>0), $sSearch='', $rol = null)
	{
			try {
				//validaciones de parámetros
				$where       = is_null($where) ? 'AND 1=1 ' : $where;
				
				$rolecondition = '';
				if($rol == 3){
					$rolecondition = ' and r0.idType = 3 ';
				}elseif($rol == 4){
					$rolecondition = ' and r0.idType in (1,4,6) ';
				}
				
				if( !is_array($sort) || !is_array($limit) || !is_string($sSearch) || !is_string($where)){ // verifico que los argumentos sean validos
					throw new Exception('Invalid parameters');	
				} else {
					if( !array_key_exists('columna', $sort) || !array_key_exists('direccion', $sort) || !array_key_exists('limit', $limit) || !array_key_exists('offset', $limit) ){
						throw new Exception('Invalid parameters');
					}
				}
				$columsSortable = array();
				$columsSortable[1] = "LOWER(r0.ocwTitle)";
				$columsSortable[2] = "LOWER(case when r0.ocwGolive = 0 then 'No' else 'Yes' end)";
				$columsSortable[3] = "LOWER(r4.typName)";
				$columsSortable[4] = "LOWER(r1.uniName)";
				$columsSortable[5] = "LOWER(r2.schName)";
				$columsSortable[6] = "LOWER(r3.depName)";				
				$columsSortable[7] = "r0.thumbnail";
				$columsSortable[8] = "IF(r0.ocwNotes is not null AND LENGTH(r0.ocwNotes)>0, 'Yes', 'No')";
						
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
										, r0.ocwTitle
										, case when r0.ocwGolive = 0 then 'No' else 'Yes' end ocwGolive
										, r4.typName
										, r1.uniName
										, r2.schName
										, r3.depName
										/* , r0.ocwUrl
										, r0.ocwBypassUrl */
										, r0.thumbnail
										, IF(ocwNotes is not null AND LENGTH(ocwNotes)>0, 'Yes', 'No') hasNote
								From OCW r0
								left join University r1 on r1.id = r0.idUniversity
								left join School r2 on r2.id = r0.idSchool
								left join Department r3 on r3.id = r0.idDepartment
								INNER join OCWTypes r4 on r4.id = r0.idType AND r4.visibility=1
								WHERE 1=1
								
								".$rolecondition;
				
				$cache = Zend_Registry::get('cache');
				if ( ($totalCount = $cache->load('getOcwGrid')) === false )
				{
					$sqlCount = "SELECT count(1) as total
									From OCW r0
									left join University r1 on r1.id = r0.idUniversity
									left join School r2 on r2.id = r0.idSchool
									left join Department r3 on r3.id = r0.idDepartment
									INNER join OCWTypes r4 on r4.id = r0.idType AND r4.visibility=1
									WHERE 1=1 ".$rolecondition;
					$res = $this->getDefaultAdapter()->fetchRow($sqlCount);
					$totalCount = $res['total'];
					$cache->save($totalCount);
				}
		
				$sqlCountWhere = "SELECT count(1) as total
									From OCW r0
									left join University r1 on r1.id = r0.idUniversity
									left join School r2 on r2.id = r0.idSchool
									left join Department r3 on r3.id = r0.idDepartment
									INNER join OCWTypes r4 on r4.id = r0.idType AND r4.visibility=1
									WHERE 1=1 $where ".$rolecondition;
				$res = $this->getDefaultAdapter()->fetchRow($sqlCountWhere);
				$totalCountWhere = $res['total'];
		
				$pag = $limit['limit'];
				$start = $limit['offset'];
				if($pag == -1 || $pag == '-1' ){
					$sql .= $where. " ORDER BY $sSort";
				} else {
					$sql .= $where. " ORDER BY $sSort LIMIT $pag OFFSET $start";
				}					 
				//$sql .=" $where	ORDER BY $sSort LIMIT $pag OFFSET $start";
			
				$rs = array('cursor'=>$this->getDefaultAdapter()->query($sql) , 'count'=>$totalCount, 'countWhere'=>$totalCountWhere );
				return $rs;
			} catch (Exception $e){
				throw new Exception( $e->getMessage() );
			}
	}
	/**
	 * Obtiene la Info de la tabla OCW, algunos campos 
	 * @param integer $id
	 * @return Mixed Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
	 */
	public function getOCWinfo($id = null ){
		$select = $this->select()->setIntegrityCheck(false);
		$select->from(array('r0'=> 'OCW'), array('r0.ocwTitle', 'r0.ocwDescription'))
				->joinLeft(array('r1'=>'University'),'r1.id = r0.idUniversity', array('r1.uniName'))
				->joinLeft(array('r2'=> 'School'), 'r2.id = r0.idSchool', array('r2.schName'))
				->joinLeft(array('r3'=> 'Department'), 'r3.id = r0.idDepartment', array('r3.depName'))
		;
		if(!is_null($id)){ // añado el where
			$select->where('r0.id = ? ', $id );
			return $this->fetchRow($select);
		}else{
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
	public function getOCWSearch($search = null, $filter = 'all', $category = 'all', $language = 'all' ){
		
		$fieldsearch = "LOWER(r0.ocwTitle), LOWER(r0.ocwKeywords), LOWER(r0.ocwDescription)";
		$fieldsearchreverse = "LOWER(r0.ocwDescription), LOWER(r0.ocwKeywords), LOWER(r0.ocwTitle)";		
		//$sSearch = strtolower($search);
		$sSearch = str_replace(' ', '%', $search);
		$sSearch = addslashes(strtolower($sSearch));
		$concat_ws = "CONCAT_WS(' ',".$fieldsearch .','. $fieldsearchreverse .") LIKE '%$sSearch%'" ;
		
		$select = $this->select()->setIntegrityCheck(false);
		$select->from(  array('r0'=>'OCW') ,
					array('r0.id', 'r0.ocwTitle', 	'r0.ocwTitleEncode', 	'r0.ocwGolive', 	'r0.ocwDescription', 	'r0.ocwKeywords', 'r0.idType')					
				)
				->joinInner(array('r1'=>'OCWTypes'), 'r1.id=r0.idType AND r1.visibility=1', array('r1.typName'))
				->joinInner(array('r8'=>'Language'), "r8.id = r0.idLanguage", array('r8.lanName'))
				->joinLeft(array( 'r2' =>'University' ), 'r2.id = r0.idUniversity', array('uniName') )
				->joinLeft(array( 'r3' =>'School' ), 'r3.id = r0.idSchool', array('schName') )
				->joinLeft(array( 'r4' =>'Department' ), 'r4.id = r0.idDepartment', array('depName') )
				->joinLeft(array( 'r5' =>'Author'), 'r5.idOCW = r0.id', '')
				->joinLeft(array( 'r6' =>'Person'), 'r6.id = r5.idPer', array(new Zend_Db_Expr("GROUP_CONCAT( CONCAT_WS(' ', r6.perFirstName, r6.perLastName ) ORDER BY r5.autSequence DESC SEPARATOR ', ') AS author")))
				->joinLeft(array( 'r9' =>'Course'), 'r0.id = r9.idOCW', array('r9.ocwBypassUrlCourse'))
				->joinLeft(array( 'r10' =>'Lecture'), 'r0.id = r10.idOCW', array('r10.ocwBypassUrlLecture'))
				->joinLeft(array( 'r11' =>'File'), 'r0.id = r11.idOCW', array('r11.ocwUrlFile'))
				->where($concat_ws)
				->where("r0.ocwGolive = ?", 1)
				->group("r0.id")
				->order('r0.ocwTitle');
		
		if( $filter != 'all' ){
			$select->where( 'r1.typName = ?' , $filter) ;
		}
		if( $category != 'all' ){
			$select->joinInner(array('r7'=> 'OCWCategory'), 'r7.idOCW = r0.id AND r7.idCat=' . $category , array('r7.idCat'));
			//$select->where( 'r7.idCat = ?' , $category) ;
		}

		if( $language != 'all' ){
			$select->where( 'r8.lanName = ?' , $language) ;
		}
		return $select;
	
	}
	/**
	 * Remueve un OCW del cache
	 * @param integer $idOCW
	 * @throws Exception
	 * @return boolean
	 */
	public function removeFromCache($idOCW){
		//try {
			if( is_null($idOCW) || empty($idOCW) ){
				return false;
			}
			$ocw = $this->fetchRow("id=$idOCW");
			
			$file = $ocw->ocwTitleEncode . '.html';
			$package = $ocw->ocwTitleEncode . '.imscc';
			$output = array();
			$output2 = array();
			$runCommand = 'find ' . CACHE_PUBLIC . ' -type f -name "'.$file.'*" -exec rm -fv {} \;';
			exec( $runCommand, $output );
			// eliminar el paquete si ya existe
			$root = $_SERVER['DOCUMENT_ROOT'];
			$root = $root.'/packages/';
			$runCommand = 'find ' . $root . ' -type f -name "*'.$package.'" -exec rm -fv {} \;';
			exec( $runCommand, $output2 );
			// Tomar como salida cierta la eliminación del cache no del paquete
			if( empty( $output ) ) {
				return false;
			}else{
				return true;
			}

	}
	/**
	 * Remueve los INDEX del cache
	 * @param integer $idType
	 * @return boolean
	 */
	public function removeCacheIndex($idType){
			if(!is_null($idType)){
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
			exec( $runCommand, $output );
			return true;
			} else {
				throw new Exception('idType null');
			}
	}
	
	public function getOcwRss($filter = 'all', $limit = 100 ){
	
	
		$select = $this->select()->setIntegrityCheck(false);
		$select->from(  array('r0'=>'OCW') ,
				array('r0.id', 'r0.ocwTitle', 	'r0.ocwTitleEncode', 	'r0.ocwGolive', 	'r0.ocwDescription', 	'r0.ocwKeywords', 'r0.idType', 'r0.thumbnail', 'r0.ocwLicense')
		)
		->joinInner(array('r1'=>'OCWTypes'), 'r1.id=r0.idType AND r1.visibility=1', array('r1.typName'))
		->joinInner(array('r8'=>'Language'), "r8.id = r0.idLanguage", array('r8.lanName'))
		->joinLeft(array( 'r2' =>'University' ), 'r2.id = r0.idUniversity', array('uniName') )
		->joinLeft(array( 'r3' =>'School' ), 'r3.id = r0.idSchool', array('schName') )
		->joinLeft(array( 'r4' =>'Department' ), 'r4.id = r0.idDepartment', array('depName') )
		->joinLeft(array( 'r5' =>'Author'), 'r5.idOCW = r0.id', '')
		->joinLeft(array( 'r6' =>'Person'), 'r6.id = r5.idPer', array(new Zend_Db_Expr("GROUP_CONCAT( CONCAT( IF(r6.perEmail IS NOT NULL, r6.perEmail, ''), ' (',CONCAT_WS(' ', r6.perFirstName, r6.perLastName ) , ') ' ) ORDER BY r5.autSequence DESC SEPARATOR ', ') AS author")))
		->joinLeft(array( 'r9' =>'Course'), 'r0.id = r9.idOCW', array('r9.ocwBypassUrlCourse'))
		->joinLeft(array( 'r10' =>'Lecture'), 'r0.id = r10.idOCW', array('r10.ocwBypassUrlLecture'))
		->joinLeft(array( 'r11' =>'File'), 'r0.id = r11.idOCW', array('r11.ocwUrlFile'))
		->where("r0.ocwGolive = ?", 1) // publicadas
		->group("r0.id")
		->order('r0.id DESC');
	
		if( $filter != 'all' && $filter != 'full' ){
			$select->where( 'r1.typName = ?' , $filter);
			
		}
		$select->where( 'r1.typName != ?' , 'File') ;
		$select->joinInner(array('r7'=> 'OCWCategory'), 'r7.idOCW = r0.id ' , array('r7.idCat'));
		if( $filter != 'full' ){
			$select->limit($limit);
		}
		return $this->fetchAll($select);
	
	}

}