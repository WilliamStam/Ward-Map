var markers = [];
function getWard() {

	var ward = $.bbq.getState("ward");





	if (ward) {
		$("#page-area").fadeIn(500);
		$("#page-area-loading").show();
		$("#page-area-content").hide();
		$.getData("/data/ward/" + ward, function (data_ward) {
			//$("#page-area").fadeIn(500);

			//console.log(data_ward)
			$('#rollerCoaster').jqotesub($("#template-roundabout-item"), data_ward);
			$('#ward-results-area').jqotesub($("#template-results"), data_ward);
			$('#local-elections').jqotesub($("#template-local-elections"), data_ward);

			$("#right-thumbs ul").jqotesub($("#template-thumbs-item"), data_ward);
			$("#page-area-loading").hide();
			$("#page-area-content").show();

			document.title = default_title_prefix + data_ward.desc;

			if (data_ward.councillors.length == 0) {
				$("#details-pane").css({"opacity": 0, "left": "-200px"});
				$(".nextprev-btn").hide();
			} else {
				$(".nextprev-btn").show();
			}

			roundable();
			//getCouncilor();
			$(".data-ward-name").html(data_ward.desc)
			$(".data-candidate-count").html("(" + data_ward.councillors.length + ")")
		}, "ward");
	} else {
		document.title = default_title;
	}
}


function getCouncilor() {
	var sub = $.bbq.getState("sub");


	//console.log(sub); 


	if (sub) {
		$("#details-pane").html("<div class='loading small'></div>").stop(true, true).animate({"opacity": 1, "left": "40px"}, 200, function () {
		});
		$.getData("/data/councillor/" + sub, function (data) {
			$("#details-pane").jqotesub($("#template-roundabout-item-details"), data);
		}, "councilor");
	} else {

		$("#details-pane").stop(true, true).animate({"opacity": 0, "left": "-200px"}, 300, function () {
		});
	}
}


