var m = angular.module('application', ['inspir3']);

m.controller('DepenseControleur', ['$scope', 'Persistance', function($scope, Persistance) {
    
    /*
     * Ajouter une dépense  
     */
    $scope.test = function() {
        console.log('test()');
        
        var json = angular.toJson({ prenom: 'regis', nom: 'baril' });
		
        Persistance.sauvegarder('data', json, function (){
					console.log('Données sauvées');                        
        });
    }       
    
}]);
