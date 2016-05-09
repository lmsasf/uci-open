<?php
class Default_ConferencesController extends Zend_Controller_Action
{
    private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education';

    public function init()
    {
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
    }

    public function indexAction()
    {
        $OCW = new Table_OCW();
        $select = $OCW->select()->from( "OCW", array('ocwTitle', 'ocwTitleEncode', 'id', 'thumbnail', 'ocwDescription') )
                                                        ->where("idType =?", 6) //conferences
                                                        ->where('ocwGolive=?', 1)
                                                        ->order('ocwTitle')
        ;

        $conferences = $OCW->fetchAll( $select );
        
        $ads = new Table_Ads();        
        $adsSelected = $ads->getAdsIndex(Table_Ads::OCW_TYPE_CONFERENCE);        
        $contador = count($adsSelected);      
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($adsSelected));
        }
                        
        $this->view->assign("conferences", $conferences);
        $this->view->headTitle("List of conferences");
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', 'List of collections');
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/conferences' );
        $this->view->headMeta()->setProperty('og:description', 'List of conferences :: UC Irvine, OpenCourseWare');	
        $this->view->headMeta()->setName('description', 'List of conferences :: UC Irvine, OpenCourseWare' );

        /*Update the HTML of conferences.html*/
        $OCW->removeCacheIndex(6);

    }
	
    public function viewAction(){
        if($this->getRequest()->getParam('id') ==='index'){
            $this->_forward('index');			
        }

        $OCWJoin = new Table_OCWJoin();
        $Conferenes = new Table_OCW();

        $ocwTitleEncode = $this->getRequest()->getParam('id');
        $golive = $this->getRequest()->getParam('golive', 1); // published by default
        
        $select = $Conferenes->select()->setIntegrityCheck(false)
                                        ->from( array('r0'=>'OCW'), array('ocwTitle', 'ocwTitleEncode', 'id', 'thumbnail', 'ocwDescription', 'ocwKeywords') )
                                        ->joinLeft(array('r1'=>'Conference'), "r1.idOCW = r0.id", array('confrequentlyQuest'))
                                        ->where("idType =?", 6)
                                        ->where('ocwGolive=?', 1)
                                        ->where('ocwTitleEncode = ?',$ocwTitleEncode )
                                        ->order('ocwTitle')
        ;

        $conference = $Conferenes->fetchRow( $select );
        
        $idPage = $conference['id'];
        
        $Category = new Table_Category();        
        $id_categories = $Category ->getCategoriesForAds($idPage);
        
        $ads = new Table_Ads();        
        $res = $ads->getAdsView(Table_Ads::OCW_TYPE_CONFERENCE, $idPage, $id_categories);
        $contador = count($res);        
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($res));
        }        
        
        //past testimonials
        $Testimonial = new Table_Testimonials();
        $where = $Testimonial->select()->where('tesVisible = ?', 1)->where('idOCW=?', $conference->id)->order('idTes DESC ');
        $pastTestimonials = $Testimonial->fetchAll($where);
        $this->view->pastTestimonials = $pastTestimonials;
        $descripcion = truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", strip_tags($conference->ocwDescription) ) , 150 );
        $joins = $OCWJoin->getJoins( $conference->id );
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS . $conference->ocwKeywords );
        $this->view->headMeta()->setName('description', $descripcion);
        $this->view->assign('ocwTypes' , $OCWJoin->getTypesJoins($ocwTitleEncode, 6));
        $this->view->assign('Conference'   , $conference  );
        $this->view->assign('Joins'    , $joins );		
        //-----
        // Facebook tags
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', $conference->ocwTitle);
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/conferences/'. $ocwTitleEncode .'.html' );
        $this->view->headMeta()->setProperty('og:description', $descripcion);
        if(!empty($course[0]['thumbnail'])){
            $this->view->headMeta()->setProperty('og:image', $conference->thumbnail);
        }
        $shortUrl = bitly_v3_shorten('http://'. $_SERVER['SERVER_NAME'].'/conferences/'. $ocwTitleEncode .'.html','j.mp');
        if(isset($shortUrl['status_code'] ) && $shortUrl['status_code'] == 200 ){
            $shortUrl = $shortUrl['url'];
        } else {
            $shortUrl = 'http://'. $_SERVER['SERVER_NAME'].'/conferences/'. $ocwTitleEncode .'.html';
        }
        $this->view->assign('shortUrl', $shortUrl);
        $this->view->headTitle($conference->ocwTitle);
        $this->view->title = $conference->ocwTitle; // useful for the breadcrums
    }

}