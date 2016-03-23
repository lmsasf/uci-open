<?php
/**
 * Clase que mapea a la tabla Course
 */
class Table_BooksOCW extends Zend_Db_Table_Abstract {
	protected $_name    = 'BooksOCW';
	protected $_primary = array('idBooks', 'idOCW');
           
}