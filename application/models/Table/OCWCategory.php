<?php
class Table_OCWCategory extends Zend_Db_Table_Abstract {
	protected $_name    = 'OCWCategory';
	protected $_primary = array('idOCW', 'idCat');
}