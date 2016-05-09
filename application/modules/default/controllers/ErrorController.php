<?php

class Default_ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        if( $this->_request->isXmlHttpRequest() ){
                    $this->_helper->layout()->setLayout('empty');
                    $this->_helper->viewRenderer->setNoRender();
                    echo "<b>Error:</b> " .$errors->exception->getMessage();
                    
        }      
        if (!$errors || !$errors instanceof ArrayObject) {
        	$this->view->title = "Not error";
        	$this->view->headTitle("Not error");
            $this->view->message = 'It has asked the error page. Use your browsers Back button to navigate to the page you have prevously come from';
            $this->view->code = 0;
        } else {
	        switch ($errors->type) {
	            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
	            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
	            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
	                // 404 error -- controller or action not found
	                $this->getResponse()->setHttpResponseCode(404);
	                $priority = Zend_Log::NOTICE;
	                $this->view->title = "Page Not Found";
	                $this->view->headTitle("Page Not Found");
	                $this->view->message = 'The page you requested could not be found, either contact your webmaster or try again. Use your browsers Back button to navigate to the page you have prevously come from';
					$this->view->code = 404;
	                break;
	            default:
	                // application error
	                $this->getResponse()->setHttpResponseCode(500);
	                $priority = Zend_Log::CRIT;
	                $this->view->title = "Internal Error";
	                $this->view->headTitle("Internal Error");
	                $this->view->message = 'Internal application error, please contact the administrator to report the error. Use your browsers Back button to navigate to the page you have prevously come from';
					$this->view->code = 500;
	                break;
	        }
        
        $this->view->referer = $this->getRequest()->getServer('HTTP_REFERER');
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        $this->view->request   = $errors->request;
        }
    }

    public function getLog()
    {
        return false;
    }


}

