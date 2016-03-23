<?php
class Default_ContactController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';
	
	public function init()
	{
		/* Initialize action controller here */
		$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
	}
	
	public function indexAction(){

		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'staging');
		$this->view->assign('pubkey',$config->recaptcha->pubkey);
		
		$this->view->headTitle('Contact Us ');

		$ContactUs= new Table_ContactUsSettings();
        //$camposOCW = $ContactUs->getFields();
        $contact = $ContactUs->fetchAll('Id=1');
        $this->view->assign('contact', $contact);
				
		if($this->getRequest()->isPost() ) {
			
			// obtener de a uno por uno los campos
			$conFirstName 	= $this->getRequest()->getParam('conFirstName'	, 'Anonymous');
			$conLastName 	= $this->getRequest()->getParam('conLastName'	, 'Anonymous');
			$conEmail 		= $this->getRequest()->getParam('conEmail'		, 'Anonymous');
			$conCountry 	= $this->getRequest()->getParam('conCountry'	, 'Anonymous');
			//$conRole		= $this->getRequest()->getParam('conRole'		, null);
			///$conInquiriType	= $this->getRequest()->getParam('conInquiriType', null);
			$conComents		= $this->getRequest()->getParam('conComents'	, null);
						
			try {
				// d($this->getRequest()->getParams());
				$recaptcha = new Zend_Service_ReCaptcha($config->recaptcha->pubkey, $config->recaptcha->privkey );
				$result = $recaptcha->verify( 
						$this->getRequest()->getParam('recaptcha_challenge_field', null ),
						$this->getRequest()->getParam('recaptcha_response_field', null )
				);
				
				$validator = new Zend_Validate_EmailAddress();
				
				if(!$validator->isValid($conEmail)){
					throw new Exception("Invalid email");
				}
				if( !$result->isValid() ) {
					throw new Exception("Invalid captcha");
				}
				if( is_null($conComents) ) {
					throw new Exception("Comments can't be empty");
				}
				// guardar en la DB
				try {
					$Contact = new Table_Contact();
					$newComment		= $Contact->createRow(); 
					
					$newComment->conFirstName	= empty($conFirstName) ? 'Anonymous': $conFirstName;
					$newComment->conLastName	= empty($conLastName) ? 'Anonymous': $conLastName;
					$newComment->conEmail		= $conEmail;
					$newComment->conCountry		= $conCountry;
					$newComment->conRole		= $conRole;
					$newComment->conInquiriType	= $conInquiriType;
					$newComment->conComents		= $conComents;
					$newComment->save();
					
					$this->view->assign('success', 'Thanks for leaving your comments');
				} catch (Exception $e ){
					throw new Exception($e->getMessage() . ' CODE: '. $e->getCode());
				}
			} catch (Exception $e) {
				$this->view->assign('error', $e->getMessage());
				$this->view->conFirstName 	= $conFirstName;
				$this->view->conLastName 	= $conLastName;
				$this->view->conEmail 		= $conEmail;
				$this->view->conCountry		= $conCountry;
				$this->view->conRole		= $conRole;
				$this->view->conInquiriType	= $conInquiriType;
				$this->view->conComents 	= $conComents;
				
			}
			
		}
		//$this->view->capcha = $recaptcha->getHTML();
		
	}
	
	
}