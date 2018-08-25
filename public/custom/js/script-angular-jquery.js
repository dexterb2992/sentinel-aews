function WpScanController($scope, $http, $timeout){
	$scope.wp_type = "";
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
		spinner.removeClass('fa-search').addClass('fa-spinner fa-spin');
		$scope.AjaxLoading = true;
		$scope.resultsVulnerabilities = []; // empty old results

		$http({
			method: 'POST',
			url: ajaxurl,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}, // converts payload data to form data
			data: data,
		}).then(function successCallback(response) {
		    // this callback will be called asynchronously
		    // when the response is available
		    spinner.removeClass('fa-spinner fa-spin').addClass('fa-search');
			$("#slug").val("");
			
			response = response.data;

			// lets convert the json object to json array
			var arr = $.map(response.response, function(el) { return el; });

			if (response.response.hasOwnProperty('error')) {
				$scope.themePluginError.msg = response.response.error;	
				$scope.displayError();

				$scope.AjaxLoading = false;
				$scope.wpScanForm.$setPristine();

				return;
			}

			if(!response.response.status){
				arr[0].vulnerabilities.name = slug;
				arr[0].vulnerabilities.url = response.detail;
				$scope.resultsVulnerabilities.push(arr[0].vulnerabilities);


				$scope.saveWPScan(slug, $scope.resultsVulnerabilities, type);

				if( arr[0].vulnerabilities.length < 1 ){
					$scope.themePluginError.msg = 'No data found for '+slug;
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

	/**
	 * Checks installed plugins/themes against WordPress vulnerabilites being pulled in
	 *
	 * @param String type            Either plugins or themes
	 * @param String tableToLookFor  The ID of the table to look for the match
	 * @param String tableToAppend   The ID of the table on where to append the results
	 */
	$scope.findInVulnerabilities = function (type, tableToLookFrom, tableToAppend) {
		let _source = window.WPSentinel.plugins,
		    _tbl = $("#"+tableToAppend),
		    _row = null;

		if (type == "themes") {
			_source = window.WPSentinel.plugins;
		}

		$.each(_source, function (key, item) {
			// look for the item on the table anchor tags
			$("#"+tableToLookFrom+" td>a").each(function (i, anchor){
			    if ($.trim($(anchor).text()) == item.slug) {
			       _row = $(anchor).parents("tr:first");

			       _tbl.find('tr[data-item="'+item.slug+'"] td:last')
			       	   .html(
			       	   		'<span class="label bg-yellow-gradient">'+_row.children("td:nth-child(2)").text()
			       	   		+'</span>'+_row.children("td:last").html()
			       	   	);

			       return false; // break the inner iteration
			    }
			});

		});
	};

	$scope.reScan = function (type, slug) {
		$("#wp_scan_modal").modal('show');
		$scope.wp_type = type.charAt(0).toUpperCase()+type.substr(1);
		$scope.slug = slug;
		$scope.startScan(type, slug);
	}

	$scope.getWpScans();
	$scope.findInVulnerabilities("plugins", "tbl_plugins");
	$scope.findInVulnerabilities("themes", "tbl_themes");

}

var app = angular.module('wpScan', []);
app.controller('wpScanCtrl', WpScanController);

