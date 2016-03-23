<?php
/*class Default_FileController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';
	
	public function init()
	{
		$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
	}

	public function indexAction()
	{
		
	}
	public function viewAction(){
		if($this->getRequest()->getParam('id') ==='index'){
			throw new Exception('Invalid file');		
		}else{
			//Zend_Debug::dump($this->getRequest()->getParams());
			$Files = new Table_OCW();
			
			$ocwTitleEncode = $this->getRequest()->getParam('id');
			$golive = $this->getRequest()->getParam('golive', 1); // por defecto muestra el publicado
			
			$select = $Files->select()->setIntegrityCheck(false)
				->from( array('r0'=>'OCW'), array('ocwTitle', 'ocwTitleEncode', 'id', 'thumbnail', 'ocwDescription', 'ocwKeywords') )
				->joinLeft(array('r1'=>'File'), "r1.idOCW = r0.id", array('ocwUrlFile'))
				->where( "idType =?" , 2 )
				->where( 'ocwGolive=?', 1)
				->where( 'ocwTitleEncode = ?', $ocwTitleEncode )
				->order( 'ocwTitle' )
			;
			
			$file = $Files->fetchRow( $select );
			if(is_null($file)) {
				throw new Exception('The requested file is invalid or does not exist ' . $select->assemble());
			}
			//d($select->assemble());
			$this->view->assign('File'   , $file  );
			
			$this->view->headTitle($file->ocwTitle);
			$this->view->headMeta()->setName('keywords', $this->_KEYWORDS . $file->ocwKeywords );
			$this->view->headMeta()->setName('description', strip_tags($file->ocwDescription) );
			$this->view->title = $file->ocwTitle; // sirve para el breadcrums						
			
		}
	}

}*/