<?php
class Admin_FrontendController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('admin');
    }
    /**
     * Listado de textos
     */
    public function indexAction()
    {
        $this->view->headTitle('Frontend :: Dashboard');
        // action body
    }

}