<?php
class Admin_MigraController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('empty');
    }

    public function encodetitleAction()
    {
        // action body
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout()->setLayout('empty');
    	$OCW = new Table_OCW();
    	foreach ($OCW->fetchAll( 'idType in( 1, 2, 3, 4, 6)' ) AS $ocwRow ) {
    		d($ocwRow->ocwTitleEncode);
    		$ocwTitle = $ocwRow->ocwTitle;
    		$sql="SELECT count(ocwTitle) FROM OCW WHERE ocwTitle ='$ocwTitle' group by ocwTitle HAVING count(ocwTitle)>1";
    		$select = $OCW->getAdapter()->select()
    						->from('OCW', array('count(ocwTitle)'))
    						->where('ocwTitle = ?', $ocwTitle )
    						->group('ocwTitle')
    						->having('count(ocwTitle)>1');
    		echo $select->assemble(); 
    		$contar = $OCW->getAdapter()->fetchOne($select);
    		if( $contar > 1 ){
	    		$ocwRow->ocwTitleEncode = codificar_titulo($ocwRow->ocwTitle . '_' . $ocwRow->id);
	    		d(codificar_titulo( $ocwRow->ocwTitle . '_' . $ocwRow->id ) );
    		}else{
    			$ocwRow->ocwTitleEncode = codificar_titulo($ocwRow->ocwTitle);
    			d(codificar_titulo( $ocwRow->ocwTitle ) );
    		}
    		$ocwRow->save();
    	}
        d('Listo');
    }
    public function encodeencodeAction()
    {
    	// action body
    	$this->_helper->viewRenderer->setNoRender();
    	$OCW = new Table_OCW();
    	foreach ($OCW->fetchAll( 'idType in( 1, 2, 3, 4, 6)' ) AS $ocwRow ) {
    		d($ocwRow->ocwTitleEncode);
    		$ocwTitle = $ocwRow->ocwTitleEncode;
    		$sql="SELECT count(ocwTitle) FROM OCW WHERE ocwTitleEncode ='$ocwTitle' group by ocwTitle HAVING count(ocwTitleEncode)>1";
    		$select = $OCW->getAdapter()->select()
				    		->from('OCW', array('count(ocwTitle)'))
				    		->where('ocwTitleEncode = ?', $ocwTitle )
				    		->group('ocwTitle')
				    		->having('count(ocwTitleEncode)>1');
    		$contar = $OCW->getAdapter()->fetchOne($select);
    		if( $contar > 1 ){
    			$ocwRow->ocwTitleEncode = codificar_titulo($ocwRow->ocwTitle . '_' . $ocwRow->id);
    			d(codificar_titulo( $ocwRow->ocwTitle . '_' . $ocwRow->id ) );
    		}else{
    			$ocwRow->ocwTitleEncode = codificar_titulo($ocwRow->ocwTitle);
    			d(codificar_titulo( $ocwRow->ocwTitle ) );
    		}
    		$ocwRow->save();
    	}
    	d('Listo');
    }
}