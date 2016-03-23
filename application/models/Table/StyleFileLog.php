<?php
/**
 * Created by PhpStorm.
 * User: nelson
 * Date: 18/06/15
 * Time: 10:53
 */

class Table_StyleFileLog extends Zend_Db_Table_Abstract {

    protected $_name = 'StyleFileLog';
    protected $_primary = 'id';

    CONST STYLE_ACTION_RESTORE = 'restore';
    CONST STYLE_ACTION_EDIT = 'edit';

}