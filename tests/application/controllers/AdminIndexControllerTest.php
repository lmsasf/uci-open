<?php
class AdminIndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

	public function setUp()
	{
		 
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
	}
	/**
	 *
	 */
	public function testIndexAction()
	{
		$params = array('action' => 'index', 'controller' => 'index', 'module' => 'admin');
		$urlParams = $this->urlizeOptions($params);
		$url = $this->url($urlParams);
		$this->dispatch($url);
		// assertions
		$this->assertModule($urlParams['module']);
		$this->assertController('auth');
		$this->assertAction('login');
		//$this->assertQueryContentContains("div#welcome h3", "This is your project's main page");
	}



}