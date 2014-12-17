var m = angular.module('inspir3', []);

/*
 * Module Persistance
 */
m.factory('Persistance', ['$http', function($http) {

	var url = 'http://localhost/php/data.php?callback=JSON_CALLBACK';
        
  var application = 'test1';
  
  /*
   * Sauvegarde une structure complète JSON
   */    
  var sauvegarde = function(Fichier, Donnees, Callback) {
  
    $http.jsonp(url, { params: {
                            application: application, 
                            action: 'sauvegarde', 
                            fichier: Fichier,
                            donnees: Donnees
                        }
                     }).
        success(function() {
            Callback();
        }).
        error(function(Data) {
            console.log('[Erreur] ' + Data);
            Callback();
        });    
  }
  
  /*
   * Charge une structure complète JSON
   */    
  var chargement = function(Fichier, Callback) {
      
    $http.jsonp(url, { params: {
                                    application: application, 
                                    action: 'chargement', 
                                    fichier: Fichier 
                                    }
                                }).
        success(function(Data) {
            Callback(Data);
        }).
        error(function(Data) {
            console.log('[Erreur] ' + Data);
            Callback();
        });
  }
	
	/*
   *  Action unitaire sur un élément d'une structure JSON de type liste
   */    
  var unitaire = function(Action, Fichier, Donnees, Callback) {
  
    $http.jsonp(url, { params: {
                            application: application, 
                            action: Action, 
                            fichier: Fichier,
                            donnees: Donnees
                        }
                     }).
        success(function() {
            Callback();
        }).
        error(function(Data) {
            console.log('[Erreur] ' + Data);
            Callback();
        });
  }
  
	/*
   *  Ajoute un élément à une structure JSON de type liste
   */    
  var ajout = function(Fichier, Donnees, Callback) {
		unitaire('ajout', Fichier, Donnees, Callback);
  }
  
  /*
   *  Modifie un élément d'une structure JSON de type liste
   */    
  var modification = function(Fichier, Donnees, Callback) {
		unitaire('modification', Fichier, Donnees, Callback);    
  }  
	
	/*
   *  Supprime un élément d'une structure JSON de type liste
   */    
  var suppression = function(Fichier, Donnees, Callback) {
		unitaire('suppression', Fichier, Donnees, Callback);    
  }     

  return {
    sauvegarde: sauvegarde,
    chargement: chargement,
    ajout: ajout,
    modification: modification,
    suppression: suppression
  }
    
}]);

/*
 * Module Liste
 */
m.factory('Liste', function() {

  /*
   * Retourne un id disponible
   */
  var idSuivant = function(Liste){
      
    if (Liste.length == 0) return 1;
    
    return Liste[Liste.length-1].id + 1;        
  }
  
  /*
   * Retourne la position d'une dépense dans le tableau
   */
  var index = function(Liste, Id){
      
    for(var i=0;i<Liste.length;i++){
        if (Liste[i].id == Id){
            return i;
        }
    }
    
    return -1;        
  }
  
  /*
   * Indique si l'objet contient le tag
   */
  var objetContientCeTag = function(Objet, Tag){

    for(var i=0;i<Objet.tags.length;i++){
        if (Objet.tags[i] == Tag) return true;
    }
    
    return false;
  }
        
  /*
   * Indique si l'objet contient tous ces tags
   */
  var objetContientTousCesTags = function(Objet, Tags){
      
    var cpt = 0;
    
    for(var i=0;i<Tags.length;i++){
        if (objetContientCeTag(Objet, Tags[i])) cpt++;
    }
            
    return (cpt == Tags.length);
  }

  /*
   * Retourne la position d'une dépense dans le tableau
   */
  var filtreParTags = function(Liste, Tags){
      
    var ret = [];
    
    for(var i=0;i<Liste.length;i++){
        if (objetContientTousCesTags(Liste[i], Tags)){
            ret.push(Liste[i]);
        }
    }
    
    return ret;        
  }
  
  return {
    idSuivant: idSuivant,
    index: index,
    filtreParTags: filtreParTags
  }
    
});