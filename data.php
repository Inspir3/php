<?php

require_once "lib/log4php/Logger.php";

Logger::configure("log4php.xml");

$log = Logger::getLogger("data.php");

require_once "hebergeur.php";		//Quelques adaptations pour gérer les différentes versions de php selon les hébergeurs

/*
 * Lit un paramètre en GET ou en POST
 */
function lireRequest($Cle){
	if (array_key_exists($Cle, $_REQUEST)){
		return $_REQUEST[$Cle];
	}

	return "";
}

$APPLICATION	= lireRequest("application");
$ACTION				= lireRequest("action");
$FICHIER			= lireRequest("fichier");
$DONNEES			= lireRequest("donnees");
$CALLBACK			= lireRequest("callback");

$log->info("APPLICATION: $APPLICATION");
$log->info("ACTION: $ACTION");
$log->info("FICHIER: $FICHIER");
$log->info("DONNEES: $DONNEES");
$log->info("CALLBACK: $CALLBACK");

/*
 * Retourne des données au client au format JSON
 */
function retourneJSON($Donnees){
	global $log;
	global $CALLBACK;
	
	header("Content-type: application/json");

	$ret = "";
	
	if (isset($CALLBACK)){
		$ret = $CALLBACK . "(" . json_encode($Donnees) . ")";		//JSONP
	}else{
		$ret = json_encode($Donnees);														//JSON
	}
	
	$log->debug($ret);
	
	echo $ret;
	
	exit();
}

/*
 * Retourne une erreur 500 avec un message JSON
 */
function retourneErreur($Message){	
	http_response_code(500);
	retourneJSON($Message);
}

if ($APPLICATION == "") retourneErreur("Veuillez renseigner le paramètre application");
if ($ACTION == "") 			retourneErreur("Veuillez renseigner le paramètre action");
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
		
		if ($DONNEES == "") retourneErreur("Veuillez renseigner le paramètre donnees");
		
		file_put_contents($repertoire . $FICHIER . ".txt", $DONNEES);  	
		retourneJSON("ok");
	break;
}

retourneErreur("Aucune action effectuée");

?>
