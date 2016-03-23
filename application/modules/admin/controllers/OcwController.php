<?php 
/**
 *
 * @author damills
 *
 */
 
class Admin_OcwController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('admin');
    }
	/**
	 * Listado de personas
	 */
    public function indexAction()
    {
    	$this->view->headTitle('OCW :: List');
    	$us = new Table_User();
    	$usuario = $us::getIdentity();
    	$this->view->assign('usuario',$usuario);
    }

	/**
	 * Pantalla principal de joins
	 */

    public function joinsAction(){
		try {
			$Id = $this->getRequest()->getParam('id', null);

			$OCW= new Table_OCW();
			$OCWJoin= new Table_OCWJoin();
			// el propio OCW mas sus joins
			$this->view->assign('Id', $Id);
			$this->view->assign('OCW', $OCW->getOCWinfo($Id));
			$this->view->assign('joins', $OCWJoin->getJoins($Id));
			// los filtros
			$filtersList = array();
			$Author = new Table_Author();
			$filtersList['Author'] = $Author->getOCWAuthorFilter();
			$Category = new Table_Category();
			$fullCategories = $Category->getTreeWithPaths();
			foreach( $fullCategories as $cat ){
				$filtersList['Category'][] = array('filterGroup'=>'Category', 'idFilter'=>$cat['id'], 'filterName'=>$cat['path'] );
			}
			$Language = new Table_Language();
			$filtersList['Language'] = $Language->getOCWLanguagesFilter();

			$Univarsity = new Table_University();
			$filtersList['University'] = $Univarsity->getOCWUniversityFilter();
			$this->view->assign('filtersList', $filtersList);
		} catch (Exception $e) {
			$this->view->assign( 'error', $e->getMessage() );
			$this->_forward('index', 'ocw', 'admin');
		}
    }
    /**
     * Borra un OCW
     * @throws Exception
     */
	public function savepublishAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$Ocw = new Table_OCW();

    	try {
    		$Id = $this->getRequest()->getParam('Id', null);
    		if( is_null($Id) ){
    			throw new Exception( 'Insufficient parameters' );
    		}
    		$row = $Ocw->fetchRow( 'id='.$Id );
    		$row->ocwGolive = $this->getRequest()->getParam('value', 0);
    		$Ocw->removeFromCache($Id);
    		$Ocw->removeCacheIndex($row->idType);
    		$Id = $row->save();
    		if($Id){
    			echo Zend_Json_Encoder::encode(array('Id'=> $Id));
    		} else {
    			throw new Exception('Failed to update the state, this is an error associated with the database, please refresh the page and try again');
    		}
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }

    public function deleteAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$OCW = new Table_OCW();

    	$tr = $OCW->getAdapter()->beginTransaction();
    	try {
    		$child	 		= $this->getRequest()->getParam('Id' , null);
    		if( !is_null($child)){
    			// borro el cache si hay
    			$OCW->removeFromCache($child);
    			// borro el ocw
    			$OCW->delete( "id  = $child " );
    			$tr->commit();
    		} else {
    			throw new Exception("Insufficient parameters");
    		}
            $this->_redirect('/admin/ocw');
    
    	} catch (Exception $e){
    		$tr->rollBack();
    		echo( $e->getMessage() );
    	}
    }
    /**
     * Obtiene los OCW disponibles para asociar en la ventana de JOINS
     */
    public function getocwjoinAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$OCW 	 = new Table_OCW();
    	$OCWJoin = new Table_OCWJoin();
    	try {
    		$joins = $this->getRequest()->getParam('joins', null);
    		$Id = $this->getRequest()->getParam('id', 0);
    		$exclude = array();
    		if(!is_null($joins)){
    			foreach ( $joins as $join ){
    				$exclude[] = $join['idChild'];
    			}
    		}
    		$exclude[]= $Id;
    		// texto
    		$text = $this->getRequest()->getParam('text', '');
    		$text =  empty($text) ? '' : '%' . str_replace(' ', '%', $text) . '%';
    		// combo
    		$combo = $this->getRequest()->getParam('combo', array());
    		$filters = array();
    		foreach ($combo as  $item ){
    			$parts = explode('_', $item );
    			$filters[$parts[0]][] = $parts[1];
    		}
    		$type = $this->getRequest()->getParam('type', array());
    		//armo todo para enviar al modelo
    		$arrayType			= $type;
    		$arrayExclude 		= $exclude;
    		$arrayUniversity 	= array_key_exists('University', $filters) ? $filters['University']: array() ;
    		$arrayCategory 		= array_key_exists('Category'  , $filters) ? $filters['Category']  : array() ;
    		$arrayAuthor		= array_key_exists('Author'    , $filters) ? $filters['Author']    : array() ;
    		$arrayLanguage		= array_key_exists('Language'  , $filters) ? $filters['Language']  : array() ;
    		$keywords			= strtolower($text);
    		$resultado = $OCWJoin->filterOCW($arrayType,$arrayExclude, $arrayUniversity, $arrayCategory,$arrayAuthor,$arrayLanguage, $keywords);
    		echo Zend_Json_Encoder::encode($resultado);
    	} catch (Exception $e){

    	}
    }

    /**
     * Guarda por ajax las asociaciones
     * @throws Exception
     */
    public function saveocwjoinAction(){

    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	$OCWJoin = new Table_OCWJoin();
    	$OCW = new Table_OCW();

    	// Obtener parámetros
    	$tr = $OCWJoin->getAdapter()->beginTransaction();
    	try {
    		$data	 	= $this->getRequest()->getParam('data' , null);
    		$Id	 		= $this->getRequest()->getParam('id' , null);
    		if( !is_null($Id) ){ // editar o añadir
    			$OCWJoin->delete( "idOCWParent  = $Id" );
    			$OCWJoinRow = null;
    			if( !is_null($data) ){
	    			foreach($data as $dato){
	    				$OCWJoinRow = $OCWJoin->createRow();
	    				$OCWJoinRow->idOCWChild  = $dato['idChild'];
	    				$OCWJoinRow->joiSequence = $dato['sequence'];
	    				$OCWJoinRow->idOCWParent = $Id;
	    				$idJoin = $OCWJoinRow->save();
	    			}
	    			echo Zend_Json_Encoder::encode(array('id'=> $idJoin));
    			} else {
    				throw new Exception("No data to save");
    			}

    			$tr->commit();
    			// borro del cache si es necesario
    			$OCW->removeFromCache($Id);
    		} else {
    			throw new Exception("Insufficient parameters");
    		}

    	} catch (Exception $e){
    		$tr->rollBack();
    		echo( $e->getMessage() );
    	}
    }
    /**
     * Guarda un OCW
     * @throws Exception
     */
 	public function saveAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	$OCW = new Table_OCW();
    	// borro el cache si existiera no importa si puede o no
    	$OCW->removeFromCache( $this->getRequest()->getParam('id', null) );
    	$tr = $OCW->getAdapter()->beginTransaction();
    	try {
			$campos = $this->getRequest()->getParam('campos'); // todos los campos a guardar
			$categories = $this->getRequest()->getParam('categorias'); // categorias asociadas
			$authors = $this->getRequest()->getParam('autores'); // autores asociadas
			$accion = $this->getRequest()->getParam('accion');
			$Id = $this->getRequest()->getParam('id', null);
			$degrees = $this->getRequest()->getParam('degrees'); // degrees asociadas
						
			if( !is_null($campos) ){ // editar o añadir
				$camposOCW = $OCW->getFields(); // array de campos a guardar en OCW
				if(!is_null($accion) && $accion == 'edit'){
					$newOcw = $OCW->fetchRow($OCW->select()->where('id = ?', $Id));
				}else{
					$newOcw = $OCW->createRow();
				}
				$type = null;
				foreach ($campos as $campo){
					if( in_array($campo['campo'], $camposOCW) ){
						if($campo['campo'] == 'idType'){
							$type = $campo['valor'];
						}
						if($campo['campo'] == 'ocwTitle'){

							if($type != 2 && $type != 5 ){
								$OCW->removeCacheIndex($type); // remuevo el indice del cache
								//verifico si existe el title para cualquier type != de 2 y 5
								$buscotitle = $OCW->fetchRow($OCW->select()->where('ocwTitle = ?', "'".$campo['valor']."'")->where('idType = ?', $type));
								if(!empty($buscotitle) || $buscotitle <> null){ throw new Exception("The title is already exists");}
								$newOcw->ocwTitleEncode = codificar_titulo($campo['valor']);
							}else{
								$newOcw->ocwTitleEncode = null;
							}

						}
						if($campo['campo'] == 'thumbnail' && $campo['valor'] != "" ) {
							$campo['valor'] = trim($campo['valor']);
							$validthumbnail = Zend_Uri::check($campo['valor']);
							if(!$validthumbnail) throw new Exception("Invalid thumbnail");
						}
						$newOcw->$campo['campo'] = $campo['valor'];

					}
				}
				$ocwID= $newOcw->save();

				//guardo course
				if($type == 1){
					$Course = new Table_Course();
					$Course->delete('idOCW='.$ocwID);
					$newCourse = $Course->createRow();
					$camposCourse = $Course->getFields();
					foreach ($campos as $campo){
						if( in_array($campo['campo'], $camposCourse) ){ 
							if($campo['campo'] == 'ocwOpenstudyUrl' && $campo['valor'] != "" ) {
								$campo['valor'] = trim($campo['valor']);
								$validopenstudy = Zend_Uri::check($campo['valor']);
								if(!$validopenstudy) throw new Exception("Invalid Open Study Url");
							}
							if($campo['campo'] == 'ocwPartnerUrl' && $campo['valor'] != "" ) {
								$campo['valor'] = trim($campo['valor']);
								$validpartner = Zend_Uri::check($campo['valor']);
								if(!$validpartner) throw new Exception("Invalid Partner Url");
							}
							if($campo['campo'] == 'ocwUrlCourse' && $campo['valor'] != "" ) {
								$campo['valor'] = trim($campo['valor']);
								$validurl = Zend_Uri::check($campo['valor']);
								if(!$validurl) throw new Exception("Invalid Url");
							}
							if($campo['campo'] == 'ocwBypassUrlCourse' && $campo['valor'] != "" ) {
								$campo['valor'] = trim($campo['valor']);
								$validpassurl = Zend_Uri::check($campo['valor']);
								if(!$validpassurl) throw new Exception("Invalid By pass Url");
							}
							$newCourse->$campo['campo'] = $campo['valor'];
							$newCourse->idOCW = $ocwID;
						}
					}
					$newCourse->save();

					//Books code
					
					// Guardo los libros
					$books = new Table_Books();
					$camposBooks = $books->getFields();

					$books_source = array();
					foreach ($campos as $campo){
						if( in_array($campo['campo'], $camposBooks) )
							$books_source[] = $campo;
					}

					$table_BooksOCW = new Table_BooksOCW();
					$table_BooksOCW->delete('idOCW='.$ocwID);
					
					$books_array = array();
					$cnt=-1;
					for($i=0; $i<count($books_source); $i++){
						if($books_source[$i]['campo']=="bookName")
							$cnt++;
						
						$books_array[$cnt][] = $books_source[$i];
						
						if($books_source[$i]['campo']=="idBooks" && $books_source[$i]['valor']!="")
							$books->delete('idBooks='.$books_source[$i]['valor']);
					}
					
					$principal = 1;
					for($i=0; $i<count($books_array); $i++){
						$save = true;
						if($books_array[$i][0]['valor']=="")
							$save = false;	
						if($save){
							$newBooks = $books->createRow();
							$newTableBooksOCW = $table_BooksOCW->createRow();
							foreach ($books_array[$i] as $campo){
								$newBooks->$campo['campo'] = $campo['valor'];
								if($principal){
									$newBooks->bookPrincipal = $principal;
									$principal = 0;
								}
							}
							$idbooks = $newBooks->save();
							$newTableBooksOCW->idBooks = $idbooks;
							$newTableBooksOCW->idOCW = $ocwID;
							$newTableBooksOCW->save();
						}
					}
					//Books code END
				}
				//guardo file
				if($type == 2){
					$File = new Table_File();
					$File->delete('idOCW='.$ocwID);
					$newFile = $File->createRow();
					$camposFile = $File->getFields();
					foreach ($campos as $campo){
						if( in_array($campo['campo'], $camposFile) ){
							$newFile->$campo['campo'] = $campo['valor'];
							$newFile->idOCW = $ocwID;
						}
						if($campo['campo'] == 'ocwUrlFile' && $campo['valor'] != "" ) {
							$campo['valor'] = trim($campo['valor']);
							$validurl = Zend_Uri::check($campo['valor']);
							if(!$validurl) throw new Exception("Invalid Url");
						}
					}
					$newFile->save();
				}
				
				//guardo lecture
				if($type == 3){
					$Lecture = new Table_Lecture();
					$Lecture->delete('idOCW='.$ocwID);
					$newLecture = $Lecture->createRow();
					$camposLecture = $Lecture->getFields();
					foreach ($campos as $campo){
						if( in_array($campo['campo'], $camposLecture) ){
							$newLecture->$campo['campo'] = $campo['valor'];
							$newLecture->idOCW = $ocwID;
						}
						if($campo['campo'] == 'ocwUrlLecture' && $campo['valor'] != "" ) {
							$campo['valor'] = trim($campo['valor']);
							$validurl = Zend_Uri::check($campo['valor']);
							if(!$validurl) throw new Exception("Invalid Url");
						}
						if($campo['campo'] == 'ocwBypassUrlLecture' && $campo['valor'] != "" ) {
							$campo['valor'] = trim($campo['valor']);
							$validpassurl = Zend_Uri::check($campo['valor']);
							if(!$validpassurl) throw new Exception("Invalid By pass Url");
						}
					}
					$newLecture->save();
				}
				
				//guardo collection
				if($type == 4){
					$Collection = new Table_Collection();
					$Collection->delete('idOCW='.$ocwID);
					$newCollection = $Collection->createRow();
					$camposCollection = $Collection->getFields();
					foreach ($campos as $campo){
						if( in_array($campo['campo'], $camposCollection) ){
							$newCollection->$campo['campo'] = $campo['valor'];
							$newCollection->idOCW = $ocwID;
						}
					}
					$newCollection->save();
				}

				//guardo conference
				if($type == 6){
					$Conference = new Table_Conference();
					$Conference->delete('idOCW='.$ocwID);
					$newConference = $Conference->createRow();
					$camposCoference = $Conference->getFields();
					foreach ($campos as $campo){
						if( in_array($campo['campo'], $camposCoference) ){
							$newConference->$campo['campo'] = $campo['valor'];
							$newConference->idOCW = $ocwID;
						}
					}
					$newConference->save();
				}

				//insertar categories
				if(!empty($categories)){
					$OCWCategories = new Table_OCWCategory();
					$OCWCategories->delete('idOCW='.$ocwID);

					foreach ($categories as $category){
						$newOCWCategory= $OCWCategories->createRow();
						$newOCWCategory->idOCW = $ocwID;
						$newOCWCategory->idCat = $category['id'];
						$newOCWCategory->occSequence= $category['order'];
						$newOCWCategory->save();
					}
				}

				if(!empty($authors)){
					$Author = new Table_Author();
					$Author->delete('idOCW='.$ocwID);

					foreach ($authors as $author){
						$newAuthor = $Author->createRow();
						$newAuthor->idOCW = $ocwID;
						$newAuthor->idPer = $author['id'];
						$newAuthor->autSequence = $author['order'];
						$newAuthor->save();
					}
				}
				//guardo los grados seleccionados
				$AuthorOCW = new Table_AuthorOCW();
				$AuthorOCW->delete('idOCW='.$ocwID);
				if(!empty($degrees)){
					foreach($degrees as $value){
						$newAuthorOcw = $AuthorOCW->createRow();
						$pos = strpos($value["campo"], '_');
						$id_pers = substr($value["campo"], 0, $pos);
						$newAuthorOcw->idPer = $id_pers;
						$newAuthorOcw->idDeg = $value["valor"];
						$newAuthorOcw->idOCW = $ocwID;
						$newAuthorOcw->save();
					}
				}
				echo Zend_Json_Encoder::encode(array('id'=> $ocwID));
				$tr->commit();

			} else {
				throw new Exception("Insufficient parameters");
			}

    	} catch (Exception $e){
    		$tr->rollBack();
    		echo( $e->getMessage() );
    	}

    }
    /**
     * Wizard de edicion y creacion de un OCW
     */
    public function editocwAction(){
    	try{
    		$us = new Table_User();
    		$usuario = $us::getIdentity();
    		$this->view->assign('usuario',$usuario);

    		$Id = $this->getRequest()->getParam('id', null);
    		$accion = $this->getRequest()->getParam('accion', null);
    		$where = "1=1";
    		$cats = null;
    		if(is_null($Id)) { // nuevo ocw
    			$this->view->headTitle('Ocw :: Add');
    			$this->view->assign('accion', 'add');
    			$this->view->assign('id', null);
    			$this->view->assign('Ocw', null);
    			$Id = 0;
    			$CreditTypes = new Table_CreditType();
    			$creditTypes = $CreditTypes->fetchAll($CreditTypes->select());
    			$this->view->assign('creditTypes', $creditTypes);
    		}
    		// TYPES
			$OCWTypes = new Table_OCWTypes();
			if($usuario->idRole == 3){
				$types = $OCWTypes->fetchAll('id=3', 'typName ASC');
			}elseif($usuario->idRole == 4){
				$types = $OCWTypes->fetchAll('id in(1,4)', 'typName ASC');
			}else{
				$types = $OCWTypes->fetchAll('visibility=1', 'typName ASC');
			}
			$this->view->assign('types', $types);
			// Languages
			$Languages = new Table_Language();
			$languages = $Languages->fetchAll('1=1','lanName ASC');
			$this->view->assign('languages', $languages);

			$Categories = new Table_Category();
			$categories = $Categories->getTreeWithPaths();
			$this->view->assign('categories', $categories);

			// Authors
			$Authora = new Table_Author();
			$authors = $Authora->getAuthorsRows();
			$this->view->assign('authors', $authors);
			// AuthorOcw
			if(!empty($Id)){
				$authorocw = $Authora->getAuthorOcw($Id);
				$this->view->assign('authorocw', $authorocw);
			}
			//University
			$University = new Table_University();
			$university = $University->fetchAll('1=1', 'uniName');
			$this->view->assign('universitys', $university);
			//School
			$School = new Table_School();
			$school = $School->fetchAll('1=1', 'schName');
			$this->view->assign('schools', $school);
			//department
			$Department = new Table_Department();
			$department = $Department->fetchAll('1=1', 'depName');
			$this->view->assign('departments', $department);

	    } catch (Exception $e){
    		$this->view->assign( 'error', $e->getMessage() );
    		$this->_forward('index', 'ocw', 'admin');
    	}

    }
    /**
     * Edición con un método separado del wizard de creación
     */
    public function editAction(){
        try{
            $us = new Table_User();
            $usuario = $us::getIdentity();
            $this->view->assign('usuario',$usuario);
            $Id = $this->getRequest()->getParam('id', null);
            $accion = $this->getRequest()->getParam('accion', null);
            $where = "1=1";
            $cats = null;
			
            $this->view->headTitle('Ocw :: Edit');
            $Ocw = new Table_OCW();
            $ocw = $Ocw->fetchRow($Ocw->select()->where('id = ?', $Id));
            if(!is_null($Id) && is_null($accion)) {
                $this->view->assign('Ocw', $ocw);

                if(!empty($ocw->idType)){
                    switch ($ocw->idType){
                        case 1:
                            $Course = new Table_Course();
                            $course = $Course->fetchRow($Course->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Course', $course);
                            $CreditTypes = new Table_CreditType();
                            $creditTypes = $CreditTypes->fetchAll($CreditTypes->select());
                            $this->view->assign('creditTypes', $creditTypes);
                            $this->view->assign('contentType', 'course');

							//Books code
							$BooksOCW = new Table_BooksOCW();
							$booksocw = $BooksOCW->fetchAll($BooksOCW->select()->from('BooksOCW',array('idBooks'))->where('idOCW = ?', $Id));
							
							if(count($booksocw) > 0){
                                //var_dump($booksocw);
                                $alert = array();
                                foreach($booksocw as $valor) {
									$alert[] = $valor['idBooks'];
								}

								$Books = new Table_Books();
								$books = $Books->fetchRow($Books->select()->where('idBooks IN (?)', $alert));
								$this->view->assign('books', $books);
								
								$otherbooks = $Books->select()->where('idBooks IN (?)', $alert)
															  ->where('bookPrincipal = 0');
								$other = $Books->fetchAll($otherbooks);
								
								$this->view->assign('other', $other);
							}							
							//Books code END
                            break;
                        case 2:
                            $File = new Table_File();
                            $file = $File->fetchRow($File->select()->where('idOCW = ?', $Id));
                            $this->view->assign('File', $file);
                            $this->view->assign('contentType', 'file');
                            break;
                        case 3:
                            $Lecture = new Table_Lecture();
                            $lecture = $Lecture->fetchRow($Lecture->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Lecture', $lecture);
                            $this->view->assign('contentType', 'lecture');
                            break;
                        case 4:
                            $Collection = new Table_Collection();
                            $collection = $Collection->fetchRow($Collection->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Collection', $collection);
                            $this->view->assign('contentType', 'collection');
                            break;
                        case 6:
                            $Conference = new Table_Conference();
                            $conference = $Conference->fetchRow($Conference->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Conference', $conference);
                            $this->view->assign('contentType', 'conference');
                            break;
                    }
                }

                $OcwCategories = new Table_OCWCategory();
                $Categories = new Table_Category();

                $select = $OcwCategories->select()
                    ->setIntegrityCheck(false)
                    ->from(array('r0'=> 'OCWCategory'), array('r0.idOCW', 'r0.occSequence'))
                    ->joinInner( array('r2'=> 'Category') , 'r0.idCat = r2.id', array('r2.catName', 'r2.id as catid'))
                    ->where('r0.idOCW = ?', $Id)
                    ->order('r0.occSequence')
                ;
                $ocwcategories = $OcwCategories->getAdapter()->fetchAll( $select ) ;

                $ocwcat = null;$cats = 0;
                foreach($ocwcategories as $value){
                    $paths = $Categories->getPath($value["catid"]);
                    $ocwcat[] = array('idOCW'=>$value["idOCW"], 'occSequence'=>$value["occSequence"], 'catName'=>$value["catName"],'catid'=>$value["catid"], 'path'=> $paths[0]["path"]);
                    $cats .= ','.$value["catid"];
                }
                $this->view->assign('ocwcategories', $ocwcat );
                $this->view->assign('idcats', explode(',',$cats) );
                $this->view->assign('accion', 'edit');
            }
            if(is_null($Id)) { // nuevo ocw
                $this->view->headTitle('Ocw :: Add');
                $this->view->assign('accion', 'add');
                $this->view->assign('id', null);
                $this->view->assign('Ocw', null);
                $Id = 0;
                $CreditTypes = new Table_CreditType();
                $creditTypes = $CreditTypes->fetchAll($CreditTypes->select());
                $this->view->assign('creditTypes', $creditTypes);
            }

            // TYPES
            $OCWTypes = new Table_OCWTypes();
            if($usuario->idRole == 3){
                $types = $OCWTypes->fetchAll('id=3', 'typName ASC');
            }elseif($usuario->idRole == 4){
                $types = $OCWTypes->fetchAll('id in(1,4)', 'typName ASC');
            }else{
                $types = $OCWTypes->fetchAll('visibility=1', 'typName ASC');
            }
            $this->view->assign('types', $types);
            // Languages
            $Languages = new Table_Language();
            $languages = $Languages->fetchAll('1=1','lanName ASC');
            $this->view->assign('languages', $languages);

            $Categories = new Table_Category();
            $categories = $Categories->getTreeWithPaths();
            $this->view->assign('categories', $categories);

            // Authors
            $Authora = new Table_Author();
            $authors = $Authora->getAuthorsRows();
            $this->view->assign('authors', $authors);
            // AuthorOcw
            if(!empty($Id)){
                $authorocw = $Authora->getAuthorOcw($Id);
                $this->view->assign('authorocw', $authorocw);
            }
            //University
            $University = new Table_University();
            $university = $University->fetchAll('1=1', 'uniName');
            $this->view->assign('universitys', $university);
            //School
            $School = new Table_School();
            $school = $School->fetchAll('1=1', 'schName');
            $this->view->assign('schools', $school);
            //department
            $Department = new Table_Department();
            $department = $Department->fetchAll('1=1', 'depName');
            $this->view->assign('departments', $department);

        } catch (Exception $e){
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'ocw', 'admin');
        }

    }

    public function editacAction(){
        try{
            $us = new Table_User();
            $usuario = $us::getIdentity();
            $this->view->assign('usuario',$usuario);

            $Id = $this->getRequest()->getParam('id', null);
            $accion = $this->getRequest()->getParam('accion', null);
            $where = "1=1";
            $cats = null;
            if(!is_null($Id) && is_null($accion)) {
                $this->view->headTitle('Ocw :: Edit');
                $Ocw = new Table_OCW();
                $ocw = $Ocw->fetchRow($Ocw->select()->where('id = ?', $Id));
                $this->view->assign('Ocw', $ocw);

                if(!empty($ocw->idType)){
                    switch ($ocw->idType){
                        case 1:
                            $Course = new Table_Course();
                            $course = $Course->fetchRow($Course->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Course', $course);
                            $CreditTypes = new Table_CreditType();
                            $creditTypes = $CreditTypes->fetchAll($CreditTypes->select());
                            $this->view->assign('creditTypes', $creditTypes);

                            $this->view->assign('contentType', 'courses');

                            break;
                        case 2:
                            $File = new Table_File();
                            $file = $File->fetchRow($File->select()->where('idOCW = ?', $Id));
                            $this->view->assign('File', $file);

                            break;
                        case 3:
                            $Lecture = new Table_Lecture();
                            $lecture = $Lecture->fetchRow($Lecture->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Lecture', $lecture);

                            $this->view->assign('contentType', 'lectures');

                            break;
                        case 4:
                            $Collection = new Table_Collection();
                            $collection = $Collection->fetchRow($Collection->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Collection', $collection);

                            $this->view->assign('contentType', 'collections');

                            break;
                        case 6:
                            $Conference = new Table_Conference();
                            $conference = $Conference->fetchRow($Conference->select()->where('idOCW = ?', $Id));
                            $this->view->assign('Conference', $conference);

                            $this->view->assign('type', 'conferences');

                            break;
                    }
                }

                $OcwCategories = new Table_OCWCategory();
                $Categories = new Table_Category();

                $select = $OcwCategories->select()
                    ->setIntegrityCheck(false)
                    ->from(array('r0'=> 'OCWCategory'), array('r0.idOCW', 'r0.occSequence'))
                    ->joinInner( array('r2'=> 'Category') , 'r0.idCat = r2.id', array('r2.catName', 'r2.id as catid'))
                    ->where('r0.idOCW = ?', $Id)
                    ->order('r0.occSequence')
                ;
                $ocwcategories = $OcwCategories->getAdapter()->fetchAll( $select ) ;

                $ocwcat = null;$cats = 0;
                foreach($ocwcategories as $value){
                    $paths = $Categories->getPath($value["catid"]);
                    $ocwcat[] = array('idOCW'=>$value["idOCW"], 'occSequence'=>$value["occSequence"], 'catName'=>$value["catName"],'catid'=>$value["catid"], 'path'=> $paths[0]["path"]);
                    $cats .= ','.$value["catid"];
                }
                $this->view->assign('ocwcategories', $ocwcat );
                $this->view->assign('idcats', explode(',',$cats) );
                $this->view->assign('accion', 'edit');
            }
            if(is_null($Id)) { // nuevo ocw
                $this->view->headTitle('Ocw :: Add');
                $this->view->assign('accion', 'add');
                $this->view->assign('id', null);
                $this->view->assign('Ocw', null);
                $Id = 0;
                $CreditTypes = new Table_CreditType();
                $creditTypes = $CreditTypes->fetchAll($CreditTypes->select());
                $this->view->assign('creditTypes', $creditTypes);
            }

            // TYPES
            $OCWTypes = new Table_OCWTypes();
            if($usuario->idRole == 3){
                $types = $OCWTypes->fetchAll('id=3', 'typName ASC');
            }elseif($usuario->idRole == 4){
                $types = $OCWTypes->fetchAll('id in(1,4)', 'typName ASC');
            }else{
				$types = $OCWTypes->fetchAll('visibility=1', 'typName ASC');
            }
            $this->view->assign('types', $types);
            // Languages
            $Languages = new Table_Language();
            $languages = $Languages->fetchAll('1=1','lanName ASC');
            $this->view->assign('languages', $languages);

            $Categories = new Table_Category();
            $categories = $Categories->getTreeWithPaths();
            $this->view->assign('categories', $categories);

            // Authors
            $Authora = new Table_Author();
            $authors = $Authora->getAuthorsRows();
            $this->view->assign('authors', $authors);
            // AuthorOcw
            if(!empty($Id)){
                $authorocw = $Authora->getAuthorOcw($Id);
                $this->view->assign('authorocw', $authorocw);
            }
            //University
            $University = new Table_University();
            $university = $University->fetchAll('1=1', 'uniName');
            $this->view->assign('universitys', $university);
            //School
            $School = new Table_School();
            $school = $School->fetchAll('1=1', 'schName');
            $this->view->assign('schools', $school);
            //department
            $Department = new Table_Department();
            $department = $Department->fetchAll('1=1', 'depName');
            $this->view->assign('departments', $department);

        } catch (Exception $e){
            $this->view->assign( 'error', $e->getMessage() );
            $this->_forward('index', 'ocw', 'admin');
        }

    }  

	/**
	 * obtiene las schools para llenar el combo del wizard
	 */
    public function getschoolajaxAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
		$idUniversity = $this->getRequest()->getParam('idUniversity', null);
		$School = new Table_School();
		$where = $School->select()->where('idUniversity = ?', $idUniversity);
		//echo $where->assemble(); exit;
		$school = $School->fetchAll($where)->toArray();
		echo Zend_Json_Encoder::encode($school);
    }
	
    /**
     * Obtiene los Deparments para llenar el combo del wizard
     */
    public function getdepartmentajaxAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	$idSchool = $this->getRequest()->getParam('idSchool', null);
    	$Department = new Table_Department();
    	$where = $Department->select()->where('idSchool = ?', $idSchool);
    	$department = $Department->fetchAll($where)->toArray();
    	echo Zend_Json_Encoder::encode($department);
    }

    /**
     * Método para subir files o images
     */
    public function uploadfileAction(){
    	$this->_helper->layout()->setLayout('empty');
    	$this->_helper->viewRenderer->setNoRender();
    	set_error_handler('handleError');

        try {
        	$data = $_FILES['qqfile'];
        	if(!empty($data)){
				$file = $_FILES['qqfile'];

				$error_text = true;
				$mime_filter = array( 'application/pdf'
									, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // docx
									, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' //xlsx
									, 'application/vnd.ms-powerpoint' //pps
									, 'application/vnd.ms-excel' // xls
									, 'application/excel' // xls
									, 'application/msword' // doc
									, 'application/mspowerpoint' //pps
									, 'image/gif', 'image/jpeg', 'image/png'
				);

				define("UPLOAD_ERR_EMPTY",5);
				define("UPLOAD_ERR_MIME", 6);

				if($file['size'] === 0 && $file['error'] === 0 && empty($file['size'])){
					$file['error'] = 5;
				}

				$tamano_permitido = ($this->getRequest()->getParam('MAX_FILE_SIZE', 0 ) / 1024 ) / 1024 ;

				$upload_errors = array(
					UPLOAD_ERR_OK           => "Without error.",
					UPLOAD_ERR_INI_SIZE     => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
					UPLOAD_ERR_FORM_SIZE    => "The uploaded file exceeds the size or maximum permitted of " . $tamano_permitido . ' MB' ,
					UPLOAD_ERR_PARTIAL      => "The uploaded file was partially loaded.",
					UPLOAD_ERR_NO_FILE      => "No file was uploaded.",
					UPLOAD_ERR_NO_TMP_DIR   => "Lack the temporary folder.",
					UPLOAD_ERR_CANT_WRITE   => "Unable to write the file on the disk.",
					UPLOAD_ERR_EXTENSION    => "File upload stopped by extension.",
					UPLOAD_ERR_EMPTY        => "Not specified file.",
					UPLOAD_ERR_MIME         => 'The file must be a PDF document or a MS Office file, the specified file is: "'. $file["type"] . '" '
				);
				if(empty($file['error'])){
					if(!in_array($file["type"], $mime_filter)){
						$file['error'] = 6;
					}
				}
				if(!empty($file['error'])){
					$error = ($error_text) ? $upload_errors[$file['error']] : $file['error'] ;
					throw new Exception($error);
				} else { // se puede subir el archivo
					try{

						 $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
						 $filename = codificar_filename($file['name']);
						 $nombre_base = basename($filename, '.'.$extension);

						 $newname = $nombre_base. '.' . $extension;
						 $folder = $this->getRequest()->getParam('target', 'files');
						 $uploadTarget = 'upload/'.$folder.'/';
						 $msg = "";
						 if (!file_exists($uploadTarget)){
							mkdir($uploadTarget, 0777);
						 }
						 move_uploaded_file($file["tmp_name"], $uploadTarget . $newname);

					}catch ( Exception $e){
						throw new Exception("Don't save this file");
					}

				}
        	}
			$ret = array("success" => true, "nameFile" =>$newname);
			echo Zend_Json_Encoder::encode($ret);
        } catch ( Exception $e ) {
			$ret = array("error" => $e->getMessage());
			echo Zend_Json_Encoder::encode($ret);
        }
    }

     /**
     * Método para rellenar los datos de la grilla de ocw
     */
    public function ocwgridAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	$filters = Zend_Json_Decoder::decode($this->getRequest()->getParam('filters', '{}'));
		$where = '';
		if(!is_null($filters)){
			$where = $this->buildWherelp($filters) . $where;
		}

    	$i = 0;

		$sEcho = $this->getRequest()->getParam('sEcho', 4);
		// sorting
		$iSortCol_0 = $this->getRequest()->getParam('iSortCol_0', 1);
		$sSortDir_0 = $this->getRequest()->getParam('sSortDir_0', 'asc');
		$sort = array('columna'=> $iSortCol_0 , 'direccion'=> $sSortDir_0);
		//Pagging
		$iDisplayLength=$this->getRequest()->getParam('iDisplayLength', 50); // limit
		$iDisplayStart = $this->getRequest()->getParam('iDisplayStart', 0); // offset
		$limit = array('limit'=>$iDisplayLength, 'offset'=> $iDisplayStart);
		//search de la grilla
		$sSearch = $this->getRequest()->getParam('sSearch', '');

		$us = new Table_User();
		$usuario = $us::getIdentity();

    	$OCW= new Table_OCW();
    	$ocws = $OCW->getOcwGrid($where, $sort, $limit, $sSearch, $usuario->idRole );

    	$cursor = $ocws['cursor'];
		$totalRegistros = $ocws['count'];
		$totalRegistrosWere = $ocws['countWhere'];

    	echo '{"aaData": [';
    	while ($row = $cursor->fetch(Zend_Db::FETCH_NUM)) {

    		if ($i <= 5000) {
    			if ($i > 0) {
    				echo ",";
    			}
    			$json = '{"DT_RowId": "'.$row[0].'", ';
    			$PK = array_shift($row); // se asume que la primer fila tiene PK
    			foreach($row AS $k => $v){
    				$json.= '"'.$k.'":'.Zend_Json_Encoder::encode($v).',';
    			}
    			$json = substr ($json, 0, -1);
    			$json .= '}';
    			echo $json;
    			$i++;
    		} else {
    			break;
    		}
    	}
    	echo "],";
		echo '"sEcho": '.$sEcho.',';
		echo '"iTotalRecords":'. '"'.$totalRegistros.'",';
		echo '"iTotalDisplayRecords": "'.$totalRegistrosWere.'"';
		echo "}";
    }
	
    /**
     * Agrega un Encabezado a los joins
     */
    public function addocwheaderAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	try {
    		$ocwTitle = $this->getRequest()->getParam('ocwTitle','Error');
    		$OCW = new Table_OCW();
    		$new = $OCW->createRow();
    		$new->idType = 5; // tipo header
    		$new->idLanguage = 1; //cualquier lenguaje
    		$new->ocwTitle = $ocwTitle;
    		$new->ocwGolive = 1;
    		$id = $new->save();
    		$result = array('ocwTitle' => $ocwTitle, 'id' => $id );
    		echo Zend_Json_Encoder::encode($result);
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }
	
    /**
     * Agrega un Label a los joins
     */
    public function addocwlabelAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	try {
    		$ocwDescription = $this->getRequest()->getParam('ocwDescription','Error');
    		$OCW = new Table_OCW();
    		$new = $OCW->createRow();
    		$new->idType = 7; // tipo Label
    		$new->idLanguage = 1; //cualquier lenguaje
    		$new->ocwTitle = 'Label';
    		$new->ocwDescription = $ocwDescription;
    		$new->ocwGolive = 1;
    		$id = $new->save();
    		$result = array('ocwDescription' => $ocwDescription, 'id' => $id );
    		echo Zend_Json_Encoder::encode($result);
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }

    /**
     * Elimina un header/label de los Joins
     * @throws Exception
     */
    public function removeocwjoinAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();
    	$OCW = new Table_OCW();
    	$OCWJOINS = new Table_OCWJoin();
    	$tr = $OCW->getAdapter()->beginTransaction();
    	try {
    		$ocwId = $this->getRequest()->getParam('id',null);
    		if (is_null($ocwId)){
    			throw new Exception('OCW Id is null');
    		}
			// borro los Joins
    		$OCWJOINS->delete("idOCWChild  = $ocwId ");
    		$OCW->delete("id = $ocwId");
    		$result = array('res' => true);
    		$tr->commit();
    		echo Zend_Json_Encoder::encode($result);
    	} catch (Exception $e) {
    		$tr->rollBack();
    		echo $e->getMessage();
    	}
    }

    public function getlicenseAction(){
    	$this->_helper->layout()->setLayout('empty');
    	// no necesita vista para renderizarse
    	$this->_helper->viewRenderer->setNoRender();

    	$authors = $this->getRequest()->getParam( 'attribution_name', array() );
    	$field_commercial = $this->getRequest()->getParam('field_commercial', 'y');
    	$field_derivatives = $this->getRequest()->getParam('field_derivatives', 'y');
    	$licclass = $this->getRequest()->getParam('licclass', 'standard');
    	$licjurisdiction = $this->getRequest()->getParam('licjurisdiction', '');
    	$licjurisdictionText = $this->getRequest()->getParam('licjurisdictionText', '');
    	$licjurisdiction = !empty($licjurisdiction) && $licjurisdiction != '' ? str_replace('/', '',str_replace('http://creativecommons.org/international/', '', $licjurisdiction)) : '';
    	$version = $this->getRequest()->getParam('version', '4');
    	if($version == '4'){
    		$licjurisdiction = '';
    	}
    	$more_permissions_url = $this->getRequest()->getParam('more_permissions_url', '');
    	$more_permissions_url = !empty($more_permissions_url) && $more_permissions_url != '' ? $more_permissions_url : '';
    	$work_url = $this->getRequest()->getParam('work_url', null);
    	$work_url = !empty($work_url) && $work_url != '' ? $work_url : '';
    	$title = $this->getRequest()->getParam('title', 'UC Irvine OCW');
    	$type  = $this->getRequest()->getParam('type', '');
    	$autores = array();
    	$Person = new Table_Person();
    	$autorString = null;
    	if(!empty($authors) && count($authors)>0){
	    	foreach ( $authors as $author ){
	    		$persona = $Person->fetchRow( $Person->select()->where("id = ?", $author["id"] ) );
	    		$autores[$author["order"]] = $persona->perLastName . ', '. $persona->perFirstName;
	    	}
    		$autorString = implode('; ', $autores);
    	}
    	$work_info = array();
    	$work_info['title'] = $title;
    	if(!empty($work_url) && strlen($work_url) > 0 && !is_null($work_url)){
    		$work_info['work-url'] = $work_url;
    	}
    	if(!is_null($autorString) && strlen($autorString) >0 ){
    		$work_info['attribution_name'] = $autorString;
    	}
    	if(!empty($more_permissions_url) && strlen($more_permissions_url) > 0 && !is_null($more_permissions_url)){
    		$work_info['more_permissions_url'] = $more_permissions_url;
    	}    	
    	if($licclass == 'zero'){
    		
    		$html ='<p xmlns:dct="http://purl.org/dc/terms/" xmlns:vcard="http://www.w3.org/2001/vcard-rdf/3.0#">';
    		$html .= '<a rel="license" href="http://creativecommons.org/publicdomain/zero/1.0/"><img src="http://i.creativecommons.org/p/zero/1.0/88x31.png" style="border-style: none;" alt="CC0" /></a>';
    		$html .= '<br />';
    		$html .= 'To the extent possible under law,';
    		if(!empty($work_url) && $work_url !=''){
    			$html .= '<a rel="dct:publisher" href="'.$work_url.'">';
    			$html .= '<span property="dct:title">'.$autorString.'</span></a>';
    		} else {
    			$html .= '<span property="dct:title">'.$autorString.'</span>';
    		}
    		$html .= 'has waived all copyright and related or neighboring rights to ';	
    		$html .= '<span property="dct:title">'.$title.'</span>.';
    		if(!empty($licjurisdiction) && $licjurisdiction!=''){
    			$html .= 'This work is published from: ';
    			$html .= '<span property="vcard:Country" datatype="dct:ISO3166" content="'.$licjurisdiction.'" about="'.$work_url.'">'.$licjurisdictionText.'</span>.</p>';
    		}
    		echo $html;
    		
    	} elseif ($licclass == 'mark'){
    		$html = '<p xmlns:dct="http://purl.org/dc/terms/">';
    		$html .= '<a rel="license" href="http://creativecommons.org/publicdomain/mark/1.0/">';
    		$html .= '<img src="http://i.creativecommons.org/p/mark/1.0/88x31.png" style="border-style: none;" alt="Public Domain Mark" />';
    		$html .= '</a><br />';
    		$html .= 'This work (<span property="dct:title">'.$title.'</span>, by ';
    		if(!empty($work_url) && $work_url !=''){
	    		$html .= '<a href="'.$work_url.'" rel="dct:creator"><span property="dct:title">'. $autorString .'</span></a>';
    		} else {
    			$html .= '<span property="dct:title">'. $autorString .'</span>';
    		}
    		$html .= '), identified by <a href="http://ocw.uci.edu" rel="dct:publisher"><span property="dct:title">UCIrvine OCW</span></a>, is free of known copyright restrictions.</p>';
    		echo $html;
    	}else{
	    	$xml = new SimpleXMLElement('<result/>');
			$response = 'http://api.creativecommons.org/rest/1.5/license/standard/get?commercial='.$field_commercial.'&derivatives='.$field_derivatives.'&jurisdiction='.$licjurisdiction;
			$xml = simplexml_load_file($response);
			$html = $xml->html->asXML();
			
			$htmlarr = explode("work", $html);
			$autorString = !empty($autorString) ? " by ".$autorString : "";
			if(!empty($work_url) && !is_null($work_url))
				$title = '<a rel="Work URL" href="'.$work_url.'">'.$title.'</a>';
			if(!empty($more_permissions_url) && !is_null($more_permissions_url))
				$more_permissions = ' <a rel="More permissions" href="'.$more_permissions_url.'">More permissions.</a>';
			else
				$more_permissions = "";
			$html = $htmlarr[0]." work (".$title.$autorString.") ".$htmlarr[1].$more_permissions;

	    	echo( $html ) ;
    	}
    }

    private function array_to_xml($student_info, &$xml_student_info) {
    	foreach($student_info as $key => $value) {
    		if(is_array($value)) {
    			if(!is_numeric($key)){
    				$subnode = $xml_student_info->addChild("$key");
    				$this->array_to_xml($value, $subnode);
    			}
    			else{
    				$subnode = $xml_student_info->addChild("item$key");
    				$this->array_to_xml($value, $subnode);
    			}
    		}
    		else {
    			$xml_student_info->addChild("$key",htmlspecialchars("$value"));
    		}
    	}
    }

    /**
     * Método para construcción del where para filtrar la grilla de ocw
     */
    private function buildWherelp($filters){
		$sql = '';
		foreach ($filters AS $campo => $opciones ) {
			$valores = $opciones['values'];
			$operador = $opciones['op'];
			// conversion de operadores relacionales
			// EQ 	NE 	GT 	LT 	GE 	LE
			switch ($operador) {
				case 'EQ':
					// igual, la lista de valores es igual al campo (Sirve para comparar un sólo valor)
					// ya que un campo no puede tener mas de un valor
					$operador = '= ALL';
					break;
				case 'NE':
					// No es igual o diferente a todos los elementos listados
					$operador = '!= ALL';
					break;
				case 'GT':
					// El campo es mayor al del listado de valores
					$operador = '> ANY';
					break;
				case 'LT':
					// El campo es menor al del listado de valores
					$operador = '< ANY';
					break;
				case 'GE':
					// El campo es mayor o igual al del listado de valores
					$operador = '>=';
					break;
				case 'LE':
					// El campo es menor al del listado de valores
					$operador = '<=';
					break;
				case 'IN':
					$operador = 'IN';
					break;
				case 'BETWEEN':
					$operador = 'BETWEEN';
					break;
				case 'LIKE':
					$operador = 'LIKE';
					break;
				default:
					throw new Exception('Operador de filtro avanzado no soportado');
					break;
			}

			if(is_array($valores)){
				if($operador === 'BETWEEN'){
					$sql .= ' AND '  . $campo . ' ' . $operador . ' ';
				}elseif($operador === 'LIKE'){
					$sql .= ' AND LOWER('  . $campo . ') ' . $operador . " '%";
				}else{
					$sql .= ' AND '  . $campo . ' ' . $operador . ' (';
				}
				$removeChars = $operador === 'BETWEEN' ?  5 : 1 ;
				foreach ($valores AS $valor ){
					// detectar si es fecha
					$patron= "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
					//Detecto si es fecha y agrego TO_DATE('27-06-2009','DD-MM-YYYY')
					$valor = preg_match($patron, $valor) ? "'".convFechaSQL($valor)."'" : ( is_numeric($valor) ? $valor : ( $operador !=='LIKE' ? "'" . $valor . "'": strtolower($valor) ) );
					// si el operador es LIKE el separador es % y debo añadirselos si la cadena tiene espacios
					$valor = $operador === 'LIKE' ? str_replace( ' ', '%', $valor ) : $valor;
					$separador = $operador === 'BETWEEN'? ' AND ' : ($operador==='LIKE' ? '%': ',');
					$sql .= $valor . $separador;
				}
				$sql = substr ($sql, 0, ( $removeChars * -1 ) );
				if($operador === 'BETWEEN'){
					$sql .= ' ';
				}elseif($operador === 'LIKE'){
					$sql .= "%' ";
				} else {
					$sql .= ') ';
				}
			}else{
				if($operador === 'BETWEEN'){
					$sql .= ' AND '  . $campo . ' ' . $operador . ' ';
				}elseif($operador === 'LIKE'){
					$sql .= ' AND LOWER('  . $campo . ') ' . $operador . " '%";
				}else{
					$sql .= ' AND '  . $campo . ' ' . $operador ;
				}
				$valor = $valores;
				$patron= "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
				$valor = preg_match($patron, $valor) ? "'".convFechaSQL($valor)."'" : ( is_numeric($valor) ? $valor : ( $operador !=='LIKE' ? "'" . $valor . "'": strtolower($valor) ) );
				$separador = $operador;
				$sql .= $valor ;
			}
		}

		return $sql;
	}
	
	/**
	 * Devuelve el tamaño de un archivo si exite
	 * @param String $typName
	 * @param String $ocwTitleEncode
	 * @return mixed string | boolean
	 */
	private function getDownloadSize($typName, $ocwTitleEncode){
		// lo mas probable es que exitsa el paquete como ZIP, verifico eso primero
		$root = $_SERVER['DOCUMENT_ROOT'];
		$packageName= strtolower($typName) . '_' . $ocwTitleEncode;
		$zipFile = $root.'/packages/'.$packageName.".zip";
		$imsccFile = $root.'/packages/'.$packageName.".imscc";
		// verificar zip
		if(file_exists($zipFile)){
			return $this->human_filesize(filesize($zipFile), 2);
		} elseif (file_exists($imsccFile)){
			return $this->human_filesize(filesize($imsccFile), 2);
		} else {
			return false;
		}

	}	
}
