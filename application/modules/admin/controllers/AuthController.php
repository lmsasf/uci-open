<?php
class Admin_AuthController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('admin');

    }

    public function indexAction()
    {
        // redirecciona a login
        $this->_forward('login');
    }
    /**
     *
     * Formulario de Login
     */
	public function loginAction()
	{
		$this->view->headTitle('Login');

		$this->view->assign('isLoggedIn', Table_User::isLoggedIn() );
		$this->view->assign('identity', Table_User::getIdentity() );
		if ($this->getRequest()->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$nick = trim($filter->filter($this->getRequest()->getPost('usrName')));
			$password = trim($filter->filter($this->getRequest()->getPost('usrPassword')));

			try{
				$user = new Table_User();
				$user->login($nick, $password);
				$this->_forward('index', 'index', 'admin');
			} catch(Exception $e){
				$responseLogin = $e->getMessage();
				$this->view->assign( 'usrName', $this->getRequest()->getPost('usrName') );
				$this->view->error = $responseLogin;
			}
		}

	}
	/**
	 *
	 * Logout
	 */
	public function logoutAction()
	{
		$user = new Table_User();
		$user->logout();
		$this->_forward('login', 'auth', 'admin');
	}
	public function noauthAction(){
		$this->getResponse()->setHttpResponseCode(401);
	}
	/**
	 * Sólo para propositos de testing del Error controller
	 */
	public function errorAction(){
		$this->_helper->layout()->setLayout('empty');
		$this->_helper->viewRenderer->setNoRender();
		throw new Exception('Error 500');
	}
}
