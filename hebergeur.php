<?php

/*
 * Adapte le code PHP selon l'hébergeur
 */
$fai = file_get_contents("fai.txt");

$log->info("FAI: $fai");

switch($fai){

	case "local":
	break;

	case "free":
		require_once "lib/json.php";			//La fonction json_encode() ne fonctionne pas chez free, cette lib défini la fonction json_encode()

	case "ovh":
		/*
		 * La fonction http_response_code() ne fonctionne pas chez free
		 */
		function http_response_code($newcode = NULL){
			static $code = 200;
			if($newcode !== NULL){
				header('X-PHP-Response-Code: '.$newcode, true, $newcode);
				if(!headers_sent()){
					$code = $newcode;
				}
			}

			return $code;
		}
	break;

}

?>
