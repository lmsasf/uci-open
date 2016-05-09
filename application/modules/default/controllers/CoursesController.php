<?php
class Default_CoursesController extends Zend_Controller_Action
{

    private $_KEYWORDS = 'University, California, Irvine, OpenCourseWare, research, education, courses';

    public function init() {
        /* Initialize action controller here */
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS);

        /* Update the HTML of courses.html */
        $OCW = new Table_OCW();
        $OCW->removeCacheIndex(1);
    }

    public function indexAction(){
        $Category = new Table_Category();
        $Course = new Table_Course();
        $OCWJoin = new Table_OCWJoin();

        // get all courses
        $allCourses = $Course->getCourses(null, null, 1)->toArray();
        $categoryCourses = array();
        $relatedCourses = array();
        $authors = array();
        foreach ($allCourses as $curso) {
            $categoryCourses[$curso['idCat']][$curso['idCourse']] = $curso;
            $authors[$curso['idCourse']][$curso['idPer']] = $curso;
            $relatedCourses[$curso['idCourse']] = $OCWJoin->getJoins($curso['id']);
        }

        $ads = new Table_Ads();
        $adsSelected = $ads->getAdsIndex(Table_Ads::OCW_TYPE_COURSE);
        $contador = count($adsSelected);
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($adsSelected));
        }

        $this->view->headTitle('List of courses');
        $this->view->headMeta()->setName('description', 'List of courses University of California, Irvine');
        $this->view->assign('tree', $Category->treeAsArray(1));             //Categories from left panel
        $this->view->assign('courses', $categoryCourses);
        $this->view->assign('relatedCourses', $relatedCourses);
        $this->view->assign('authors',$authors);

        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', 'List of courses');
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/courses' );
        $this->view->headMeta()->setProperty('og:description', 'List of courses :: UC Irvine, OpenCourseWare');
        $this->view->headMeta()->setName('description', 'List of courses :: UC Irvine, OpenCourseWare' );
    }

    public function viewAction(){
        $admin = $this->getRequest()->getParam('adm');
        $preview = $this->getRequest()->getParam('prv');
        
        /*Updates the HTML of courses.html*/
        $OCW = new Table_OCW();
        $OCW->removeCacheIndex(1);

        if($this->getRequest()->getParam('id') ==='index'){
            $this->_forward('index');
        }
        $OCWJoin = new Table_OCWJoin();
        $Course = new Table_Course();

        $ocwTitleEncode = $this->getRequest()->getParam('id');
        $golive = $this->getRequest()->getParam('golive', 1); // published by default
        $course = $Course->getCourses(null, $ocwTitleEncode, $golive, $admin)->toArray();

        if( empty($course) ) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $authors = array();
        foreach ( $course as $curso ){
            if(!empty($curso['idPer']) && !is_null($curso['idPer'])){
                $authors[$curso['idPer']] = $curso;
            }
        }

        $joins  =   $OCWJoin->getJoins($course[0]['id']);
        $headers    =   $OCWJoin->getJoins($course[0]['id']);

        //past testimonials
        $Testimonial    =  new Table_Testimonials();
        $where = $Testimonial->select()->where('tesVisible = ?', 1)->where('idOCW=?', $course[0]['id'])->order('idTes DESC ');
        $pastTestimonials = $Testimonial->fetchAll($where);

        $BooksOCW = new Table_BooksOCW();

        $select = $BooksOCW->select()->from('BooksOCW',array('idBooks'))->where('idOCW = ?', $course[0]['id']);

        $booksocw = $BooksOCW->fetchAll($select);

        if(count($booksocw) > 0){
            foreach($booksocw as $valor) {
                $alert[] = $valor['idBooks'];
            }

            $Books = new Table_Books();

            $selectBooks = $Books->select()->where('idBooks IN (?)', $alert);

            $otherbooks = $Books->select()->where('idBooks IN (?)', $alert)
                ->where('bookPrincipal = 0');

            $other = $Books->fetchAll($otherbooks);
            $books = $Books->fetchRow($selectBooks);
            $count_other = count($other);

            $this->view->assign('books', $books);
            $this->view->assign('other', $other);
            $this->view->assign('count_other', $count_other);

        }

        $idPage = $course[0]['id'];

        $Category = new Table_Category();
        $id_categories = $Category ->getCategoriesForAds($idPage);

        $ads = new Table_Ads();
        $res = $ads->getAdsView(Table_Ads::OCW_TYPE_COURSE, $idPage, $id_categories);
        $contador = count($res);
        if(empty($contador) == false){
            $this->view->assign('count_ads', $contador);
            $this->view->assign('ads', json_encode($res));
        }

        $this->view->pastTestimonials = $pastTestimonials;
        $descripcion = truncateString( preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", strip_tags($course[0]['ocwDescription']) ) , 150 );
        $this->view->headTitle($course[0]['ocwTitle']);

        // Facebook tags
        $this->view->doctype('XHTML1_RDFA'); // controller
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:title', $course[0]['ocwTitle']);
        $this->view->headMeta()->setProperty('og:url', 'http://'. $_SERVER['SERVER_NAME'].'/courses/'. $ocwTitleEncode .'.html' );
        $this->view->headMeta()->setProperty('og:description', $descripcion);

        if(!empty($course[0]['thumbnail'])){
            $this->view->headMeta()->setProperty('og:image', $course[0]['thumbnail']);
        }

        $shortUrl = bitly_v3_shorten('http://'. $_SERVER['SERVER_NAME'].'/courses/'. $ocwTitleEncode .'.html','j.mp');

        if(isset($shortUrl['status_code'] ) && $shortUrl['status_code'] == 200 ){
            $shortUrl = $shortUrl['url'];
        } else {
            $shortUrl = 'http://'. $_SERVER['SERVER_NAME'].'/courses/'. $ocwTitleEncode .'.html';
        }

        $this->view->assign('shortUrl', $shortUrl);
        $this->view->title = $course[0]['ocwTitle'] ; // useful for the breadcrums
        $this->view->headMeta()->setName('keywords', $this->_KEYWORDS . $course[0]['ocwKeywords'] );
        $this->view->headMeta()->setName('description', $descripcion ) ;
        $this->view->assign('ocwTypes' , $OCWJoin->getTypesJoins($ocwTitleEncode, 1));
        $course[0]['ocwLicense'] = trim($course[0]['ocwLicense'])=="" ? null : $course[0]['ocwLicense'];
        $this->view->assign('Course'   , $course  );
		
		if($preview=='y'){
			$this->view->assign('aContent' , $course[0]['ocwDraft'] );
			$this->view->assign('Mode' , "preview" );
		}
		else{
			$this->view->assign('aContent' , $course[0]['ocwTemplate'] );
			$this->view->assign('Mode' , "normal" );
		}
        $this->view->assign('idPage', $idPage);
		
		$this->view->assign('Authors'  , $authors );
        $this->view->assign('Joins'    , $joins );
        $this->view->assign('Headers'    , $headers );
        $this->view->assign('downloadSize', $this->getDownloadSize($course[0]['typName'], $ocwTitleEncode) );
		$this->view->assign('prmLoc' , $course[0]['promoloc'] );
        
		$tmplid = Zend_Json::decode($course[0]['ocwTemplate']);

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

        $runCommand = 'rm -rf ' . CACHE_PUBLIC . '/courses/view';
        exec( $runCommand, $output );

        $this->_helper->viewRenderer->setRender($tmplview);

    }

    /**
     * Return the file size if exists
     * @param String $typName
     * @param String $ocwTitleEncode
     * @return mixed string | boolean
     */
    private function getDownloadSize($typName, $ocwTitleEncode){
        // chances are there is the package as ZIP, verify that first
        $root = $_SERVER['DOCUMENT_ROOT'];
        $packageName= strtolower($typName) . '_' . $ocwTitleEncode;
        $zipFile = $root.'/packages/'.$packageName.".zip";
        $imsccFile = $root.'/packages/'.$packageName.".imscc";
        // verify zip
        if(file_exists($zipFile)){
            return $this->human_filesize(filesize($zipFile), 2);
        } elseif (file_exists($imsccFile)){
            return $this->human_filesize(filesize($imsccFile), 2);
        } else {
            return false;
        }

    }

    private function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $sz = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' .@$sz[$factor];
    }

}