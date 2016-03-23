<?php
/**
 * Implementación de cache
 * @author damills
 *
 */
class Asf_Controller_Plugin_Cache extends Zend_Controller_Plugin_Abstract
{
	
	private $_whitelist;
	private $_cachear = false;
	
	public function __construct()
	{
		// ACCTIONES PERMITIDAS POR LISTA BLANCA //
		$this->_whitelist = array
		(
				'default_testimonials_post',
				'default_search_results',
				'default_index_index',
				'default_info_index',
				'default_redirector_redirect',
				'default_rss_index',
				'default_contact_index',
				'default_download_index',
				'default_blc_index'
		);
	}



	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		//d('routeShutdown 1');
		$recurso = $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();
		
		//d($this->getResponse());
		if($request->getModuleName() != 'admin' && $this->actionMethodExists($request) && Zend_Controller_Front::getInstance()->getDispatcher()->isDispatchable( $request ) && !in_array($recurso, $this->_whitelist) ){
			$this->_cachear = true;
		}
	}

	public function dispatchLoopShutdown()
	{
		//d($this->getResponse()->getHttpResponseCode());
		if($this->_cachear == true && !$this->getResponse()->isException() ) {
				$tagCache = Zend_Cache::factory('Core',
						'File',
						array('automatic_serialization' => true),
						array('cache_dir' => CACHE_PATH ));
				 
				$cache = Zend_Cache::factory('Capture',
						'Static',
						array(),
						array('index_filename' => 'index',
								'public_dir'     => CACHE_PUBLIC,
								'tag_cache'      => $tagCache));
		
				$id = bin2hex($_SERVER["REQUEST_URI"]);
		
				if(!$cache->load($id)){
					$cache->start($id, array());
			
				}else{
					echo $cache->load($id);
					exit();
				}
		}
	}
    protected function actionMethodExists( Zend_Controller_Request_Abstract $zfRequestObj )
        {

        //getControllerClass() does not return the module prefix
        $controllerClassSuffix = Zend_Controller_Front::getInstance()
            ->getDispatcher()
            ->getControllerClass( $zfRequestObj );


        $controllerClassName = Zend_Controller_Front::getInstance()
            ->getDispatcher()
            ->formatClassName( $zfRequestObj->getModuleName() , $controllerClassSuffix );


        //load the class before we call method_exists()
        Zend_Controller_Front::getInstance()
            ->getDispatcher()
            ->loadClass( $controllerClassSuffix );

        $actionMethod = Zend_Controller_Front::getInstance()
            ->getDispatcher()
            ->getActionMethod( $zfRequestObj );

        return ( method_exists( $controllerClassName, $actionMethod ) );
        }

}
