<?php
/**
 * Esta clase es un Helper que ayuda a renderizar el menu izquierdo de navegacion  
 * @package	library/Asf/View/Helper
 * @copyright	Aconcagua Software Factory 
 * @author	damills
 * @version	19/12/2012
 */
class Asf_View_Helper_Navbar extends Zend_View_Helper_Abstract
{
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
	public function navbar(){
		return $this;               
	}
        /**
         * Devuelve una instancia de un objeto menú para luego configurar un menú
         * @return \MenuHeader
         */
        public function createMenu($id = null){
            return new MenuLeft($id);
        }
        /**
         * Este método espera un menú configurado para renderizar
         * @param \MenuHeader $menu Intancia de la clase MenuHeader
         */
        public function setMenu($menu){
            $this->_menu = $menu;
        }
       /**
        * Funcion privada que imprime las listas que serán los menus
        */
       public function render(){
           /*
            * <div id="navbar">
            <ul id="menu" class="nav">
                <li class="dash"><a href="index.php" title="" class="active"><span>Obtener parámetros de intervención</span></a></li>
                <li class="charts"><a href="#" title=""><span>Opción 2</span></a></li>
                <li class="forms"><a href="#" title="" class="exp"><span>Opción 3</span></a>
                    <ul class="sub">
                        <li><a href="#" title="">Sub option 1</a></li>
                        <li><a href="#" title="">Sub option 2</a></li>
                        <li><a href="#" title="">Sub option 3</a></li>
                        <li class="last"><a href="#" title="">Sub option 4</a></li>
                    </ul>
                </li>
                <li class="ui"><a href="#" title=""><span>Opción 4</span></a></li>
                <li class="tables"><a href="#" title="" class="exp"><span>Opción 5</span></a>
                    <ul class="sub">
                        <li><a href="#" title="">Sub option 1</a></li>
                        <li><a href="#" title="">Sub option 2</a></li>
                        <li class="last"><a href="#" title="">Sub option 3</a></li>
                    </ul>
                </li>
                <li class="widgets"><a href="#" title="" class="exp"><span>Opción 6</span></a>
                    <ul class="sub">
                        <li><a href="#" title="">Sub option 1</a></li>
                        <li class="last"><a href="#" title="">Sub option 2</a></li>
                    </ul>
                </li>                
            </ul>    
        </div>
            */
           $menu_items = $this->_menu->getItems();
           $ulid = is_null($this->_menu->getId()) ? '' : 'id="'. $this->_menu->getId() .'"';
           echo '<div id="navbar">';
           echo '<ul '. $ulid .' class="nav">';
           foreach ($menu_items as $item) {
               $id = $item['id'] != '' ?  ' id="'. $item['id'] . '" ' : '';
               $onclick = $item['jsFunction'] !='' ? ' onclick="'. $item['jsFunction'] . '" ' : '';
               $href = $item['link'] != '' ? ' href="'. $item['link'] .'" ' : ''; 
               $liClass = $item['liClass'] != '' ? ' class="'.$item['liClass'] . '" ' : '';
               echo '<li ' . $id . $liClass . '>';
               echo '<a title="'. $item['atitle'] . '" ' . $onclick . $href .'>';
               echo '<span>' . $item['label'] . '</span>';
               echo '</a>';
               if(!is_null( $item['SubMenu'] ) ){
                   $subMenu = $item['SubMenu'];
                   $subMenuUlId = is_null($subMenu->getId()) ? '': 'id="'. $subMenu->getId() .'"';
                   echo '<ul '. $subMenuUlId .' class="sub">';
                   foreach($subMenu->getItems() as $sItem ){
                       $id = $sItem['id'] != '' ?  'id="'. $sItem['id'] . '"' : '';
                       $onclick = $sItem['jsFunction'] !='' ? ' onclick="'. $sItem['jsFunction'] . '" ' : '';
                       $href = $sItem['link'] != '' ? ' href="'. $sItem['link'] .'" ' : ''; 
                       echo '<li '. $id .' >';
                       echo '<a title="'. $sItem['atitle'].'" ' . $onclick . $href .'>';
                       echo $sItem['label'] .'</a></li>';
                   }
                   echo '</ul>';
               }
               echo "</li>";
           }
           echo '</ul>';
           echo '</div>';
       }    
}

/**
 * Clase auxiliar para generar el menú que luego el helper se encarga de renderizar
 * ver si hay que colocarla en otro lado para generalizar menues
 */
class MenuLeft
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
     * @param string $liClass Clase css que se le aplica al <span></span> generalmente para mostrar un icono
     * @param string $id Id del <li></li>
     * @param string $jsFunction Nombre de la función JS que se ejecuta al hacer click
     * @param string $link Se le aplica al elemento <a></a> propiedad href
     * @param object $subMenu Otra instancia de esta misma clase para construir un submenú
     */
    public function addItem( $aTitle ="" , $label="", $liClass="", $id="", $jsFunction="", $link='#', $subMenu = null ){
        $item = array(    'atitle'      => $aTitle
                        , 'label'       => $label
                        , 'liClass'     => $liClass 
                        , 'jsFunction'  => $jsFunction
                        , 'id'          => $id
                        , 'link'        => $link
                        , 'SubMenu'     => $subMenu
                    );
        $this->_items[] = $item;
    }   
}
