<?php
/**
 * Class that maps to the table BooksOCW
 */
class Table_BooksOCW extends Zend_Db_Table_Abstract {
	protected $_name    = 'BooksOCW';
	protected $_primary = array('idBooks', 'idOCW');
           
}