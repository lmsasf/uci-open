<?php
class Default_BlcController extends Zend_Controller_Action
{
	private $_allOcw;
	
	public function init()
	{
		$OCW = new Table_OCW();
		// all OCW which are not labels or headers and are published
		$select = $OCW->select()
						->from($OCW, array('id'))
						->where('idType != ?',5)
						->where('idType != ?', 7)
						->where('ocwGolive = ?', 1)
		;
		$this->_allOcw = $OCW->fetchAll($select);

	}
	
	public function indexAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		// define the fields in which you have to check the URLs of $ ownData
		// in this fields only saved the url
		$urlFields = array(	  'ocwOpenstudyUrl'
							, 'ocwPartnerUrl'
							, 'creditUrl'
							, 'ocwUrlCourse'
							, 'ocwBypassUrlCourse'
							, 'ocwUrlFile'
							, 'ocwUrlLecture'
							, 'ocwBypassUrlLecture'
		);
		$blc = new Table_BrokenLinks();
		$invalid = array();
		// iterating all OCW
		foreach ($this->_allOcw as $ocw){
			// create object
			$myOcw = new Entity_OCW( (int)$ocw->id );
			// check the url OWNDATA
			$ownData = $myOcw->ownData;
			if(!is_null($ownData) || !empty($ownData)){
				foreach ( $urlFields as $field ){
					if( array_key_exists($field, $ownData ) ){
						if(!empty($ownData[$field]) && strlen(trim($ownData[$field]))>0 ){
							if( $this->checkLink($myOcw, $ownData[$field]) == false ){
								$invalid[] = array('ocwID'=> $myOcw->id, 'field'=> $myOcw->typeName.'.'.$field, 'link'=> $ownData[$field] );
								$blc->getAdapter()->insertIgnore('BrokenLinks', array('ocwID'=> $myOcw->id, 'field'=> $myOcw->typeName.'.'.$field, 'link'=> $ownData[$field] ));
							}
						}
					}
				}
			}
			// check thumbnail
			$thum = trim($myOcw->thumbnail);
			if(!empty( $thum ) && strlen( $thum )>0 ){
				if( $this->checkLink($myOcw, $myOcw->thumbnail) == false ){
					$invalid[] = array('ocwID'=> $myOcw->id, 'field'=>'Thumbnail', 'link'=> $myOcw->thumbnail );
					$blc->getAdapter()->insertIgnore('BrokenLinks', array('ocwID'=> $myOcw->id, 'field'=>'thumbnail', 'link'=> $myOcw->thumbnail ));
				}
			}
			// check the  description (texto)
			$arrayLinks = $this->scanForLinks($myOcw->description);
			if(is_array($arrayLinks) && !empty($arrayLinks)){
				foreach ($arrayLinks as $link ){
					if(!empty($link) && strlen(trim($link))>0 ){
						if($this->checkLink($myOcw, $link) == false ){
							$invalid[] = array('ocwID'=> $myOcw->id, 'field'=>'Description', 'link'=> $link );
							$blc->getAdapter()->insertIgnore('BrokenLinks', array('ocwID'=> $myOcw->id, 'field'=>'description', 'link'=> $link ));
						}
					}
				}
			}
			// check inside the	owndata.colfrequentlyQuest
			if(!is_null($ownData) || !empty($ownData)){
				if(array_key_exists('colfrequentlyQuest', $ownData)){
					$arrayLinks = $this->scanForLinks($ownData['colfrequentlyQuest']);
					if(is_array($arrayLinks) && !empty($arrayLinks)){
						foreach ($arrayLinks as $link ){
							if(!empty($link) && strlen(trim($link)) > 0 ){
								if($this->checkLink($myOcw, $link) == false ){
									$invalid[] = array('ocwID'=> $myOcw->id, 'field'=>'Frecuently asked questions', 'link'=> $link );
									$blc->getAdapter()->insertIgnore('BrokenLinks', array('ocwID'=> $myOcw->id, 'field'=>'colfrequentlyQuest', 'link'=> $link ));
								}
							}
						}
					}
				}
			}
			// check inside the	owndata.confrequentlyQuest
			if(!is_null($ownData) || !empty($ownData)){
				if(array_key_exists('confrequentlyQuest', $ownData)){
					$arrayLinks = $this->scanForLinks($ownData['confrequentlyQuest']);
					if(is_array($arrayLinks) && !empty($arrayLinks)){
						foreach ($arrayLinks as $link ){
							if(!empty($link) && strlen(trim($link)) > 0 ){
								if($this->checkLink($myOcw, $link) == false ){
									$invalid[] = array('ocwID'=> $myOcw->id, 'field'=>'Frecuently asked questions', 'link'=> $link );
									$blc->getAdapter()->insertIgnore('BrokenLinks', array('ocwID'=> $myOcw->id, 'field'=>'confrequentlyQuest', 'link'=> $link ));
								}
							}
						}
					}
				}
			}			
			
		}
		echo Zend_Json::encode($invalid);
		
	}
	/**
	 * Scan the links inside an HTML
	 * @param String $html HTML
	 */
	private function scanForLinks($html){
		$regexp = "<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp/siU", $html, $matches)) {
			return $matches[2];
		}
	}
	/**
	 * Check whether the link is valid and alive
	 * @param string $link
	 * @return boolean
	 */
	private function checkLink($myOcw, $link = '' ){
		if(!empty( $link ) || !is_null( $link ) || strlen( $link ) > 0 ){
			// Check whether the link is valid and alive
			if($myOcw->isAlive($link)){
				return true;
			} else {
				return false;
			}
		}
	}
}
