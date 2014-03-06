<?php
date_default_timezone_set('Africa/Johannesburg');
$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}
function test_array($array) {
	header("Content-Type: application/json");
	echo json_encode($array);
	exit();
}
function getConnected($host,$user,$pass,$db) {

	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());

	return $mysqli;
}

$mysqli = getConnected($cfg['DB']['host'],$cfg['DB']['username'],$cfg['DB']['password'],$cfg['DB']['database']);
function getData($mysqli,$sql){
	$rs=$mysqli->query($sql);

	if($rs === false) {
		trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
	} else {
		$rows_returned = $rs->num_rows;
		$arr = $rs->fetch_all(MYSQLI_ASSOC);
		return $arr;
	}

};




$data_wards = getData($mysqli,"SELECT * FROM wd_wards");



$return = array();

foreach($data_wards as $row){
	
	$councillors = array();
	$data_councillors = getData($mysqli,"SELECT wd_councillors.*, wd_parties.name as party FROM wd_councillors LEFT JOIN wd_parties ON wd_councillors.party_ID = wd_parties.ID WHERE wd_councillors.ward_ID='".$row['ID']."' ORDER BY wd_councillors.name ASC");
	foreach($data_councillors as $rowC){
		$councillors[] = array(
			"name"=>$rowC['name'],
			"img"=>$rowC['img'],
			"party"=>$rowC['party'],
		);
	}
	$results = array();
	$data_results = getData($mysqli,"SELECT wd_results.*, wd_parties.name as party FROM wd_results LEFT JOIN wd_parties ON wd_results.party_ID = wd_parties.ID WHERE wd_results.ward_ID='".$row['ID']."' ORDER BY votes DESC");
	foreach($data_results as $rowR){
		$results[] = array(
			"party"=>$rowR['party'],
			"votes"=>$rowR['votes']
		);
	}
	
	
	
	
	
	// 	{"party": "DEMOCRATIC ALLIANCE/DEMOKRATIESE ALLIANSIE", "votes": 7117},
	
	$return[$row['ref']] = array(
		"desc"=>$row['desc'],
		"registered" => $row['registered'],
		"votes" => $row['votes'],
		"councillors" =>$councillors,
		"results"=>$results 
	);
	//echo $row['desc'] . '<br>';
}



//exit();

//$t = array("test");

//test_array($return); 

?>

