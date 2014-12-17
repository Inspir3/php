var m = angular.module('application', ['inspir3']);

m.controller('DepenseControleur', ['$scope', 'Persistance', function($scope, Persistance) {
    
    /*
     * Test un chargement  
     */
    $scope.chargement = function() {
    	console.log('chargement...');
    	
			Persistance.chargement('data', function (Data){				                        
				console.log(Data);
				console.log('chargement... OK');
      });
    }
    
    /*
     * Test une sauvegarde  
     */
    $scope.sauvegarde = function() {
    	console.log('sauvegarde...');
    	
    	var utilisateurs = [];
        
      utilisateurs.push({ id: 1, prenom: 'regis', nom: 'baril' });
      utilisateurs.push({ id: 2, prenom: 'julien', nom: 'cuillandre' });
        		
      Persistance.sauvegarde('data', angular.toJson(utilisateurs), function (){
				console.log('sauvegarde... OK');                        
      });
    }
		
		/*
     * Test un ajout  
     */
    $scope.ajout = function() {
			console.log('ajout...');
                		
      Persistance.ajout('data', angular.toJson({ id: 0, prenom: 'andre', nom: 'baril' }), function (){
				console.log('ajout... OK');                        
      });
    }       
    
    /*
     * Test une modification  
     */
    $scope.modification = function() {
			console.log('modification...');
			
			Persistance.modification('data', angular.toJson({ id: 3, prenom: 'julien', nom: 'cuillandre' }), function (){
					console.log('modification... OK');                        
			});
			
		}
		
		/*
     * Test une suppression  
     */
    $scope.suppression = function() {
			console.log('suppression...');
			
			Persistance.suppression('data', angular.toJson({ id: 2, prenom: 'andre', nom: 'baril' }), function (){
					console.log('suppression... OK');                        
			});
		}
    
}]);
