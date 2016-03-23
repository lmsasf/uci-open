<?php
class Default_RedirectorController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';
	
	public function init()
	{
		/* Initialize action controller here */
		$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
		$description =truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", "Founded in 1965, The University of California, Irvine combines the strengths of a major research university with the bounty of an incomparable Southern California location. Over four remarkable decades, UCI has become internationally recognized for efforts that are improving lives through research and discovery, fostering excellence in scholarship and teaching, and engaging and enriching the community." ) , 150 );
		$this->view->headTitle('UC Irvine, OpenCourseWare');
		$this->view->doctype('XHTML1_RDFA'); // controller
		$this->view->headMeta()->setProperty('og:type', 'website');
		$this->view->headMeta()->setProperty('og:title', 'UC Irvine, OpenCourseWare');
		$this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'] );
		$this->view->headMeta()->setProperty('og:description', $description);
		$this->view->headMeta()->setName('description', $description );		
	}

	public function indexAction()
	{

	}

	public function redirectAction()
	{
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$id 	= $this->getRequest()->getParam('id', null);
		$type 	= $this->getRequest()->getParam('type', null);
		// redirección para RSS de cursos
		$redirector = $this->_helper->getHelper('Redirector');
		if($id=='rss' && $type == 'courses'){
			$this->_forward("index",'rss','default', array('strType'=>'course'));
			return;
		}
		
		$array_id=array_filter(explode('-', $id));
		$tmp = array();
		foreach ($array_id as $v){
			$tmp[] = strtolower($v);
		}
		$array_id = $tmp;
		$str = implode(' ', $array_id);
		$OCW = new Table_OCW();
		$type = substr($type,0,-1);
		$select= $OCW->getOCWSearch($str);
		$results = $OCW->fetchAll($select);
		$contador = $results->count();
		
		if($contador == 1 ){ // redirigir a la página correcta del curso
			$curso = $results->toArray();
			$url = '/'.strtolower($curso[0]['typName']).'s/'.$curso[0]['ocwTitleEncode'].'.html';
			$redirector->setCode(301)->gotoUrl($url);
			return;
		}elseif ($contador > 1 ){ // enviar al buscador con los parametros adecuados
			$redirector->setCode(301)
					   ->gotoSimple('results',
									'search',
									null,
									array('keyword' => $str)
			);
			return;
		}else{ // no hay resultados
			throw new Zend_Controller_Action_Exception('This page does not exist', 404);
		}
		
	}
}
