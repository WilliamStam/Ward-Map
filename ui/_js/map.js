

var markers = [];
function getWard() {

	var ward = $.bbq.getState("ward");


	
	

	if (ward){
		$("#page-area").fadeIn(500);
		$("#page-area-loading").show();
		$("#page-area-content").hide();
		$.getData("/data/ward/"+ward,function(data_ward){
			//$("#page-area").fadeIn(500);

			$('#rollerCoaster').jqotesub($("#template-roundabout-item"), data_ward);
			$('#ward-results-area').jqotesub($("#template-results"), data_ward);

			$("#right-thumbs ul").jqotesub($("#template-thumbs-item"), data_ward);
			$("#page-area-loading").hide();
			$("#page-area-content").show();

			document.title = default_title_prefix+data_ward.desc;
			
			if (data_ward.councillors.length==0){
				$("#details-pane").css({"opacity": 0, "left": "-200px"});
				$(".nextprev-btn").hide();
			} else {
				$(".nextprev-btn").show();
			}
			
			roundable();
			//getCouncilor();
			$(".data-ward-name").html(data_ward.desc)
			$(".data-candidate-count").html("(" + data_ward.councillors.length + ")")
		},"ward");
	} else {
		document.title = default_title;
	}
}


function getCouncilor() {
	var sub = $.bbq.getState("sub");

	
	//console.log(sub); 
	

	if (sub){
		$("#details-pane").html("<div class='loading small'></div>").stop(true, true).animate({"opacity": 1, "left": "40px"}, 200, function () {});
		$.getData("/data/councillor/"+sub,function(data){
			$("#details-pane").jqotesub($("#template-roundabout-item-details"), data);
		},"councilor");
	} else {
		
		$("#details-pane").stop(true, true).animate({"opacity": 0, "left": "-200px"}, 300, function () {});
	}
}


function roundable(sub) {
	
	sub = $.bbq.getState("sub");
	sub = sub ? sub : "";

	var startingChild = 0;
	$("#right-thumbs ul li").each(function(i,v){
		//console.log(i); 
		if ($(this).attr("data-id")==sub){
			startingChild = i;
		}
	});
	
	//console.info(sub);
	//console.info(startingChild);
	
	
	$('#rollerCoaster').roundabout({
		shape             : 'lazySusan',
		minOpacity        : 0.3,
		minScale          : 0.2,
		responsive        : true,
		btnNext           : ".nextprev-btn.next",
		btnPrev           : ".nextprev-btn.prev",
		triggerBlurEvents : true,
		triggerFocusEvents: true,
		startingChild     : startingChild

	});

}





$(document).ready(function () {
	getWard();
	
	
	$(document).on("roundfocus", "#rollerCoaster li", function () {
		//console.log("focus");

		var sub = $(this).attr("data-id");

		$.bbq.pushState({"sub": sub});
		getCouncilor();

		$("#right-thumbs").stop(true, true).animate({"opacity": 1, "right": "50px"}, 200, function () {});
		$("#right-thumbs ul li[data-id='"+sub+"']").addClass("active");
		

	});
	$(document).on("roundblur", "#rollerCoaster li", function () {
		//console.log("blur");
		$("#details-pane").animate({"opacity": 0, "left": "-200px"}, 300, function () {});
		$("#right-thumbs").animate({"opacity": 0, "right": "-200px"}, 300, function () {});
		$("#right-thumbs ul li.active").removeClass("active");

	});
	$(document).on("click", ".close-pages", function () {
		$.bbq.removeState("sub");
		$.bbq.removeState("ward");
		document.title = default_title;
		$("#page-area").fadeOut(500);

	});

	$(document).on("click", "#right-thumbs ul li", function () {
		var $this = $(this);
		var id = $this.attr("data-id");
		var i = $this.attr("data-i");


		$('#rollerCoaster').roundabout("animateToChild", i)
		
		
		

	});
	
	
	$(document).on("shown", "#nav-top", function () {
		$('#rollerCoaster').roundabout("relayoutChildren");

	});


	$(document).on("click", "#nav-admin-add", function () {
		

	});
	


	google.maps.event.addDomListener(window, 'load', initialize);

});


function initialize() {

	var markers = [];
	var map = new google.maps.Map(document.getElementById('map-canvas'), {
		zoom     : 8,
		center   : new google.maps.LatLng(-23.0462413, 29.904656199999977),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});



	// Create the search box and link it to the UI element.
	var input = /** @type {HTMLInputElement} */(
		document.getElementById('searchbox')
		);
	input.style.marginTop = '10px';


	map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);




	var searchBox = new google.maps.places.SearchBox(/** @type {HTMLInputElement} */(input));

	// [START region_getplaces]
	// Listen for the event fired when the user selects an item from the
	// pick list. Retrieve the matching places for that item.
	google.maps.event.addListener(searchBox, 'places_changed', function () {
		var places = searchBox.getPlaces();

		for (var i = 0, marker; marker = markers[i]; i++) {
			marker.setMap(null);
		}

		// For each place, get the icon, place name, and location.
		markers = [];
		var bounds = new google.maps.LatLngBounds();
		for (var i = 0, place; place = places[i]; i++) {
			var image = {
				url       : place.icon,
				size      : new google.maps.Size(71, 71),
				origin    : new google.maps.Point(0, 0),
				anchor    : new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};

			// Create a marker for each place.
			var marker = new google.maps.Marker({
				map     : map,
				icon    : image,
				title   : place.name,
				position: place.geometry.location
			});

			markers.push(marker);



			bounds.extend(place.geometry.location);




		}
		if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
			var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() + 0.01);
			var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() - 0.01);
			bounds.extend(extendPoint1);
			bounds.extend(extendPoint2);
		}
		map.fitBounds(bounds);





	});
	// [END region_getplaces]

	// Bias the SearchBox results towards places that are within the bounds of the
	// current map's viewport.
	google.maps.event.addListener(map, 'bounds_changed', function () {
		var bounds = map.getBounds();
		searchBox.setBounds(bounds);
	});


	var myMapsEngineLayer = new google.maps.KmlLayer({
		suppressInfoWindows: true,
		preserveViewport   : false,
		//url                : 'http://mapsengine.google.com/map/kml?mid=zWvgkr23Oofo.kUtb8VgXr3w0&r=' + Math.random()
		url                : 'http://mapsengine.google.com/map/kml?mid=z69UsdMNhnPs.kz_ceda-ZUMU&r=' + Math.random()
	});
	myMapsEngineLayer.setMap(map);

	google.maps.event.addListener(myMapsEngineLayer, 'click', function (kmlEvent) {
		var text = kmlEvent.featureData.description;

		var test = text.match(/WARD_ID\s(.*)/);
		test = test[1];
		test = test.replace(" ", "");

		if (test) {
			text = test;
		}
		//	console.info(text); 
	
		$.bbq.pushState({"ward": text});
		getWard();

	});

}

