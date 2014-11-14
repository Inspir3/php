<?php

require_once "json.php5";			//La fonction json_encode() ne fonctionne pas chez free, cette lib défini la fonction __json_encode()

/*
* La fonction http_response_code() ne fonctionne pas chez free,
*/
function http_response_code($newcode = NULL){
	static $code = 200;
	if($newcode !== NULL)
	{
	    header('X-PHP-Response-Code: '.$newcode, true, $newcode);
	    if(!headers_sent())
	        $code = $newcode;
	}       
	return $code;
}
    
/*
 * Lit un paramètre en GET ou en POST
 */
function lireRequest($Cle){
    if (array_key_exists($Cle, $_REQUEST)){
      return $_REQUEST[$Cle];
    }
    
    return "";   
}

$APPLICATION    = lireRequest("application");
$ACTION         = lireRequest("action");
$FICHIER        = lireRequest("fichier");
$DONNEES        = lireRequest("donnees");

/*
 * Retourne des données au client au format JSON
 */
function retourneJSON($Donnees){
	header("Content-type: application/json");
		
	//echo __json_encode($Donnees);                                     //JSON simple
	
	echo $_GET['callback'] . "(" . __json_encode($Donnees) . ")";       //JSONP
	
	exit();
}

/*
 * Retourne une erreur 500 avec un message JSON
 */
function retourneErreur($Message){         
	http_response_code(500);
	retourneJSON($Message);
}	  

if ($ACTION == "") 			retourneErreur("Veuillez renseigner le paramètre action");                    
if ($APPLICATION == "") retourneErreur("Veuillez renseigner le paramètre application");
if ($FICHIER == "") 		retourneErreur("Veuillez renseigner le paramètre fichier");

$repertoire = "data/" . $APPLICATION . "/";

if (!file_exists($repertoire)) {
	mkdir($repertoire);								//Créé le répertoire de l'application si il n'existe pas (dans data/<application>/)
}
                              
switch(strtolower($ACTION)){

    case "charger":
        
        $contenu = "";
        $chemin = $repertoire . $FICHIER . ".txt";
        
        if (file_exists($chemin)){
            $contenu = file_get_contents($chemin);
        }
	
        retourneJSON($contenu);
    break;
    
    case "sauvegarder":		
  		file_put_contents($repertoire . $FICHIER . ".txt", $DONNEES);  	
		retourneJSON("ok");
		break;
}

retourneErreur("Aucune action effectuée");
                              
?>