<!doctype html>
<html>
<head>
<title></title>
<META name="author" content="William Stam">
<META name="date" content="2014/03/04 10:39 AM">
<link rel="stylesheet" href="_css/libs/bootstrap/bootstrap.css">
<style type="text/css">

	body {
		overflow : hidden;
	}

	#page-area {
		position         : absolute;
		top              : 0;
		left             : 0;;
		right            : 0;
		bottom           : 0;
		background-color : rgba(250, 250, 250, 0.8);
		display          : none;
		z-index: 100;
	}

	.page-area {
		position : absolute;

		top      : 0;
		left     : 0;;
		right    : 0;
		bottom   : 0;
		display  : none;;
	}

	#flyaround {
		position : absolute;
		margin   : 0;
		top      : 0;
		left     : 0;;
		right    : 0;
		bottom   : 170px;
	}

	#rollerCoaster {
		list-style : none;
		padding    : 0;
		position   : absolute;
		margin     : 0;
		top        : 0;
		left       : 0;;
		right      : 0;
		bottom     : 0;
	}

	#rollerCoaster li {
		height     : 400px;
		width      : 400px;
		text-align : center;
		cursor     : pointer;
	}

	#rollerCoaster li img {
		width      : auto;
		max-height : 380px;
	}

	#rollerCoaster li.roundabout-in-focus {
		cursor : default;
	}

	#rollerCoaster li h2 {
		display : none;

	}

	#rollerCoaster li.roundabout-in-focus h2 {
		margin-left  : -300px;
		margin-right : -300px;
		display      : block;

	}

	footer {
		display               : block;
		position              : absolute;
		bottom                : 20px;
		left                  : 40px;
		right                 : 40px;
		
		-webkit-border-radius : 10px;
		-moz-border-radius    : 10px;
		border-radius         : 10px;
		border                : #7F7F7F solid 5px;
		background-color      : rgba(250, 250, 250, 0.9);
		text-align            : center;
		padding               : 10px;
		z-index               : 3;
	}
	#page-area footer {
		background-color      : #fff;
		z-index               : 12;
	}
	.bubble-left {

		margin-top            : auto;
		margin-bottom         : auto;
		position              : absolute;
		width                 : 30%;
		top                   : 50px;
		bottom                : 50px;
		padding               : 0px;
		background            : #FFFFFF;
		-webkit-border-radius : 10px;
		-moz-border-radius    : 10px;
		border-radius         : 10px;
		border                : #7F7F7F solid 5px;
		left                  : 40px;
		z-index               : 1000;
	}

	.bubble-left:after {
		content      : '';
		position     : absolute;
		border-style : solid;
		border-width : 20px 0 20px 20px;
		border-color : transparent #FFFFFF;
		display      : block;
		width        : 0;
		z-index      : 1;
		margin-top   : -20px;
		right        : -20px;
		top          : 50%;
	}

	.bubble-left:before {
		content      : '';
		position     : absolute;
		border-style : solid;
		border-width : 24px 0 24px 24px;
		border-color : transparent #7F7F7F;
		display      : block;
		width        : 0;
		z-index      : 0;
		margin-top   : -24px;
		right        : -29px;
		top          : 50%;
	}

	.nextprev-btn {
		position : absolute;
		top      : 0;
		bottom   : 0;
		width    : 100px;
	}

	.nextprev-btn.next {
		right : 0;

	}

	.nextprev-btn.prev {
		left : 0;
	}

	.bubble-top {

		margin-top            : auto;
		margin-bottom         : auto;

		z-index               : 1000;
		background            : #FFFFFF;
		-webkit-border-radius : 10px;
		-moz-border-radius    : 10px;
		border-radius         : 10px;
		border                : #7F7F7F solid 5px;
	}

	#nav-top {
		position : absolute;

		top      : 50px;
		padding  : 0;
		right    : 40px;
		margin   : 0;
		z-index  : 1500;
	}

	#nav-top li a {
		padding : 15px 30px;
	}

	.votegraph {
		background-color : #CCC;
		height           : 5px;
	}

	#map-canvas {
		position : absolute;

		top      : 0;
		left     : 0;;
		right    : 0;
		bottom   : 0;
	}

	.glow {
		-webkit-box-shadow : 0px 0px 16px 0px rgba(50, 50, 50, 0.75);
		-moz-box-shadow    : 0px 0px 16px 0px rgba(50, 50, 50, 0.75);
		box-shadow         : 0px 0px 16px 0px rgba(50, 50, 50, 0.75);
		border             : 1px solid #444;
	}
	
	#searchbox{
		background-color: #fff;
		padding:2px;
		-webkit-border-radius : 3px;
		-moz-border-radius    : 3px;
		border-radius         : 3px;
		margin: 0;
		margin-top: 10px;
		
		-webkit-box-shadow : 0px 0px 3px 0px rgba(50, 50, 50, 0.3) inset;
		-moz-box-shadow    : 0px 0px 3px 0px rgba(50, 50, 50, 0.3) inset;
		box-shadow         : 0px 0px 3px 0px rgba(50, 50, 50, 0.3) inset;
		
	}
	.gmnoprint img {
		max-width: none;
	}
	
</style>
</head>
<body>
<div style="text-align:center;">
	<input type="text" id="searchbox" name="searchbox" class="span5 " style="z-index: 0; width: 400px; padding:10px;" placeholder="Input your address here to find your ward">
</div>



<article id="map-canvas"></article>