function roundable(sub) {

	sub = $.bbq.getState("sub");
	sub = sub ? sub : "";

	var startingChild = 0;
	$("#right-thumbs ul li").each(function (i, v) {
		//console.log(i); 
		if ($(this).attr("data-id") == sub) {
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



var uploader;

$(document).ready(function () {
	getWard();


	$(document).on("roundfocus", "#rollerCoaster li", function () {
		//console.log("focus");

		var sub = $(this).attr("data-id");

		$.bbq.pushState({"sub": sub});
		getCouncilor();

		$("#right-thumbs").stop(true, true).animate({"opacity": 1, "right": "50px"}, 200, function () {
		});
		$("#right-thumbs ul li[data-id='" + sub + "']").addClass("active");


	});
	$(document).on("roundblur", "#rollerCoaster li", function () {
		//console.log("blur");
		$("#details-pane").animate({"opacity": 0, "left": "-200px"}, 300, function () {
		});
		$("#right-thumbs").animate({"opacity": 0, "right": "-200px"}, 300, function () {
		});
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


	$(document).on("shown", "#nav-top", function (e) {

		//e.target // activated tab
		//e.relatedTarget // previous tab

		//console.log(e.target)

		if ($(e.target).attr("href") == "#ward-candidates") {
			$('#rollerCoaster').roundabout("relayoutChildren");
		}
		if ($(e.target).attr("href") == "#local-elections") {
			$(".pdf-viewer").css("height", $("#local-elections").height() - 140);
		}



	});


	$(document).on("click", "#nav-admin-add", function (e) {
		e.preventDefault();
		var sub = "_";
		$.getData("/data/admin/candidates/" + sub, function (data) {
			if (data.ward_ID == "") data.ward_ID = $.bbq.getState("ward");
			$("#modal-admin").jqotesub($("#template-admin-candidate"), data).modal("show");
			uploadstuff()
		}, "admin_candidate");


		return false;
	});

	$(document).on("click", "#nav-admin-edit", function (e) {
		e.preventDefault();
		var sub = $.bbq.getState("sub");
		$.getData("/data/admin/candidates/" + sub, function (data) {
			if (data.ward_ID == "") data.ward_ID = $.bbq.getState("ward");
			$("#modal-admin").jqotesub($("#template-admin-candidate"), data).modal("show");
			uploadstuff()
		}, "admin_candidate");


		return false;
	});

	$(document).on("click", "#nav-admin-delete", function (e) {
		e.preventDefault();

		if (confirm("Are you sure you want to delete this candidate?")) {

			$.post("/save/admin/candidate/delete?ID=" + $.bbq.getState("sub"), {}, function () {
				$.bbq.removeState("sub");
				getWard();
			})

		}
		return false;
	});


	$(document).on("click", ".btn-open-ward", function (e) {
		e.preventDefault();
		var wardID = $(this).attr("data-ward");

		$.bbq.pushState({"ward":wardID});
		getWard();

		return false;
	});

	

	
	$(document).mouseup(function (e)
	{
		var container = $("#map-marker-details");

		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.stop(true,true).animate({"opacity": 0, "right": "-200px"}, 300, function () {});
		}
	});
	
	
	

//map-canvas




	google.maps.event.addDomListener(window, 'load', initialize);

});
function uploadstuff() {



	var uploader = new plupload.Uploader({
		runtimes           : 'html5,flash,silverlight,html4',
		browse_button      : 'pickfiles', // you can pass in id...
		container          : document.getElementById('container'), // ... or DOM Element itself
		url                : '/save/admin/candidate/upload',
		flash_swf_url      : '/ui/plupload/js/Moxie.swf',
		silverlight_xap_url: '/ui/plupload/js/Moxie.xap',
		multipart_params   : {},
		unique_names       : true,
		multi_selection    : false,
		filters            : {
			max_file_size: '30mb',
			mime_types   : [
				{title: "Image files", extensions: "jpg,gif,png,jpeg"},
			]
		},

		init: {
			PostInit    : function () {
				document.getElementById('filelist').innerHTML = '';



				/*
				 document.getElementById('uploadfiles').onclick = function() {
				 uploader.start();
				 return false;
				 };
				 */
			},
			BeforeUpload: function (up, file) {
				//up.settings.multipart_params["candidate_ID"] = $("#ID").val();
				up.settings.multipart_params = {
					"candidate_name" : $("#candidate_name").val(),
					"candidate_ID"   : $("#candidate_ID").val(),
					"candidate_party": $("#candidate_party").val()
				}
				up.settings.url = '/save/admin/candidate/upload?ward_ID=' + $("#form-admin-candidate #ward_ID").val();
			},
			FileUploaded: function (up, file) {
				if (up.files.length == (up.total.uploaded + up.total.failed)) {
					getWard();
					$("#modal-admin").modal("hide");
				}

			},
			FilesAdded  : function (up, files) {
				plupload.each(files, function (file) {
					document.getElementById('filelist').innerHTML = '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
				});
			},

			UploadProgress: function (up, file) {
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";

				$("#progress").css("width", file.percent);
			},

			Error: function (up, err) {
				document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			}
		}
	});





	uploader.init();


	$("#form-admin-candidate").on("submit", function (e) {
		e.preventDefault();

		var data = $(this).serialize();

		// console.log(uploader.files.length)
		//$("#pickfiles").start();

		if (uploader.files.length === 0) {
			$.post('/save/admin/candidate?ward_ID=' + $("#form-admin-candidate #ward_ID").val(), data, function () {
				getWard();
				$("#modal-admin").modal("hide");
			});
		} else {
			uploader.start();
		}



		return false;
	});



}


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
				size: new google.maps.Size(50, 50),
				origin    : new google.maps.Point(0, 0),
				scaledSize: new google.maps.Size(50, 50)
			};

			// Create a marker for each place.
			var marker = new google.maps.Marker({
				map     : map,
				icon    : image,
				title   : place.name,
				position: place.geometry.location
			});


			google.maps.event.addListener(marker, 'click', function () {
//				console.log(marker.getPosition()); 
//				console.info(marker); 
				//	map.setZoom(3);


				address_lookup(marker);
				map.setCenter(marker.getPosition());
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
function address_lookup(data) {
	var pos = data.getPosition();
	// console.log(data);
	// console.log(pos);
	// console.log(pos.k);




	//var url = 'http://www.jquery4u.com/scripts/jquery4u-sites.json?callback=?';
	var url = 'http://maps.elections.org.za/iecdirectory/services/delimitation/v1/vssummary?callback=jsonCallback&vdnumber=&location=' + pos.A + '%2C' + pos.k;

	//console.log(url);

	$("#map-marker-details").animate({"opacity": 1, "right": "20px"}, 300, function () {});
	$.ajax({
		type         : 'GET',
		url          : url,
		async        : false,
		jsonpCallback: 'jsonCallback',
		contentType  : "application/json",
		dataType     : 'jsonp',
		success      : function (json) {
			var result = json.result;


			$("#map-marker-details .content").jqotesub($("#template-map-marker-details"), result);
			$("#map-marker-details .loading").fadeOut(300);
			

			//console.dir(result);
		},
		error        : function (e) {
			console.log(e.message);
		}
	});






}
