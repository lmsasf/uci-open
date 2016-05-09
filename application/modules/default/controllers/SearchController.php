<?php
class Default_SearchController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';
	
    public function init()
    {
        /* Initialize action controller here */
    	$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
    }

    public function resultsAction(){
    	     	    
    		$search 			= $this->getRequest()->getParam('keyword');
    		$ocwtype 			= $this->getRequest()->getParam('ocwtype' , 'all');
    		$category 			= $this->getRequest()->getParam('category', 'all');
    		$language 			= $this->getRequest()->getParam('language', 'all');
    		
    		$this->view->headTitle('Search result for: "' . $search . '"');
    		
    		$OCW 				= new Table_OCW();
    		$resultado 			= $OCW->getOCWSearch($search, $ocwtype, $category, $language); 
	    	// get the current page
    		$page 				= $this->_getParam('page', 1);
	    	// number of records to display per page
	    	$registros_pagina 	= 20;
	    	// maximum number of pages to display in the paginated
	    	$rango_paginas 		= 10;
	    	$adapter 			= new Zend_Paginator_Adapter_DbTableSelect($resultado);
	    	$paginator 			= new Zend_Paginator($adapter);
	    	// options
	    	$paginator  ->setItemCountPerPage($registros_pagina)
				    	->setCurrentPageNumber($page)
				    	->setPageRange($rango_paginas);
	    	
	    	$Categories = new Table_Category();
	    	$categories = $Categories->getTreeWithPaths();
	    	$this->view->assign('categories', $categories);
	    	
	    	$Language = new Table_Language();
	    	$this->view->assign('languages'		, $Language->fetchAll());
	    	
	    	$this->view->assign('searchresult'	, $paginator);
	    	$this->view->assign('page'			, $page);
	    	$this->view->assign('keyword'		, $search);
	    	$this->view->assign('ocwtype'		, $ocwtype);
	    	$this->view->assign('idCat'			, $category);
	    	$this->view->assign('lanName'		, $language);
	    	
    }
       	
}
