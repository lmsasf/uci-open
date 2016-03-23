<?php
/**
 * Esta clase es un Helper que ayuda a renderizar los encabezados y menús del header
 * @package	library/Asf/View/Helper
 * @copyright	Aconcagua Software Factory 
 * @author	damills
 * @version	03/12/2012
 */
class Asf_View_Helper_Header extends Zend_View_Helper_Abstract
{
   
	/**
         * Título principal
         * @access private
         * @var string
         */
        private $_title = '';
        /**
         * Subtítulo 
         * @access private
         * @var string 
         */
        private $_subtitle = '';
        /**
         * Objeto menú
         * @access private
         * @var \MenuHeader 
         */
        private $_menu = null;
        
        /**
         * Devuelve una instancia de esta clase, simplemente es para ordenar el código 
         * y la forma de usarlo
         * @return \Asf_View_Helper_Header
         */
	public function header(){
		return $this;               
	}
        /**
         * Configura el título y subtitulo
         * @param string $title Título de la pantalla
         * @param string $subtitle Subtitulo de la pantalla
         */
        public function setTitle($title, $subtitle= ''){
            $this->_title = $title;
            $this->_subtitle = $subtitle;
        }
        /**
         * Devuelve una instancia de un objeto menú para luego configurar un menú
         * @return \MenuHeader
         */
        public function createMenu($id = null){
            return new MenuHeader($id);
        }
        /**
         * Este método espera un menú configurado para renderizar
         * @param \MenuHeader $menu Intancia de la clase MenuHeader
         */
        public function setMenu($menu){
            $this->_menu = $menu;
        }
        /**
         * Renderiza todo el encabezado, incluyendo menú si estuviese configurado
         */
        public function render(){
            try {
                echo '<div class="topHeader" ><div class="ToptitleArea"><div class="wrapper">';
                echo '<div class="TopPageTitle"><h5>'. $this->_title. '</h5><span>'. $this->_subtitle .'</span></div>';
                if(!is_null($this->_menu)){
                    $cItems = count($this->_menu->getItems()) * 64;
                    $width = $cItems > 0 ? $cItems : 64 ; // si por alguna razon no hay items
                    echo '<div class="middleNav noprint" style="width:'.$width.'px">';
                    $this->renderMenu();
                    echo '<div class="clear"></div></div>';
                }
                echo '<div class="clear"></div></div></div></div>';
                
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
       }
       /**
        * Funcion privada que imprime las listas que serán los menus
        */
       private function renderMenu(){
           $menu_items = $this->_menu->getItems();
           $ulid = is_null($this->_menu->getId()) ? '' : 'id="'. $this->_menu->getId() .'"';
           echo '<ul '. $ulid .' >';
           foreach ($menu_items as $item) {
               $id = $item['id'] != '' ?  ' id="'. $item['id'] . '" ' : '';
               $onclick = $item['jsFunction'] !='' ? ' onclick="'. $item['jsFunction'] . '" ' : '';
               $href = $item['link'] != '' ? ' href="'. $item['link'] .'" ' : ''; 
               echo '<li ' . $id .'>';
               echo '<a class="tipN" title="'. $item['atitle'] . '" ' . $onclick . $href .'>';
               echo '<span class="' . $item['spanClass'] . '">' . $item['label'] . '</span>';
               echo '</a>';
               if(!is_null( $item['SubMenu'] ) ){
                   $subMenu = $item['SubMenu'];
                   $subMenuUlId = is_null($subMenu->getId()) ? '': 'id="'. $subMenu->getId() .'"';
                   echo '<ul '. $subMenuUlId .' >';
                   foreach($subMenu->getItems() as $sItem ){
                       $id = $sItem['id'] != '' ?  'id="'. $sItem['id'] . '"' : '';
                       $span = $sItem['spanClass'] != '' ? '<span class="' .$sItem['spanClass'] . '"></span>': '';
                       $onclick = $sItem['jsFunction'] !='' ? ' onclick="'. $sItem['jsFunction'] . '" ' : '';
                       $href = $sItem['link'] != '' ? ' href="'. $sItem['link'] .'" ' : ''; 
                       echo '<li '. $id .' >';
                       echo '<a title="'. $sItem['atitle'].'" ' . $onclick . $href .'>';
                       echo $sItem['label'] . $span .'</a></li>';
                       //echo '<li><a href="#" title="">Pending tickets<span class="numberRight">12</span></a></li>';
                   }
                   echo '</ul>';
               }
               echo "</li>";
           }
           echo '</ul>';
       }
}
/**
 * Clase auxiliar para generar el menú que luego el helper se encarga de renderizar
 * ver si hay que colocarla en otro lado para generalizar menues
 */
class MenuHeader
{
    private $_items = array();
    private $_id = null;
    
    public function __construct($id = null) {
        $this->_id = $id;
    }    
    public function getItems(){
        return $this->_items;
    }
    public function getId(){
        return $this->_id;
    }
    /**
     * Añade un item al menú
     * @param string $aTitle Titulo que se le aplica al elemento <a></a>
     * @param string $label Texto que se impime 
     * @param string $spanClass Clase css que se le aplica al <span></span> generalmente para mostrar un icono
     * @param string $id Id del <li></li>
     * @param string $jsFunction Nombre de la función JS que se ejecuta al hacer click
     * @param string $link Se le aplica al elemento <a></a> propiedad href
     * @param object $subMenu Otra instancia de esta misma clase para construir un submenú
     */
    public function addItem( $aTitle ="" , $label="", $spanClass="", $id="", $jsFunction="", $link='#', $subMenu = null ){
        $item = array(    'atitle'      => $aTitle
                        , 'label'       => $label
                        , 'spanClass'   => $spanClass 
                        , 'jsFunction'  => $jsFunction
                        , 'id'          => $id
                        , 'link'        => $link
                        , 'SubMenu'     => $subMenu
                    );
        $this->_items[] = $item;
    }
    public function addToogleMenu(){
        $item = array(    'atitle'      => 'Mostrar / Ocultar men&uacute; de navegaci&oacute;n'
                , 'label'       => ''
                , 'spanClass'   => 'playlist' 
                , 'jsFunction'  => ''
                , 'id'          => 'navbarToogle'
                , 'link'        => ''
                , 'SubMenu'     => null
            );
        $this->_items[] = $item;
    }
    
}