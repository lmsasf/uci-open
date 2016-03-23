<?php
class Default_IndexController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education';
    public function init()
    {
        /* Initialize action controller here */
    	$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
    }

    public function indexAction()
    {
    	$description =truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", "Founded in 1965, The University of California, Irvine combines the strengths of a major research university with the bounty of an incomparable Southern California location. Over four remarkable decades, UCI has become internationally recognized for efforts that are improving lives through research and discovery, fostering excellence in scholarship and teaching, and engaging and enriching the community." ) , 150 );
    	$this->view->doctype('XHTML1_RDFA'); // controller
    	$this->view->headMeta()->setProperty('og:type', 'website');
    	$this->view->headMeta()->setProperty('og:title', 'UC Irvine, OpenCourseWare');
    	$this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'] );
    	$this->view->headMeta()->setProperty('og:description', $description);
    	$this->view->headMeta()->setName('description', $description );

		$Template = new Table_SectionTemplate();
		$design = $Template->fetchRow($Template->select()->where("secCode = 'HOME'"));

		$this->view->assign('homeDesign',$design['secTemplate']);
    }

}