<article id="page-area">
	<ul class="nav nav-pills" id="nav-top">
		<li class="active"><a href="#ward-candidates" data-toggle="tab">Candidates</a></li>
		<li><a href="#ward-results" data-toggle="tab">2009 Results</a></li>
		<li>
			<a href="#" class="close-pages" style="padding:14px; border:1px solid #ccc; margin-left:10px; font-size:30px; color: #999;">&times;</a>
		</li>
	</ul>
	<div class="tab-content">
		<section id="ward-candidates" class="page-area tab-pane active">

			<div id="flyaround">

				<div class="bubble-left"></div>


				<ul id="rollerCoaster" class="roundabout-holder">


				</ul>
			</div>


			<footer>
				<button class="btn btn-primary nextprev-btn prev">Previous</button>
				<button class="btn btn-primary nextprev-btn next">Next</button>
				<h1>Candidates <span class="data-candidate-count"></span><br>
					<small class="data-ward-name"></small>
				</h1>

			</footer>
		</section>
		<section id="ward-results" class="page-area tab-pane">
			<h2 style="margin-top: 50px; margin-left: 50px;">Will this year be any different?</h2>

			<div id="ward-results-area" style="margin: 50px; "></div>

			<footer>
				<h1>2009 General elections result<br>
					<small class="data-ward-name"></small>
				</h1>

			</footer>


		</section>
	</div>
</article>




<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=visualization,places&sensor=false"></script>
<script type="text/javascript" src="_js/libs/jquery.js"></script>
<script type="text/javascript" src="_js/libs/bootstrap.js"></script>
<script type="text/javascript" src="_js/plugins/jquery.jqote2.js"></script>
<script type="text/javascript" src="_js/plugins/jquery.ba-bbq.js"></script>
<script type="text/javascript" src="_js/jquery.roundabout.js"></script>
<script type="text/javascript" src="_js/jquery.roundabout-shapes.js"></script>
<script>
var markers = [];
var data = <?php echo json_encode($return); ?>;

 
function show() {

	var ward = $.bbq.getState("ward");
	var sub = $.bbq.getState("sub");
	sub = sub ? sub : 0;
	var data_ward = data[ward];

	$("#page-area").hide();
	if (data_ward) {

		$("#page-area").fadeIn(500);

		$('#rollerCoaster').jqotesub($("#template-roundabout-item"), data_ward);
		roundable(sub);


		$('#ward-results-area').jqotesub($("#template-results"), data_ward);


		load_details();
		replace_data(data_ward);



	}




}
function roundable(sub) {
	if (sub) {

	} else {
		sub = $.bbq.getState("sub");
		sub = sub ? sub : 0;
	}
	$('#rollerCoaster').roundabout({
		shape             : 'lazySusan',
		minOpacity        : 0.3,
		minScale          : 0.2,
		responsive        : true,
		btnNext           : ".nextprev-btn.next",
		btnPrev           : ".nextprev-btn.prev",
		triggerBlurEvents : true,
		triggerFocusEvents: true,
		startingChild     : sub ? sub : 0

	});

}
function replace_data(data) {

	$(".data-ward-name").html(data.desc)
	$(".data-candidate-count").html("("+data.councillors.length+")")
}
function load_details() {
	var ward = $.bbq.getState("ward");
	var sub = $.bbq.getState("sub");
	var data_ward = data[ward];


	sub = sub ? sub : 0;


	var d = data[ward]['councillors'][sub];
	if (d) {

		$(".bubble-left").jqotesub($("#template-roundabout-item-details"), d).stop(true, true).animate({"opacity": 1, "left": "40px"}, 200, function () {
		})

	}
}



$(document).ready(function () {
	show();
	$(document).on("roundfocus", "#rollerCoaster li", function () {
		//console.log("focus");


		var i = $(this).attr("data-i");
		$.bbq.pushState({"sub": i});
		load_details();

	});
	$(document).on("roundblur", "#rollerCoaster li", function () {
		//console.log("blur");
		$(".bubble-left").animate({"opacity": 0, "left": "-200px"}, 300, function () {
		})

	});
	$(document).on("click", ".close-pages", function () {
		$.bbq.removeState("sub");
		$.bbq.removeState("ward");
		$("#page-area").fadeOut(500);

	});
	$(document).on("shown", "#nav-top", function () {
		$('#rollerCoaster').roundabout("relayoutChildren");

	});
	$(document).on("submit","#form-search",function(){
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			title: 'Hello World!'
		});
		
	})


	google.maps.event.addDomListener(window, 'load', initialize);
	
});


