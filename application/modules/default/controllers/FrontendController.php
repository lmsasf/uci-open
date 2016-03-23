<?php
class Default_FrontendController extends Zend_Controller_Action
{
    public function getfooterdesignAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Template = new Table_SectionTemplate();
        $design = $Template->fetchRow($Template->select()->where("secCode = 'FOO'"));
        echo Zend_Json_Encoder::encode($design['secTemplate']);
    }

    public function getfooterdataAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Footer = new Table_Footer();
        $data = $Footer->getFooterData();

        echo Zend_Json_Encoder::encode($data);
    }

    public function getheaderdesignAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Template = new Table_SectionTemplate();
        $design = $Template->fetchRow($Template->select()->where("secCode = 'HEAD'"));
        echo Zend_Json_Encoder::encode($design['secTemplate']);
    }

    public function getheaderdataAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Header = new Table_Footer();
        $data = $Header->getHeaderData();

        echo Zend_Json_Encoder::encode($data);
    }

    public function gethomedesignAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Template = new Table_SectionTemplate();
        $design = $Template->fetchRow($Template->select()->where("secCode = 'HOME'"));
        echo Zend_Json_Encoder::encode($design['secTemplate']);
    }

    public function gethomedataAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Home = new Table_Home();
        $data = $Home->getHomeData();

        echo Zend_Json_Encoder::encode($data);
    }

    public function getslidecountAction()
    {
        $this->_helper->layout()->setLayout('empty');
        $this->_helper->viewRenderer->setNoRender();

        $Home = new Table_Home();
        $data = $Home->getSlideCount();

        echo Zend_Json_Encoder::encode($data['count']);
    }
}