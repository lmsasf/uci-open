<?php
class Default_TestimonialsController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';
	
	public function init()
	{
		/* Initialize action controller here */
		$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
	}
	
	public function postAction(){
		$testimonialOptionsTable = new Table_TestimonialOptions();
		$select1 = $testimonialOptionsTable->select()->order('sequence');
		$select2 = $testimonialOptionsTable->select()->order('sequence');
		
		$grupo_1 = $testimonialOptionsTable->fetchAll($select1->where('groupId=?', 1));
		$grupo_2 = $testimonialOptionsTable->fetchAll($select2->where('groupId=?', 2));
		$this->view->group1 = $grupo_1;
		$this->view->group2 = $grupo_2;
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'staging');
		$this->view->assign('pubkey',$config->recaptcha->pubkey);
		
		// OCW related
		$ocwTitleEncode = $this->getRequest()->getParam('ocwTitleEncode');
		$OCW = new Table_OCW();
		$this->view->assign('OCW', $OCW->fetchRow($OCW->select()->where('ocwTitleEncode = ?', $ocwTitleEncode)));
		
		$this->view->headTitle('Provide a testimonial for "' .  $this->view->OCW->ocwTitle . '"');
		
		//past testimonials
		$Testimonial = new Table_Testimonials();
		$where = $Testimonial->select()->where('tesVisible = ?', 1)->where('idOCW=?', $this->view->OCW->id )->order('idTes DESC ');
		$pastTestimonials = $Testimonial->fetchAll($where);
		
		$this->view->pastTestimonials = $pastTestimonials;
		
		if($this->getRequest()->isPost() && !is_null( $this->getRequest()->getParam('idOCW', null) ) ) {
			
			// get the fields one by one
			$tesName 		= $this->getRequest()->getParam('tesName'		, 'Anonymous');
			$tesCountry 	= $this->getRequest()->getParam('tesCountry'	, 'Anonymous');
			$tesEmail 		= $this->getRequest()->getParam('tesEmail'		, 'Anonymous');
			$tesQuestion1	= $this->getRequest()->getParam('tesQuestion1'	, null);
			$tesQuestion2	= $this->getRequest()->getParam('tesQuestion2'	, null);
			$tesQuestion3	= $this->getRequest()->getParam('tesQuestion3'	, null);
			$tesTestimonial	= $this->getRequest()->getParam('tesTestimonial', null);
			$tesMarketing	= $this->getRequest()->getParam('tesMarketing'	, 0);
			$tesContact		= $this->getRequest()->getParam('tesContact'	, 0);
			$idOCW			= $this->getRequest()->getParam('idOCW'			, null);
						
			try {
				$recaptcha = new Zend_Service_ReCaptcha($config->recaptcha->pubkey, $config->recaptcha->privkey );
				$result = $recaptcha->verify( 
						$this->getRequest()->getParam('recaptcha_challenge_field', null ),
						$this->getRequest()->getParam('recaptcha_response_field', null )
				);
				if( !$result->isValid() ) {
					throw new Exception("Invalid captcha");
				}
				if( is_null($tesTestimonial) ) {
					throw new Exception("Testimonial can't be empty");
				}
				// save in the DB
				try {
					
					$newTestimonial = $Testimonial->createRow();
					
					$newTestimonial->tesName 		= empty($tesName) ? 'Anonymous': $tesName ;
					$newTestimonial->tesCountry 	= $tesCountry;
					$newTestimonial->tesEmail		= $tesEmail;
					$newTestimonial->tesQuestion1	= $tesQuestion1;
					$newTestimonial->tesQuestion2	= $tesQuestion2;
					$newTestimonial->tesQuestion3	= $tesQuestion3;
					$newTestimonial->tesMarketing	= $tesMarketing;
					$newTestimonial->tesContact		= $tesContact;
					$newTestimonial->tesTestimonial	= $tesTestimonial;
					$newTestimonial->idOCW			= $idOCW;
					$newTestimonial->save();
					
					$this->view->assign('success', 'Thanks for leaving your comments');
				} catch (Exception $e ){
					throw new Exception($e->getMessage() . ' CODE: '. $e->getCode());
				}
			} catch (Exception $e) {
				$this->view->assign('error', $e->getMessage());
				$this->view->tesName 		= $tesName;
				$this->view->tesCountry 	= $tesCountry;
				$this->view->tesEmail 		= $tesEmail;
				$this->view->tesQuestion1	= $tesQuestion1;
				$this->view->tesQuestion2	= $tesQuestion2;
				$this->view->tesQuestion3	= $tesQuestion3;
				$this->view->tesTestimonial = $tesTestimonial;
				$this->view->tesMarketing	= $tesMarketing;
				$this->view->tesContact		= $tesContact;
				$this->view->idOCW			= $idOCW;
			}
		}
	}
	
	
}