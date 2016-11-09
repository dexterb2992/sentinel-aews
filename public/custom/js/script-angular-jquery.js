function WpScanController($scope, $http, $timeout){
	$scope.wpTypeOptions = ['Plugins', 'Themes'];
	$scope.AjaxLoading = false;
	$scope.wpscan = {}; // empty wpscan object
	$scope.wpScans = []; // list of wp scans
	$scope.resultsVulnerabilities = [];
	$scope.themePluginError = { 'msg' : '', 'status' : false };
	$scope.wpScansListing = [];

	$scope.displayError = function(){
    	$scope.themePluginError.status = true;
	    $timeout(function() {
	        $scope.themePluginError = { msg: '', status: false };
	    }, 5000);
    }; 

    $scope.saveWPScan = function (url, wpScanResult, wpType){
    	var $_postData = {
			q: 'save_scan',
			url: url,
			scan_result: JSON.stringify(wpScanResult),
			is_wp_scan: true,
			wp_type: wpType
		};

		$http({
			url: ajaxurl,
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			data: $.param($_postData)
		}).then(function successCallback(response){
			console.log(response.data);
		}, function errorCallback(){

		});
    };

	$scope.startScan = function (type, slug){
		type = angular.lowercase(type);
		slug = angular.lowercase(slug);
		
		var src = type == 'plugin' ? $scope.pluginsApi : $scope.themesApi;
		var data = $.param({
            type: type, 
            slug: slug, 
            q: 'wp_scan'
        });

		var spinner = $("#start_scan_loader");
		spinner.removeClass('fa-search').addClass('fa-refresh fa-spin');
		$scope.AjaxLoading = true;

		$http({
			method: 'POST',
			url: ajaxurl,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}, // converts payload data to form data
			data: data,
		}).then(function successCallback(response) {
		    // this callback will be called asynchronously
		    // when the response is available
		    spinner.removeClass('fa-refresh fa-spin').addClass('fa-search');
			$("#slug").val("");
			
			response = response.data;

			// lets conver the json object to json array
			var arr = $.map(response.response, function(el) { return el; });

			if(!response.response.status){
				$scope.resultsVulnerabilities = []; // empty old results
				arr[0].vulnerabilities.name = slug;
				arr[0].vulnerabilities.url = response.detail;
				$scope.resultsVulnerabilities.push(arr[0].vulnerabilities);


				$scope.saveWPScan(slug, $scope.resultsVulnerabilities, type);

				if( arr[0].vulnerabilities.length < 1 ){
					$scope.themePluginError.msg = 'No data found for <strong>'+slug+'</strong>.';	
					$scope.displayError();				
				}
			}else{
				// the server returns 404
				$scope.themePluginError.msg = response.response.statusText;	
				$scope.displayError();
			}

			$scope.AjaxLoading = false;
			$scope.wpScanForm.$setPristine();
		  }, function errorCallback(response) {
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
				$scope.themePluginError.msg = "Something went wrong while processing your request.";
				$scope.displayError();	
		  });

	};


	$scope.getWpScans = function (){
		$http({
			method: 'POST',
			url: ajaxurl,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			data: $.param({q: 'get_wp_scans'})
		}).then(function successCallback(response){
			var results = response.data;
			if( results !== "" && results !== null){
				$scope.wpScansListing = results;

				console.log($scope.wpScansListing);
			}

		}, function errorCallback(response){
			console.log(response);
		});
	};

	$scope.getWpScans();

	$scope.showPreviousScans = function (){
		console.log("showing previous scan...");
		$scope.getWpScans();
		$("#wp_scans_modal").modal('show');
	};

	$scope.deleteScan = function (index){
		var confirmation = confirm("Are you sure you want to delete this scan?");
		var selectedScan = $scope.wpScansListing[index];
		if( confirmation ){
			$http({
				url: ajaxurl,
				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				data: $.param({
					q: 'delete_scan',
	                id: selectedScan.id
				})
			}).then(function successCallback(response){
				if( response.data == 1 ){
					$scope.wpScansListing.splice(index);
				}

			}, function errorCallback(response){
				console.log(response);
			});
		}
	};

}

var app = angular.module('wpScan', []);
app.controller('wpScanCtrl', WpScanController);

