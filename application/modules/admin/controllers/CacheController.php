<?php
class Admin_CacheController extends Zend_Controller_Action
{
	/**
	 * Init
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->layout()->setLayout('admin');
	}
	/**
	 * Listado de Categorias
	 */
	public function indexAction()
	{
		$this->view->headTitle('Cache :: Tree');
		///////////////////////////////////////////
		//d( $this->dirToArray(CACHE_PUBLIC) );
		$this->view->cached = $this->dirToArray(CACHE_PUBLIC);

		
	}
	public function cleanAction()
	{
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		try {
			$output = array();
			$runCommand = "rm -Rf ". CACHE_PUBLIC .'/*';
			exec( $runCommand, $output );
			$this->view->success = "Cache successfully cleaned";
		} catch (Exception $e) {
			$this->view->error = "An error occurred while clearing the cache";
		}
		$this->_forward('index','cache','admin');
	}
	
	public function removeAction()
	{
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$data = $this->getRequest()->getParam('data', null);
		try {
			if(is_null($data)){
				throw new Exception('Data empty');
			}
			$file = base64_decode($data);
			$output = array();
			$runCommand = "rm -f ". $file;
			exec( $runCommand, $output );
			$this->view->success = "Cache successfully cleaned";
		} catch (Exception $e) {
			$this->view->error = "An error occurred while clearing the cache . ". $e->getMessage();
		}
		$this->_forward('index','cache','admin');
	}

	public function refreshAction()
	{
		$this->_helper->layout()->setLayout('empty');
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();
		$idOCW = $this->getRequest()->getParam('Id', null);
		$cron = $this->getRequest()->getParam('cron', null);
                
                $redirect = true;
                
		try {
			if(is_null($idOCW)){
                            $output_all = array();
                            $runCommand = 'find ' . CACHE_PUBLIC . ' -type f -exec rm -fv {} \;';
                            exec( $runCommand, $output_all );
                            if($cron){
                                $redirect = false;
                            }else{
                                if( empty( $output_all ) ) {
                                    $this->view->info = "OCW was not in cache.";
                                }else{
                                    $this->view->success = "The cache has been cleaned.";
                                }
                            }
                            
			}else{
                            $OCW = new Table_OCW();
                            $ocw = $OCW->fetchRow("id=$idOCW");
                            $file = $ocw->ocwTitleEncode . '.html';
                            $output = array();
                            $runCommand = 'find ' . CACHE_PUBLIC . ' -type f -name "'.$file.'*" -exec rm -fv {} \;';
                            exec( $runCommand, $output );
                            
                            if( empty( $output ) ) {
 				$this->view->info = "OCW was not in cache.";
                            }else{
                                    $this->view->success = "The cache for <b>". $ocw->ocwTitle ."</b> has been cleaned.";
                            }
                            
                        }
   
		} catch (Exception $e) {
			$this->view->error = "An error occurred while clearing the cache . ". $e->getMessage();
			echo $e->getMessage();
		}
                
                if($redirect){
                    $this->_forward('index','ocw','admin');
                }
                
	}
	/**
	 * Devuelve el arbol de directorios en un array
	 * 
	 * @param String $dir
	 * @return array
	 */		
	private function dirToArray($dir) {
	   $result = array();
	   if($dir){
		   $cdir = scandir($dir);
		   foreach ($cdir as $key => $value)
		   {
		      if (!in_array($value,array(".","..")))
		      {
		         if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
		         {
		            $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
		         }
		         else
		         {
		            $result[] = array('path'=>$dir.DIRECTORY_SEPARATOR.$value, 'name'=>$value);
		         }
		      }
		   }
	   }
	   return $result;
	} 
	
}