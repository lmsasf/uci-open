<?php

class Admin_FcontactusController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('admin');
    }

    public function indexAction()
    {

        $this->view->headTitle('Contact Us');
        $ContactUs= new Table_ContactUsSettings();
        $contact = $ContactUs->fetchAll('Id=1');
        $this->view->assign('contact', $contact);
      
    }
    public function savecontactusAction()
    {
        $this->_helper->layout()->setLayout('empty');
        // no necesita vista para renderizarse
        $this->_helper->viewRenderer->setNoRender();
        $ContactUs = new Table_ContactUsSettings();
        $tr = $ContactUs->getAdapter()->beginTransaction();
        try {
            $data  = $this->getRequest()->getParam('data' , null);
            $ContactUsRow = null;
            $ContactUsRow = $ContactUs->fetchRow($ContactUs->select()->where('Id = 1'));

            foreach($data as $dato){
                if(isset($dato['campo'])){
                    if($dato['campo']!== 'accion'){
                        $ContactUsRow->$dato['campo'] = $dato['valor'];
                    }
                }
            }
            $idContact = $ContactUsRow->save();

            $tr->commit();
            
        } catch (Exception $e){
            $tr->rollBack();
            echo( $e->getMessage() );
        }
    }
}