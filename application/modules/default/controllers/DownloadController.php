<?php 
class Default_DownloadController extends Zend_Controller_Action
{
	private $ocwTitleEncode = null;
	
	public function init()
	{
		$this->ocwTitleEncode = $this->getRequest()->getParam('id', null);
		
		if ( is_null($this->ocwTitleEncode) ){
			throw new Exception('Invalid download file');
		}
	}

	public function indexAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		// obtener el OCW
		$OCW = new Entity_OCW($this->ocwTitleEncode);
		// verificar si el paquete existe y devolverlo
		$root = $_SERVER['DOCUMENT_ROOT'];
		$packageName= strtolower($OCW->typeName) . '_' . $OCW->titleEncode.".imscc";
		$zipFile = $root.'/packages/'.$packageName;
		if( file_exists($zipFile) ){
			$this->sendheaders($zipFile);
		}
		// Crear la carpeta temporal que luego se va a comprimir 
		$tmpFolder = APPLICATION_PATH . '/tmp/';
		$newFolder = $tmpFolder. $OCW->titleEncode;
		
		
		if(is_dir($newFolder)){ // la carpeta existe, hay que eliminarla
			$this->rrmdir($newFolder);
		}
		$result = mkdir($newFolder);
		$newFolder .= '/'.  $OCW->titleEncode;
		//d($newFolder); exit();
		$result = mkdir($newFolder);
		if( $result ){ // se creo con exito
			// Obtener y copiar los html a la carpeta
			// Copiar la carpeta frontend que tiene todas las imágenes
			$source = $root . '/frontend';
			
			mkdir($newFolder .'/frontend');
			$this->full_copy($source, $newFolder . '/frontend' );
			
			// Obtengo los archivos de la carpeta frontend
			$dir =array();
			$this->listDir($source, $dir); // ojo! dir esta pasado por referencia, ver la función mas abajo
			
			// obtener y copiar el Index del curso
			$host = $_SERVER['HTTP_HOST'];
			$resource = strtolower($OCW->typeName). 's/'. $OCW->titleEncode . '.html'; 
			$index = file_get_contents('http://'. $host .'/'. $resource );
			$index = str_replace('/frontend', 'frontend', $index);
			$index = str_replace('/courses', 'courses', $index);
			$index = str_replace('/collections', 'collections', $index);
			$index = str_replace('/conferences', 'conferences', $index);
			$index = str_replace('/lectures', 'lectures', $index);
			$fp=fopen($newFolder.'/index.html',"x");
			fwrite($fp,$index);
			fclose($fp) ;
			// Obtener los Joins, crear la carpeta y obtener los archivos
			foreach ($OCW->joins as $jocw ){
				if($jocw->type != 5 && $jocw->type !=7 ) {
					$titleEncode = is_null($jocw->titleEncode) ? codificar_titulo($jocw->title) : $jocw->titleEncode;
					$target = '';
					// verifico si la carpeta del Join existe y si no la creo
					$typeDir = strtolower($jocw->typeName).'s';
					if(!is_dir($newFolder. '/'. $typeDir)){ // no esta creada, la creo
						mkdir($newFolder. '/'. $typeDir);
					}
					switch ($jocw->type) {
						case 1: // courses
							$target = "http://" . $host . '/courses/' . $titleEncode.'.html';
							break;
						case 2: // files
							$target = $jocw->ownData['ocwUrlFile'];
							break;
						case 3: // lectures
							$target = "http://" . $host . '/lectures/' . $titleEncode.'.html';
							break;
						case 4: // Collections
							$target = "http://" . $host . '/collections/' . $titleEncode.'.html';
							break;
						case 6: // Conferences
							$target = "http://" . $host . '/conferences/' . $titleEncode.'.html';
							break;
						default:
							;
							break;
					}
					
					// si es File hay que obtener el nombre del archivo
					if($jocw->type == 2){
						if($jocw->isAlive($target)){
							$resource = @file_get_contents($target);
							$fileName = $this->getRealFileName($target);
							$fp = fopen($newFolder . '/' . $typeDir . '/' . $fileName,"x");
						}
					}else{
						$resource = @file_get_contents($target);
						$resource = str_replace('/frontend', '../frontend', $resource);
						$resource = str_replace('/courses', '../courses', $resource);
						$resource = str_replace('/collections', '../collections', $resource);
						$resource = str_replace('/conferences', '../conferences', $resource);
						$resource = str_replace('/lectures', '../lectures', $resource);
						$fp = fopen($newFolder . '/' . $typeDir . '/' . $titleEncode.'.html',"x");
					}
					fwrite( $fp, $resource );
					fclose($fp) ;
				}				
			}
			// generar el manifest y copiarlo a la carpeta
			$manifest = $this->view->partial("download/imsmanifest.phtml", array( 'ocw'=> $OCW, 'harcodeddir'=> $dir, 'tmpFolder'=> $tmpFolder ));
			$fp=fopen($tmpFolder. $OCW->titleEncode.'/imsmanifest.xml',"x");
			fwrite($fp,$manifest);
			fclose($fp) ;
			// comprimir
			//$packageName= strtolower($OCW->typeName) . '_' . $OCW->titleEncode.".imscc";
			$result = Asf_Zip::zipDir($tmpFolder . $OCW->titleEncode , 'packages/'.$packageName);
			// devolver el File
			//$zipFile = $root.'/packages/'.$packageName;
			$this->sendheaders($zipFile);
		} else {
			throw new Exception('Could not create directory');
		}
	}
	
	/**
	 * @param sting $zipFile Path del archivo comprimido
	 */
	private function sendheaders($zipFile){
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $zipFile);
		$size = filesize($zipFile);
		$name = basename($zipFile);
			
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			// cache settings for IE6 on HTTPS
			header('Cache-Control: max-age=120');
			header('Pragma: public');
		} else {
			header('Cache-Control: private, max-age=120, must-revalidate');
			header("Pragma: no-cache");
		}
		header("Content-Type: $mimeType");
		header('Content-Disposition: attachment; filename="' . $name . '";');
		header("Accept-Ranges: bytes");
		header('Content-Length: ' . filesize($zipFile));
		print readfile($zipFile);
		exit;
	}
	
	/**
	 * Remueve recursivamente un directorio
	 * @param String $dir
	 */
	private function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	/**
	 * Obtiene el nombre real de un archivo a través de su URL
	 * @param String $url
	 **/
	private function getRealFileName($url){
		$content = get_headers($url,1);
		$content = array_change_key_case($content, CASE_LOWER);
		
		// by header
		if (isset($content['content-disposition'])) {
			$tmp_name = explode('=', $content['content-disposition']);
			if ($tmp_name[1]) $realfilename = trim($tmp_name[1],'";\'');
		} else
		// by URL Basename
		{
			$stripped_url = preg_replace('/\\?.*/', '', $url);
			$realfilename = basename($stripped_url);
		
		}
		return $realfilename;
	}

	
	/**
	 * Copia un directorio completo
	 * @param unknown $source
	 * @param unknown $target
	 */
	private function full_copy( $source, $target ) {
	    if ( is_dir( $source ) ) {
	        @mkdir( $target );
	        $d = dir( $source );
	        while ( FALSE !== ( $entry = $d->read() ) ) {
	            if ( $entry == '.' || $entry == '..' ) {
	                continue;
	            }
	            $Entry = $source . '/' . $entry; 
	            if ( is_dir( $Entry ) ) {
	                $this->full_copy( $Entry, $target . '/' . $entry );
	                continue;
	            }
	            copy( $Entry, $target . '/' . $entry );
	        }
	 
	        $d->close();
	    }else {
	        copy( $source, $target );
	    }
	}
	

	/**
	 * Recorre un directorio y devuelve un array con todos los archivos y su ruta relativa
	 * @param String $ruta
	 * @throws Exception
	 * @return array
	 */
	private function listDir($ruta, &$arrayRet)
	{
		// comprobamos si lo que nos pasan es un direcotrio
		if (is_dir($ruta)){
			// Abrimos el directorio y comprobamos que
			if ($aux = opendir($ruta)){
				while (($archivo = readdir($aux)) !== false){
					// Si quisieramos mostrar todo el contenido del directorio pondríamos lo siguiente:
					// echo '<br />' . $file . '<br />';
					// Pero como lo que queremos es mostrar todos los archivos excepto "." y ".."
					if ($archivo!="." && $archivo!=".."){
						$ruta_completa = $ruta . '/' . $archivo;
						// Comprobamos si la ruta más file es un directorio (es decir, que file es
						// un directorio), y si lo es, decimos que es un directorio y volvemos a
						// llamar a la función de manera recursiva.
						if ( is_dir($ruta_completa) )	{
							$this->listDir($ruta_completa , $arrayRet);
						}else{
							$ruta_completa = str_replace($_SERVER['DOCUMENT_ROOT'], '', $ruta_completa);
							$arrayRet[] = $ruta_completa;
						}
					}
				}
				closedir($aux);
			}
			
		}else{
			throw new Exception('Ruta no válida');
		}
	}	
	
}
	