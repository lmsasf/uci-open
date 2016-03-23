<?php
class Table_AuthorOCW extends Zend_Db_Table_Abstract {
	
	protected $_name    = 'AuthorOCW';
	protected $_primary = array('idPer', 'idDeg', 'idOCW');
	
}