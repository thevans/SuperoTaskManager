var app = angular.module('AppTaskManager', ['ngRoute']);

app.factory("services", ['$http', function($http, $route) {
	var serviceBase = 'service/'
    var obj = {};
    
    obj.getTarefas = function() {
        return $http.get(serviceBase + 'getAllTasks');
    }
    
    obj.getTarefa = function(tarefa_id){
        return $http.get(serviceBase + 'getTask?id=' + tarefa_id);
    }

    obj.inserirTarefa = function (tarefa) {
    	return $http.post(serviceBase + 'insertTask', tarefa).then(function (results) {
        	return results;
    	});
	};

	obj.alterarTarefa = function (id, tarefa) {
	    return $http.post(serviceBase + 'editTask', {tarefa_id:id, tarefa:tarefa}).then(function (status) {
	        return status.data;
	    });
	};

	obj.apagarTarefa = function (id) {
	    return $http.delete(serviceBase + 'deleteTask?id=' + id).then(function (status) {
	        return status.data;
	    });
	};

    return obj; 
}]);

app.controller('listCtrl', function ($scope, services) {
    services.getTarefas().then(function(data) {
        $scope.tarefas = data.data;
    });
});

app.controller('editCtrl', function ($scope, $rootScope, $location, $routeParams, services, tarefa) {
    var tarefa_id = ($routeParams.tarefa_id) ? parseInt($routeParams.tarefa_id) : 0;
    
    $rootScope.title = (tarefa_id > 0) ? 'Editar Tarefa' : 'Adicionar Tarefa';
    $scope.buttonText = (tarefa_id > 0) ? 'Salvar Tarefa' : 'Adicionar Tarefa';
      
	var original = tarefa.data;
    original.tarefa_id = tarefa_id;
    $scope.tarefa = angular.copy(original);
    $scope.tarefa.tarefa_id = tarefa_id;

    $scope.isClean = function() {
    	return angular.equals(original, $scope.tarefas);
	};
	
	$scope.apagarTarefa = function(tarefa) {
		$location.path('/');
        if (confirm("Tem certeza que deseja apagar a tarefa: " + tarefa.tarefa_titulo + "?") == true) {
        	services.apagarTarefa(tarefa.tarefa_id);
       }
	};
	
    $scope.salvarTarefa = function(tarefa) {
    	$location.path('/');
        
        if (tarefa_id <= 0) {
            services.inserirTarefa(tarefa);
        }
        else {
            services.alterarTarefa(tarefa_id, tarefa);
        }
    };
});

app.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/', {
        title: 'Tarefas',
        templateUrl: 'paginas/tarefas.html',
        controller: 'listCtrl'
      })
      .when('/editar-tarefa/:tarefa_id', {
        title: 'Editar Tarefa',
        templateUrl: 'paginas/editar_tarefa.html',
        controller: 'editCtrl',
        resolve: {
          tarefa: function(services, $route){
            var tarefa_id = $route.current.params.tarefa_id;
            return services.getTarefa(tarefa_id);
          }
        }
      })
      .otherwise({
        redirectTo: '/'
      });
}]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
        $rootScope.title = current.$$route.title;
    });
}]);