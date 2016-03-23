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
	    	// obtenemos la página actual
    		$page 				= $this->_getParam('page', 1);
	    	// número de registros a mostrar por página
	    	$registros_pagina 	= 20;
	    	// número máximo de páginas a mostrar en el paginador
	    	$rango_paginas 		= 10;
	    	$adapter 			= new Zend_Paginator_Adapter_DbTableSelect($resultado);
	    	$paginator 			= new Zend_Paginator($adapter);
	    	// opciones
	    	$paginator  ->setItemCountPerPage($registros_pagina)
				    	->setCurrentPageNumber($page)
				    	->setPageRange($rango_paginas);
	    	
	    	//$this->view->productos = $paginador;
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
