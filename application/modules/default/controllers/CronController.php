<?php
class Default_CronController extends Zend_Controller_Action
{

	public function init()
	{
		$Books = new Table_Books();
		$select = $Books->select()->where('adsEndDate < (select date_format(sysdate(), "%Y-%m-%d"))')
                                          ->where('adsActive = 1');
		$Books->fetchAll($select);
                
                

	}

}