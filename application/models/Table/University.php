<?php
class Table_University extends Zend_Db_Table_Abstract {

	protected $_name    = 'University';
	protected $_primary = 'id';


	/**
	 * Este mètodo obtiene el listado de universidades para mostrar en listado 
	 * @param string $where
	 * @throws Exception
	 * @return Ambigous <Zend_Db_Statement_Interface, Zend_Db_Statement, PDOStatement>
	 */
	public function getUniversityGrid($where = null)
	{
		try{
			$where       = is_null($where) ? 'AND 1=1 ' : $where;
			if(!is_string($where)){
				throw new Exception('Invalid parameter');
			}
			$sql         = "SELECT    u.id
									, u.id 
									, u.uniName As Name
									, s.schName 
									
							From University as u
							left join School as s on s.idUniversity = u.id
							WHERE 1=1
							$where
							ORDER BY uniName LIMIT 5000";
		
			$rs          = $this->getDefaultAdapter()->query($sql);
			return $rs;
		}catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}
	public function getOCWUniversityFilter(){
// 		$sql = "SELECT 
// 						 'University' as filterGroup
// 						, r0.uniName filterName
// 						, r0.id as idFilter
// 					FROM University r0 ;";
		$select = $this->select()->setIntegrityCheck(false);
		$select->from($this, array(new Zend_Db_Expr("'University' as filterGroup"), 'uniName as filterName', "id AS idFilter"));
		return $this->fetchAll($select)->toArray();
		
	}
}