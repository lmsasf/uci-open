<?php
class Default_BlcController extends Zend_Controller_Action
{
	private $_allOcw;
	
	public function init()
	{
		$OCW = new Table_OCW();
		// todos los OCW que no sean labels o headers y esten publicadas
		$select = $OCW->select()
						->from($OCW, array('id'))
						->where('idType != ?',5)
						->where('idType != ?', 7)
						->where('ocwGolive = ?', 1)
//  						->limit(10) // TODO eliminar esto
		;
		$this->_allOcw = $OCW->fetchAll($select);

	}
	
	public function indexAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		// defino los campos en los que hay que verificar las URLs de $ownData
		// en estos campos se guardan sólo url
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
		// iterar todos los OCW
		foreach ($this->_allOcw as $ocw){
			// crear el objeto
			$myOcw = new Entity_OCW( (int)$ocw->id );
			// verificar las url de OWNDATA
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
			// verificar thumbnail
			$thum = trim($myOcw->thumbnail);
			if(!empty( $thum ) && strlen( $thum )>0 ){
				if( $this->checkLink($myOcw, $myOcw->thumbnail) == false ){
					$invalid[] = array('ocwID'=> $myOcw->id, 'field'=>'Thumbnail', 'link'=> $myOcw->thumbnail );
					$blc->getAdapter()->insertIgnore('BrokenLinks', array('ocwID'=> $myOcw->id, 'field'=>'thumbnail', 'link'=> $myOcw->thumbnail ));
				}
			}
			// verificar dentro de  descripción (texto)
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
			// verificar dentro de 	owndata.colfrequentlyQuest
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
			// verificar dentro de 	owndata.confrequentlyQuest
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
		// for log
		echo Zend_Json::encode($invalid);
		
	}
	/**
	 * Escanea los links que hay dentro de un HTML
	 * @param String $html HTML
	 */
	private function scanForLinks($html){
		$regexp = "<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp/siU", $html, $matches)) {
			//d($matches[2]);
			// $matches[2] = array of link addresses
			// $matches[3] = array of link text - including HTML code }
			return $matches[2];
		}
	}
	/**
	 * Comprueba si el enlace e válido y esta vivo
	 * @param string $link
	 * @return boolean
	 */
	private function checkLink($myOcw, $link = '' ){
		if(!empty( $link ) || !is_null( $link ) || strlen( $link ) > 0 ){
			// verificar si es una URL válida
// 			if(Zend_Uri::check( $link )){
// 				// chequeo si esta viva
				if($myOcw->isAlive($link)){
					return true;
				} else {
					return false;
				}
// 			} else {
// 				return false;
// 			}
		}
	}
}
