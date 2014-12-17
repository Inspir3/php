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

$chemin = $repertoire . $FICHIER . ".txt";

/*
 * Charge le fichier de données JSON
 */
function charge(){
	global $chemin;
	global $log;
	
	$log->debug("charge()");
	
	$contenu = "";
		
	if (file_exists($chemin)){
		$contenu = file_get_contents($chemin);
	}
	
	return $contenu;
}

/*
 * Retourne un ID disponible dans la liste JSON
 */
function ID_disponible($Liste){
  global $log;
  
  $log->debug("ID_disponible()");
  
	$ret = 0;

	foreach($Liste as $obj){
		if ($obj->id > $ret) $ret = $obj->id;
	}
	
	$ret++;
	
	$log->debug("ID_disponible() -> $ret");
	
	return $ret;
}

/*
 * Supprime un objet de la liste, retourne la nouvelle liste
 */
function modification($Liste, $Obj){
  global $log;
  
  $log->debug("modification()");
  
	$ret = [];
	
	$inconnu = true;

	foreach($Liste as $obj){	
		if ($obj->id == $Obj->id){
			array_push($ret, $Obj);
			$inconnu = false;
		}else{
			array_push($ret, $obj);
		}	
	}
	
	if ($inconnu){												//Si l'objet n'existe pas
		$Obj->id = ID_disponible($Liste);		//On lui affecte un id disponible
		array_push($ret, $Obj);   					//et on l'ajoute à la fin de la liste
	} 
		
	return $ret;
}

/*
 * Supprime un objet de la liste, retourne la nouvelle liste
 */
function suppression($Liste, $Obj){
  global $log;
  
  $log->debug("suppression()");
  
	$ret = [];

	foreach($Liste as $obj){	
		if ($obj->id != $Obj->id) array_push($ret, $obj);	
	}
		
	return $ret;
}

switch(strtolower($ACTION)){

	case "chargement":
		retourneJSON(charge());	
	break;

	case "sauvegarde":
		
		if ($DONNEES == "") retourneErreur("Veuillez renseigner le paramètre donnees");
						
		file_put_contents($chemin, $DONNEES);  	
		retourneJSON("ok");
	break;
	
	case "ajout":
		
		if ($DONNEES == "") retourneErreur("Veuillez renseigner le paramètre donnees");
		
		$liste = json_decode(charge());						
		if ($liste == "") $liste = [];        	//Si la structure JSON n'existe pas encore, on la créé
		
		$obj = json_decode($DONNEES);						//On recupere l'objet à ajouter
		$obj->id = ID_disponible($liste);				//et on lui affecte un id disponible
		
		array_push($liste, $obj);		
				
		file_put_contents($chemin, json_encode($liste));  	
		retourneJSON("ok");
	break;
	
	case "modification":
		
		if ($DONNEES == "") retourneErreur("Veuillez renseigner le paramètre donnees");
		
		$liste = json_decode(charge());						
		if ($liste == "") $liste = [];        	//Si la structure JSON n'existe pas encore, on la créé
		
		$obj = json_decode($DONNEES);						//On recupere l'objet à modifier
		
		$liste = modification($liste, $obj);
						
		file_put_contents($chemin, json_encode($liste));  	
		retourneJSON("ok");
	break;
	
	case "suppression":
		
		if ($DONNEES == "") retourneErreur("Veuillez renseigner le paramètre donnees");
		
		$liste = json_decode(charge());						
		if ($liste == "") $liste = [];        	//Si la structure JSON n'existe pas encore, on la créé
		
		$obj = json_decode($DONNEES);						//On recupere l'objet à supprimer
		
		$liste = suppression($liste, $obj);     
						
		file_put_contents($chemin, json_encode($liste));  	
		retourneJSON("ok");
	break;
}

retourneErreur("Aucune action effectuée");

?>
