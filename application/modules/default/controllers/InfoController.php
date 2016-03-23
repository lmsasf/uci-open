<?php
class Default_InfoController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';
	
	public function init()
	{
		/* Initialize action controller here */
		$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
	}

	public function indexAction()
	{
		$description =truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", "Founded in 1965, The University of California, Irvine combines the strengths of a major research university with the bounty of an incomparable Southern California location. Over four remarkable decades, UCI has become internationally recognized for efforts that are improving lives through research and discovery, fostering excellence in scholarship and teaching, and engaging and enriching the community." ) , 150 );
		$this->view->headTitle('About us');
		$this->view->doctype('XHTML1_RDFA'); // controller
		$this->view->headMeta()->setProperty('og:type', 'website');
		$this->view->headMeta()->setProperty('og:title', 'About us');
		$this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/info' );
		$this->view->headMeta()->setProperty('og:description', $description);
		$this->view->headMeta()->setName('description', $description );

		$Section = new Table_SectionTexts();
		$this->view->assign('about_us', $Section->fetchRow($Section->select()->where('secActive = 1 AND section = 1')));
		$this->view->assign('faq', $Section->fetchRow($Section->select()->where('secActive = 1 AND section = 2')));
		$this->view->assign('term_of_use', $Section->fetchRow($Section->select()->where('secActive = 1 AND section = 3')));
		$this->view->assign('awards', $Section->fetchRow($Section->select()->where('secActive = 1 AND section = 5')));
		$this->view->assign('faculty', $Section->fetchRow($Section->select()->where('secActive = 1 AND section = 6')));
		$this->view->assign('our_team', $Section->fetchRow($Section->select()->where('secActive = 1 AND section = 7')));

	}

}