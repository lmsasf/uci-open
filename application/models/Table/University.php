<?php
class Table_University extends Zend_Db_Table_Abstract {

	protected $_name    = 'University';
	protected $_primary = 'id';

	/**
	 * This method gets the list of universities to show in list
	 * @param string $where
	 * @throws Exception
	 * @return Ambigous <Zend_Db_Statement_Interface, Zend_Db_Statement, PDOStatement>
	 */
	public function getUniversityGrid($where = null) {
		try {
			$where = is_null($where) ? 'AND 1=1 ' : $where;
			if(!is_string($where)){
				throw new Exception('Invalid parameter');
			}
			$sql         = "SELECT    u.id
									, u.id 
									, u.uniName AS Name
									, s.schName 
									
							FROM University AS u
							LEFT JOIN School AS s ON s.idUniversity = u.id
							WHERE 1=1
							$where
							ORDER BY uniName LIMIT 5000";
		
			$rs          = $this->getDefaultAdapter()->query($sql);
			return $rs;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function getOCWUniversityFilter() {
		$select = $this->select()->setIntegrityCheck(false);
		$select->from($this, array(new Zend_Db_Expr("'University' as filterGroup"), 'uniName as filterName', "id AS idFilter"));
		return $this->fetchAll($select)->toArray();
	}
}