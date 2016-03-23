<?php
/**
 * Implementación de la capa servicios
 * @author damills
 *
 */
namespace Asf\Service;

abstract class Service implements IService
{
    protected $_options = array();

    public function __construct(array $options = array())
    {
        $this->_setOptions($options);
        $this->init();
    }

    /**
     * Constructs a Service instance
     * @param array $options
     * @return Asf_Service_Service
     */
    public static function factory(array $options = array())
    {
        $class = get_called_class();
        return new $class($options);
    }
	/**
	 * Si es necesario para configurar un servicio
	 * @param array $options
	 */
    protected function _setOptions(array $options)
    {
        $this->_options = $options;
    }
	/**
	 * Obtiene las opciones con que se configuró el servicio
	 * @see \Asf\Service\IService::getOptions()
	 */
    public function getOptions()
    {
        return $this->_options;
    }
	/**
	 * No usada
	 */
    protected function init()
    {
        
    }
	/**
	 * Retorna el tipo o nombre de clase instanciada como servicio
	 * @return string
	 */
    public static function getType()
    {
        return get_called_class();
    }
	/**
	 * Obtiene los métodos y parámetros que tiene el servicio instanciado
	 * @return array:
	 */
    public static function getMethods()
    {
    	$resultado = array();    	
    	$class = get_called_class();
        $metodos = get_class_methods($class);
        foreach ($metodos as $metodo) {
        	$r = new \ReflectionMethod($class, 'metodoDePrueba');
        	$params = $r->getParameters();        	
        	$resultado[$metodo]= array('parametros' => $params) ;
        }
        return $resultado ; 
    }
}

?>