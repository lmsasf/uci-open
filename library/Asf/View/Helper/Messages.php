<?php
/**
 * Esta clase es un Helper que muestra los cuadros de mensajes estandar
 * @package	library/Asf/View/Helper
 * @copyright	Aconcagua Software Factory 
 * @author	damills
 * @version	03/12/2012
 */
class Asf_View_Helper_Messages extends Zend_View_Helper_Abstract
{
    	public function messages(){
		return $this;               
	}
        
        public function info( $frase, $prefrase = 'INFORMACI&Oacute;N' ){
            $this->render($frase, $prefrase, 'nInformation');
        }
        public function success( $frase, $prefrase = 'INFORMACI&Oacute;N' ){
            $this->render($frase, $prefrase, 'nSuccess');
        }
        public function error( $frase, $prefrase = 'ERROR' ){
            $this->render($frase, $prefrase, 'nFailure');
        }
        public function warning( $frase, $prefrase = 'CUIDADO' ){
            $this->render($frase, $prefrase, 'nWarning');
        }      
        public function idea( $frase, $prefrase = 'IDEA' ){
            $this->render($frase, $prefrase, 'nLightbulb');
        } 
        public function email( $frase, $prefrase = 'EMAIL' ){
            $this->render($frase, $prefrase, 'nMessages');
        }             
        private function render( $frase, $prefrase, $class){
            echo '<div class="nNote '. $class .' hideit">';
            echo '<p><strong>';
            echo $prefrase .': ';
            echo '</strong>';
            echo $frase;
            echo '</p>';
            echo '</div>';            
        }
}