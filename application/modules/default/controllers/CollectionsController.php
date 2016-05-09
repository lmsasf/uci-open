<?php
class Default_CollectionsController extends Zend_Controller_Action
{
    private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education';

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
    }

    public function indexAction()
    {
        $OCW = new Table_OCW();
        $OCW->removeCacheIndex(4);
        $select = $OCW->select()->from( "OCW", array('ocwTitle', 'ocwTitleEncode', 'id', 'thumbnail', 'ocwDescription') )
            ->where("idType =?", 4)
            ->where('ocwGolive=?', 1)
            ->order('ocwTitle')
        ;

        $collections = $OCW->fetchAll( $select );

        $ads = new Table_Ads();
        $adsSelected = $ads->getAdsIndex(Table_Ads::OCW_TYPE_COLLECTION);
        $contador = count($adsSelected);
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($adsSelected));
        }

        $Section = new Table_SectionTexts();
        $this->view->assign('section', $Section->fetchRow($Section->select()->where('section = 4 AND secActive = 1')));

        $this->view->assign("collections", $collections);

        $this->view->headTitle("List of collections");
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', 'List of collections');
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/collections' );
        $this->view->headMeta()->setProperty('og:description', 'List of collections :: UC Irvine, OpenCourseWare');
        $this->view->headMeta()->setName('description', 'List of collections :: UC Irvine, OpenCourseWare' );
    }

    public function viewAction(){
        $admin = $this->getRequest()->getParam('adm');
		$preview = $this->getRequest()->getParam('prv');
		
        if($this->getRequest()->getParam('id') ==='index'){
            $this->_forward('index');
        }

        $OCWJoin = new Table_OCWJoin();
        $Collections = new Table_OCW();

        $ocwTitleEncode = $this->getRequest()->getParam('id');
        $golive = $this->getRequest()->getParam('golive', 1); // show publish by default

        $select = $Collections->select()->setIntegrityCheck(false)
            ->from( array('r0'=>'OCW'), array('ocwTitle', 'ocwTitleEncode', 'id', 'thumbnail', 'ocwDescription', 'ocwKeywords','ocwTemplate','ocwDraft','promoloc') )
            ->joinLeft(array('r1'=>'Collection'), "r1.idOCW = r0.id", array('colfrequentlyQuest'))
            ->where("idType =?", 4)
            ->where('ocwTitleEncode = ?',$ocwTitleEncode )
            ->order('ocwTitle')
        ;
        if($admin != 'y') $select ->where('ocwGolive=?', 1);
        
        $collection = $Collections->fetchRow( $select );

        $shortUrl = bitly_v3_shorten('http://'. $_SERVER['SERVER_NAME'].'/collections/'. $ocwTitleEncode .'.html','j.mp');
        if(isset($shortUrl['status_code'] ) && $shortUrl['status_code'] == 200 ){
            $shortUrl = $shortUrl['url'];
        } else {
            $shortUrl = 'http://'. $_SERVER['SERVER_NAME'].'/collections/'. $ocwTitleEncode .'.html';
        }

        $idPage = $collection['id'];

        $Category = new Table_Category();
        $id_categories = $Category ->getCategoriesForAds($idPage);

        $ads = new Table_Ads();
        $res = $ads->getAdsView(Table_Ads::OCW_TYPE_COLLECTION, $idPage, $id_categories);
        $contador = count($res);
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($res));
        }

        $this->view->assign('shortUrl', $shortUrl);
        $descripcion = truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", strip_tags($collection->ocwDescription) ) , 150 );
        $joins = $OCWJoin->getJoins( $collection->id );
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS . $collection->ocwKeywords );
        $this->view->headMeta()->setName('description', $descripcion );
        $this->view->assign('ocwTypes' , $OCWJoin->getTypesJoins($ocwTitleEncode, 4));
        $this->view->assign('Collection'   , $collection  );
		
		if($preview=='y'){
			$this->view->assign('aContent' , $collection->ocwDraft );
			$this->view->assign('Mode' , "preview" );
		}
		else{
			$this->view->assign('aContent' , $collection->ocwTemplate );
			$this->view->assign('Mode' , "normal" );
		}
        $this->view->assign('idPage', $idPage);		
		
        $this->view->assign('Joins'    , $joins );
        // facebook Tags
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', $collection->ocwTitle);
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/collections/'. $ocwTitleEncode .'.html' );
        $this->view->headMeta()->setProperty('og:description', $descripcion);
        if(!empty($course[0]['thumbnail'])){
            $this->view->headMeta()->setProperty('og:image', $collection->thumbnail);
        }
        $this->view->headTitle($collection->ocwTitle);
        $this->view->title = $collection->ocwTitle; // useful for the breadcrums
		$this->view->assign('prmLoc' , $collection->promoloc );
		
        $tmplid = Zend_Json::decode($collection->ocwTemplate);

        switch ($tmplid['T']) {
            case '1': $tmplview = 'template1'; break;
            case '2': $tmplview = 'template2'; break;
            case '3': $tmplview = 'template3'; break;
            case '4': $tmplview = 'template4'; break;
            case '5': $tmplview = 'template5'; break;
            case '6': $tmplview = 'template6'; break;
            default:
                $tmplview = 'template1'; break;
        }

        $runCommand = 'rm -rf ' . CACHE_PUBLIC . '/collections/view';
        exec( $runCommand, $output );

        $this->_helper->viewRenderer->setRender($tmplview);
    }

}