function initialize() {

	var markers = [];
	var map = new google.maps.Map(document.getElementById('map-canvas'), {
		zoom: 8,
		center: new google.maps.LatLng(-23.0462413, 29.904656199999977),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	

	// Create the search box and link it to the UI element.
	var input = /** @type {HTMLInputElement} */(
		document.getElementById('searchbox')
	);
	input.style.marginTop = '10px';


	map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);


	

	var searchBox = new google.maps.places.SearchBox(
			/** @type {HTMLInputElement} */(input));

	// [START region_getplaces]
	// Listen for the event fired when the user selects an item from the
	// pick list. Retrieve the matching places for that item.
	google.maps.event.addListener(searchBox, 'places_changed', function() {
		var places = searchBox.getPlaces();

		for (var i = 0, marker; marker = markers[i]; i++) {
			marker.setMap(null);
		}

		// For each place, get the icon, place name, and location.
		markers = [];
		var bounds = new google.maps.LatLngBounds();
		for (var i = 0, place; place = places[i]; i++) {
			var image = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};

			// Create a marker for each place.
			var marker = new google.maps.Marker({
				map: map,
				icon: image,
				title: place.name,
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
	google.maps.event.addListener(map, 'bounds_changed', function() {
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
		test = test.replace(" ","");
		
		if (test){
			text = test;
		}
		console.log(text)
		//	console.info(text); 
		$("#details-pane").hide();
		if (data[text]) {
			$.bbq.pushState({"ward": text});
			//$.bbq.removeState("sub");

			show();

		}

	});
	
}


</script>

<script type="text/x-jqote-template" id="template-roundabout-item">
	<![CDATA[
	<% for (var i in this.councillors) { %>
	<li data-i="<%= i %>">
		<img src="<%= this.councillors[i].img %>" class="glow"><br>

		<h2>
			<%= this.councillors[i].name %><br>
			<small><%= this.councillors[i].party %></small>

		</h2>
	</li>
	<% } %>






	]]>
</script>
<script type="text/x-jqote-template" id="template-roundabout-item-details">
	<![CDATA[
	<h3><%= this.name %></h3>





	]]>
</script>
<script type="text/x-jqote-template" id="template-results">
	<![CDATA[
	<table class="table" style="border-bottom: 1px solid #CCCCCC">
		<tr>
			<td style="vertical-align: top; border-right: 1px solid #ccc;">
				<table class="table table-hover">
					<thead>
					<tr>
						<th></th>
						<th>Value</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>Registered Voters</td>
						<td><%= this.registered %></td>
					</tr>
					<tr>
						<td>Total Votes</td>
						<td><%= this.votes %></td>
					</tr>
					<tr>
						<td>Percentage Turnout</td>
						<td><%= Math.round(((this.votes/this.registered)*100) * 100) / 100 %>%</td>
					</tr>
					</tbody>
				</table>
			</td>
			<td style="vertical-align: top">
				<table class="table table-hover">
					<thead>
					<tr>
						<th>Party</th>
						<th>Votes</th>
					</tr>
					</thead>
					<tbody>
					<% for (var i in this.results) { %>
					<tr>
						<td><%= this.results[i].party %>
							<div style="width: <%= Math.round(((this.results[i].votes / this.votes)*100) * 100) / 100 %>%" class="votegraph"></div>
						</td>
						<td><%= this.results[i].votes %></td>
					</tr>
					<% } %>


					</tbody>
				</table>
			</td>
		</tr>
	</table>





	]]>
</script>
</body>
</html>