<?php
class Default_LecturesController extends Zend_Controller_Action
{
	private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses'; 
	
	public function init()
	{
		$this->view->headMeta()->setName('keywords', $this->_KEYWORDS);
	}

    public function indexAction() {
        $Category = new Table_Category();
        $Lecture = new Table_Lecture();

        $OCWJoin = new Table_OCWJoin();
        // obtengo todos los cursos
        $allLectures = $Lecture->getLecture(null,null,1)->toArray();
        $categoryLecture = array();
        $relatedLectures = array();
        $authors = array();
        foreach ( $allLectures as $lecture ){
            $categoryLecture[$lecture['idCat']][$lecture['idLecture']]=$lecture;
            $authors[$lecture['idLecture']][$lecture['idPer']] = $lecture;
            $relatedLectures[$lecture['idLecture']] = $OCWJoin->getJoins($lecture['id'] );
        }

        $ads = new Table_Ads();
        $adsSelected = $ads->getAdsIndex(Table_Ads::OCW_TYPE_LECTURE);
        $contador = count($adsSelected);
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($adsSelected));
        }

        $this->view->assign('tree', $Category->treeAsArray(3));
        $this->view->assign('lectures', $categoryLecture);
        $this->view->assign('relatedLectures', $relatedLectures);
        $this->view->assign('authors',$authors);

        $this->view->headTitle("List of lectures");
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', 'List of lectures');
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/lectures' );
        $this->view->headMeta()->setProperty('og:description', 'List of lectures :: UC Irvine, OpenCourseWare');
        $this->view->headMeta()->setName('description', 'List of lectures :: UC Irvine, OpenCourseWare' );

        $OCW = new Table_OCW();
        $OCW->removeCacheIndex(3);
    }

    public function viewAction(){
        $admin = $this->getRequest()->getParam('adm');
		$preview = $this->getRequest()->getParam('prv');
		
        if($this->getRequest()->getParam('id') ==='index'){
            $this->_forward('index');
        }
        $OCWJoin = new Table_OCWJoin();
        $Course = new Table_Course();

        $Lecture = new Table_Lecture();

        $ocwTitleEncode = $this->getRequest()->getParam('id');
        $golive = $this->getRequest()->getParam('golive', 1); // por defecto muestra el curso publicado

        $course = $Course->getCourses(null, $ocwTitleEncode, $golive, $admin)->toArray();

        $lecture = $Lecture->getLecture(null, $ocwTitleEncode, $golive, $admin)->toArray();
        $authors = array();
        foreach ( $lecture as $lectura ){
            $authors[$lectura['idPer']] = $lectura;
        }

        //past testimonials
        $Testimonial = new Table_Testimonials();
        $where = $Testimonial->select()->where('tesVisible = ?', 1)->where('idOCW=?', $lecture[0]['id'])->order('idTes DESC ');
        $pastTestimonials = $Testimonial->fetchAll($where);
        $this->view->pastTestimonials = $pastTestimonials;

        // d($course); exit();
        $joins = $OCWJoin->getJoins($lecture[0]['id']);
        $files = $OCWJoin->getJoins($lecture[0]['id'], 2);
        //d($joins);
        // Tags para facebook

        $idPage = $lecture[0]['id'];

        $Category = new Table_Category();
        $id_categories = $Category ->getCategoriesForAds($idPage);

        $ads = new Table_Ads();
        $res = $ads->getAdsView(Table_Ads::OCW_TYPE_LECTURE, $idPage, $id_categories);
        $contador = count($res);
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($res));
        }

        $descripcion = truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", strip_tags($lecture[0]['ocwDescription']) ) , 150 );
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', $lecture[0]['ocwTitle']);
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/lectures/'. $ocwTitleEncode .'.html' );
        $this->view->headMeta()->setProperty('og:description', $descripcion);
        if(!empty($course[0]['thumbnail'])){
            $this->view->headMeta()->setProperty('og:image', $lecture[0]['thumbnail']);
        }

        $this->view->headTitle($lecture[0]['ocwTitle']);
        $this->view->title = $lecture[0]['ocwTitle'] ; // sirve para el breadcrums
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS . $lecture[0]['ocwKeywords'] );
        $this->view->headMeta()->setName('description', strip_tags( $descripcion ) );
        $this->view->assign('ocwTypes' 		, $OCWJoin->getTypesJoins($ocwTitleEncode, 3));
        $lecture[0]['ocwLicense'] = trim($lecture[0]['ocwLicense'])=="" ? null : $lecture[0]['ocwLicense'];
        $this->view->assign('Lecture'   	, $lecture );
		
		if($preview=='y'){
			$this->view->assign('aContent' , $lecture[0]['ocwDraft'] );
			$this->view->assign('Mode' , "preview" );
		}
		else{
			$this->view->assign('aContent' , $lecture[0]['ocwTemplate'] );
			$this->view->assign('Mode' , "normal" );
		}
        $this->view->assign('idPage', $idPage);		
		
        $this->view->assign('Authors'  		, $authors );
        $this->view->assign('Joins'    		, $joins );
        $this->view->assign('Files'    		, $files );
        $this->view->assign('ocwTitleEncode', $ocwTitleEncode );

        $shortUrl = bitly_v3_shorten('http://'. $_SERVER['SERVER_NAME'].'/lectures/'. $ocwTitleEncode .'.html','j.mp');
        if(isset($shortUrl['status_code'] ) && $shortUrl['status_code'] == 200 ){
            $shortUrl = $shortUrl['url'];
        } else {
            $shortUrl = 'http://'. $_SERVER['SERVER_NAME'].'/lectures/'. $ocwTitleEncode .'.html';
        }
        $this->view->assign('shortUrl', $shortUrl);
		$this->view->assign('prmLoc' , $lecture[0]['promoloc'] );
		
        $tmplid = Zend_Json::decode($lecture[0]['ocwTemplate']);

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

        $runCommand = 'rm -rf ' . CACHE_PUBLIC . '/lectures/view';
        exec( $runCommand, $output );

        $this->_helper->viewRenderer->setRender($tmplview);        
        
    }

}