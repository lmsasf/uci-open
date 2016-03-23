<?php
require_once APPLICATION_PATH . '/modules/default/controllers/ErrorController.php';

class DefaultErrorControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
	
	public function setUp()
	{
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
	}
	
    public function testErrorAction404() {
    	
        $this->dispatch('/my-error-controller/error');
        $body = $this->getResponse()->getBody();
        $this->assertResponseCode('404');
        $this->assertContains('Page Not Found',$body);
    }
    
    public function testErrorAction500() {
    	 
    	$this->dispatch('/admin/auth/error');
    	$body = $this->getResponse()->getBody();
    	$this->assertResponseCode('500');
    	$this->assertContains('Internal Error',$body);
    }
    
    /**
     * @TODO Revisar, aparentemente no funciona como deberia
     */
    public function testXmlHttpRequets(){
    	$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    	$this->getRequest()->setHeader('X_REQUESTED_WITH', 'XmlHttpRequest');
    	$this->getRequest()->setMethod('POST');
     	$this->getRequest()->setPost(array(
     			'name' => 'name bar',
     			'email' => 'email x',
     	));
    	$this->dispatch('/admin/auth/error');
    	echo($this->getResponse()->getBody());
    	$this->assertResponseCode('500');
    }
}