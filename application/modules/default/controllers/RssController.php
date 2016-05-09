<?php
class Default_RssController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->setLayout('empty');
	}
	
	public function indexAction(){
		$ocwType = $this->getRequest()->getParam('strType', 'all');
		$ocw = new Table_OCW();
		$category = new Table_Category();
		$joins	= new Table_OCWJoin();
		
		$feedData = array(
			'title'=>'UC Irvine, OpenCourseWare',
            'description'=>'Open Courseware from the University of California, Irvine',
     		'language' => 'en-us',
            'link'=>'http://'. $_SERVER['SERVER_NAME'],
     		'copyright'=> 'Copyright 2014, UC Irvine Extension',
            'charset'=>'utf-8',
            'entries'=>array()
        );
		// build the arrangement ITEM
		$entries = $ocw->getOcwRss( $ocwType );

		foreach ($entries as $entrie ){
			$cat = $category->getPath($entrie->idCat);
			if(!empty($entrie->ocwBypassUrlCourse) || !empty($entrie->ocwBypassUrlLecture)){
				$ocwLink = empty($entrie->ocwBypassUrlLecture)? $entrie->ocwBypassUrlCourse: $entrie->ocwBypassUrlLecture;
			} else {
				$ocwLink = 'http://' . $_SERVER['SERVER_NAME'] . '/' . strtolower( $entrie->typName ) . 's/' . $entrie->ocwTitleEncode .'.html';
			}
			if(!Zend_Uri::check($ocwLink)){
				$ocwLink = '';
			}
			$related = $joins->getJoins( $entrie->id );
			
			if($ocwType =='full'){
				$descriptionBody = ss($entrie->ocwDescription);
			} else {
				$descriptionBody = $this->view->partial('rss/body.phtml', 
										array(
												  'description'	=> $entrie->ocwDescription
												, 'category'	=> $cat[0]['path'] 
												, 'author'		=> $entrie->author
												, 'tags' 		=> $entrie->ocwKeywords
												, 'link'		=> $ocwLink
												, 'license'		=> $entrie->ocwLicense
												, 'thumbnail'	=> $entrie->thumbnail
												, 'joins'		=> $related
											 )
										);
			}
			$item = array(
							'title'			=> $entrie->ocwTitle 
							,'description'	=> $descriptionBody
							,'author' 		=> $entrie->author
							,'link'			=> $ocwLink
							,'guid'			=> $entrie->id
			);
			array_push( $feedData['entries'], $item );
		}
        // create our feed object and import the data
        $feed = @Zend_Feed::importArray ( $feedData, 'rss' );
        // set the Content Type of the document
        header ( 'Content-type: text/rss' );
        // echo the contents of the RSS xml document
        echo $feed->send();
		
	}
}