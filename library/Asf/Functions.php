<?php

/**
 * metodo abreviado para depurar facilmente
 * @param type $data
 */
function d($data)
{
    Zend_Debug::dump($data);
}

/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 * Elimina los espacios
 * @param string $string la cadena a sanear
 * @return string $string cadena saneada
 */
function formatear_string($string)
{
 
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "<", ";", ",", ":",
             ".", " "),
        '',
        $string
    );
 
 
    return $string;
}

/**
 * Le quita los caracteres especiales a una cadena
 * y reemplaza los espacios por guiones bajos
 * @param string $string
 * @return mixed
 */
function codificar_titulo($string)
{

	$string = trim($string);

	$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
	);

	$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
	);

	$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
	);

	$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
	);

	$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
	);

	$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
	);

	//Esta parte se encarga de eliminar cualquier caracter extraño
	$string = str_replace(
			array("\\", "¨", "º", "-", "~",
					"#", "@", "|", "!", "\"",
					"·", "$", "%", "&", "/",
					"(", ")", "?", "'", "¡",
					"¿", "[", "^", "`", "]",
					"+", "}", "{", "¨", "´",
					">", "<", ";", ",", ":",
					"."),
			'',
			$string
	);
	$string = str_replace(" ", "_", $string);


	return strtolower( $string );
}


function codificar_filename($string)
{
	$string = trim($string);

	$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
	);

	$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
	);

	$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
	);

	$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
	);

	$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
	);

	$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
	);

	//Esta parte se encarga de eliminar cualquier caracter extraño
	$string = str_replace(
			array("\\", "¨", "º", "-", "~",
					"#", "@", "|", "!", "\"",
					"·", "$", "%", "&", "/",
					"(", ")", "?", "'", "¡",
					"¿", "[", "^", "`", "]",
					"+", "}", "{", "¨", "´",
					">", "<", ";", ",", ":"),
			'',
			$string
	);
	$string = str_replace(" ", "_", $string);


	return strtolower( $string );
}
/**
 * ss (Sanitize String)
 * Abreviatura de filter_var con el filtro FILTER_SANITIZE_STRING
 * usado para no guardar ni mostrar código en la db
 * @param string $cadena string a sanear
 * @return string Cadena saneada
 */
function ss($cadena){
    try {
        return filter_var($cadena, FILTER_SANITIZE_STRING);
    } catch (Exception $exc) {
        throw new Exception('Error, caracteres especiales no pudieron ser convertidos');
    }

    
}
/**
 * Elimina ciertos atributos de un html
 * @example <p style="color:red">Text</p> to <p>Text</p> 
 * @param String $html
 * @param array $attrs
 */
function removeHtmlAttribute($html, $attrs=array('style','face') ){
	// de paso remuevo la posibilidad de insertar scripts
	$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
	foreach ($attrs as $attr ) {
		$html = preg_replace('/(<[^>]+) '.$attr.'=".*?"/i', '$1', $html);
	}
	return $html;
}
/**
 * Le quita la hora a una fecha (migrado del framework viejo)
 * @param string $data cadena que contenga una fecha
 * @return string
 */
function db2phpTime( $data )
{
        if( $data )
                return str_replace( " 00:00:00", "",  $data );
}

/**
 * Detecta si el browser es IE
 * @return boolean
 */
function isIE(){
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}
/**
 * Agrega puntos suspensivos a una cadena si excede el $max de caracteres permitidos
 * 
 * @param string $cadena 
 * @param integer $max numero maximo de caracteres
 * @return string si la $cadena no excede el tamaño maximo retorna la cadena entera, si no la cadena agregando los puntos suspensivos
 */
function recortar_cadena($cadena, $max){
    if(strlen($cadena) > $max ){
        return substr($cadena, 0, $max) . '...';
    } else {
        return $cadena;
    } 
}
/**
 * 
 * @param string $string
 * @param string $limit cantidad de caracteres
 * @param string $break caracter de corte, generalmente espacios 
 * @param string $pad lo que se agrega al final
 * @return string
 */
function truncateString($string, $limit, $break=" ", $pad="...") {
	// return with no change if string is shorter than $limit
	if(strlen($string) <= $limit)
		return $string;
	// is $break present between $limit and the end of the string?
	if(false !== ($breakpoint = strpos($string, $break, $limit))) {
		if($breakpoint < strlen($string) - 1) {
			$string = substr($string, 0, $breakpoint) . $pad;
		}
	}
	return $string;
} 
function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
/**
 * Imprime el Json para alimentar a jqueryDatatables
 * @param array $dataCursor espera un array con tres claves "cursor", "count", "countWhere"
 */
function printJsonGrid( $dataCursor, $sEcho ){
	$i = 0;
	$cursor = $dataCursor['cursor'];
	$totalRegistros = $dataCursor['count'];
	$totalRegistrosWere = $dataCursor['countWhere'];

	echo '{"aaData": [';
	while ($row = $cursor->fetch(Zend_Db::FETCH_NUM)) {

		if ($i <= 5000) {
			if ($i > 0) {
				echo ",";
			}
			$json = '{"DT_RowId": "'.$row[0].'", ';
			$PK = array_shift($row); // se asume que la primer fila tiene PK
			//$json = Zend_Json_Encoder::encode($row);
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
 * Método para construcción del where para filtrar la grilla de ocw
 * @param Array $filters
 */
function buildWhere( $filters = array() ){
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
				$operador = '= ';
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
				// verificar si el valor es una fecha y aplicar al campo DATE_FORMAT para que la comparacion funcione
				$patron = "/[0-9]{2}-[0-9]{2}-[0-9]{4}$/";
				$campo 	= preg_match($patron, $valores) ? "DATE_FORMAT($campo, '%Y-%m-%d')": $campo;
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

?>