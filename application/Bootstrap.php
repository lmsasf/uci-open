<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
//     protected function _initNavigation()
//     {
//         $this->bootstrap('layout');
//         $this->bootstrap('view');
//         $view = $this->getResource('view');
//         $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
//         $navigation = new Zend_Navigation($config);
//         $view->navigation($navigation);
//     }
	
    protected function _initApplication()
    {
    	$this->bootstrap('frontController');
    	date_default_timezone_set("America/Argentina/Buenos_Aires");

    	Zend_Loader::loadClass('Zend_Session_Namespace');

        //Carga el autoloader para el namespace Asf
    	$loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Asf_');
        // CONFIGURA CACHE //
        $this->setupCache();
        $this->initDb();
    }
	protected function _initRoutes(){
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
	
		// ruta para los viejos cursos		
	    $route = new Zend_Controller_Router_Route_Regex(
	        '(.+)/(.+)\.aspx',
	        array(
					'controller'	=> 'redirector',
					'action'     	=> 'redirect',
					'module'		=> 'default'
	        ),
	        array(
	            1 => 'type',
	        	2 => 'id'
	        ),
	        'courses/%s.aspx'
	    );
	    $router->addRoute('coursesviewASPX', $route);	
	    
	    
	    //---------------------------------------------
	    // Rss full
	    $route = new Zend_Controller_Router_Route_Static(
	    		'rss-full',
	    		array('controller' => 'rss', 'action' => 'index', 'module'=> 'default', 'strType'=>'full')
	    );	 
	    $router->addRoute('fullRSS', $route);
	    // Rss cursos
	    $route = new Zend_Controller_Router_Route_Static(
	        	'courses/rss',
   				 array('controller' => 'rss', 'action' => 'index', 'module'=> 'default', 'strType'=>'Course')
	    );
	    $router->addRoute('coursesRSS', $route);	
	    // broken link checker
	    $route = new Zend_Controller_Router_Route_Static(
	    		'broken-link-checker.json',
	    		array('controller' => 'blc', 'action' => 'index', 'module'=> 'default')
	    );
	    $router->addRoute('blc', $route);
	    // nuevos cursos
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'courses/(.+)\.html',
	    		array(
	    				'controller'	=> 'courses',
	    				'action'     	=> 'view',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'courses/%s.html'
	    );
	    $router->addRoute('coursesviewHTML', $route);
	    // collections
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'collections/(.+)\.html',
	    		array(
	    				'controller'	=> 'collections',
	    				'action'     	=> 'view',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'collections/%s.html'
	    );
	    $router->addRoute('collectionsviewHTML', $route);
	    // conferences
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'conferences/(.+)\.html',
	    		array(
	    				'controller'	=> 'conferences',
	    				'action'     	=> 'view',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'conferences/%s.html'
	    );
	    $router->addRoute('conferencesviewHTML', $route);
	    // files
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'getfile/(.+)\.html',
	    		array(
	    				'controller'	=> 'file',
	    				'action'     	=> 'view',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'getfile/%s.html'
	    );
	    $router->addRoute('filesviewHTML', $route);
	    // lectures
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'lectures/(.+)\.html',
	    		array(
	    				'controller'	=> 'lectures',
	    				'action'     	=> 'view',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'lectures/%s.html'
	    );
	    $router->addRoute('lecturesviewHTML', $route);	    
	    $router->addDefaultRoutes();
	    // download OCW
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'download/(.+)\.imscc',
	    		array(
	    				'controller'	=> 'download',
	    				'action'     	=> 'index',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'download/%s.imscc'
	    );
	    $router->addRoute('downloadview', $route);
	    $router->addDefaultRoutes();	
		
	    $route = new Zend_Controller_Router_Route_Regex(
	    		'download/(.+)\.zip',
	    		array(
	    				'controller'	=> 'download',
	    				'action'     	=> 'index',
	    				'module'		=> 'default'
	    		),
	    		array(
	    				1 => 'id'
	    		),
	    		'download/%s.zip'
	    );
	    $router->addRoute('downloadviewZIP', $route);
	    $router->addDefaultRoutes();			
		
	}
	
    protected function _initBaseUrl()
    {
        $this->bootstrap("frontController");
        $front = $this->getResource("frontController");
        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
    }

    protected function _initPlugins()
    {
// 		$layout  = new Asf_Controller_Plugin_Modularlayout();
//     	$this->frontController->registerPlugin($layout);
        // ACL //
         $acl  = new Asf_Controller_Plugin_Acl();
         $this->frontController->registerPlugin($acl); 
         $helper = new Asf_Controller_Helper_Acl();
         $helper->setAcl();
		// Cache plugin
		$Cache = new Asf_Controller_Plugin_Cache();
		$this->frontController->registerPlugin($Cache);
		// Login de admin
		$auth = new Asf_Controller_Plugin_Authentication();
		$this->frontController->registerPlugin($auth);
    }

    protected function _initView() {
    	
    	$this->view = new Zend_View();
    	
    	$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    	Zend_Controller_Front::getInstance()->setParam('disableOutputBuffering', true);
    	$this->view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
    	$this->view->addHelperPath("Asf/View/Helper", "Asf_View_Helper");
    	    	
    	$this->view->jQuery()
        ->enable()
        ->addJavascriptFile($baseUrl . '/backend/js/jquery/jquery-1.9.0.js')
        ->addJavascriptFile($baseUrl . '/backend/js/jquery/jquery-migrate-1.0.0.js')
    	->addJavascriptFile($baseUrl . "/backend/js/jquery/jquery-ui-1.10.3.custom.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/spinner/jquery.mousewheel.js")
    	->addJavascriptFile($baseUrl . '/backend/js/plugins/forms/uniform.js')
     	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/jquery.cleditor.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/jquery.validationEngine-en.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/jquery.validationEngine.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/jquery.tagsinput.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/autogrowtextarea.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/jquery.maskedinput.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/jquery.inputlimiter.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/forms/chosen.jquery.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/wizard/jquery.form.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/wizard/jquery.validate.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/wizard/jquery.form.wizard.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/tables/jquery.dataTables.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/tables/tablesort.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/tables/resizable.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.tipsy.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.collapsible.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.progress.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.timeentry.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.colorpicker.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.jgrowl.js")
     	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.breadcrumbs.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.sourcerer.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.msgBox.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/fullcalendar.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/elfinder.min.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/messagesHelper.js")
    	->addJavascriptFile($baseUrl . "/backend/js/plugins/ui/jquery.blockUI.js")
    	;
    	   	
    	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    	$viewRenderer->setView($this->view);
    }
    
    
    protected function _initAutoloader()
    {
    	$autoloader = Zend_Loader_Autoloader::getInstance();
    	//$autoloader->setFallbackAutoloader(true);
    	$autoloader->registerNamespace('Table_');
    	$autoloader->registerNamespace('Entity_');
    	return $autoloader;
    }    
    
   
    /**
    * 
    * Setea el adapter para toda la aplicación
    * @param array $params Array de parametros
    */
    private function initDb()
    {
		$profiler = new Zend_Db_Profiler_Firebug('All Database Queries:');
		$profiler->setEnabled(APPLICATION_ENV == 'development'? false : true);
		$rs = $this->getPluginResource("db");
		$db = $rs->getDbAdapter();
		$db->setProfiler($profiler);
		//$db->query("SET NAMES utf8");
    }

    private function setupCache()
    {
        $frontendOptions = array
        (
            'lifetime' => 3600,                // cache lifetime 1 hora
            'automatic_serialization' => true  // es mas lento serializar
        );

        $backendOptions = array('cache_dir' => CACHE_PATH);

        $cache = Zend_Cache::factory
        (
            'Output',
            'File',
            $frontendOptions,
            $backendOptions
        );

        Zend_Registry::set('cache', $cache);
    }
}

?>
