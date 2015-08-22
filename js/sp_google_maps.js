/**
 * 
 * 
 */

var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
function initialize() {
	// Create an array of styles.
	var styles = JSON.parse(mapdata.style);
	var latitude = Number(mapdata.lat);
	var longitude = Number(mapdata.lng);
	var routeLatLng = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=r&saddr=" + latitude + "," + longitude + "&daddr=" + mapdata.messages.client_location_request;
	var a = document.getElementById('link');
	if(a){
		a.href = routeLatLng;
	}
	var latlng = new google.maps.LatLng(latitude, longitude);
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myOptions = {
		zoom: 14,
		center: latlng,
		styles: styles,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: false
	};
	var map = new google.maps.Map(document.getElementById("map_canvas_"+mapdata.mapid), myOptions);
	directionsDisplay.setMap(map);
	directionsDisplay.setPanel(document.getElementById("directionsPanel"));
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng)),
		map: map,
		icon: mapdata.icon,
		title: mapdata.title
	});
	var infowindow = new google.maps.InfoWindow({
		content: mapdata.description
	});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	});
}

google.maps.event.addDomListener(window, 'load', initialize);

options = {
	enableHighAccuracy: true,
	timeout: 60000, //60 seconds
	maximumAge: 0
};
/*35.68169,139.765396*/
function geo_error_worn(error){
	switch(error.code){
		case error.TIMEOUT:
			alert(mapdata.messages.geo_timeout);
		break;
		case error.POSITION_UNAVAILABLE:
			alert(mapdata.messages.geo_position_unavailable);
		break;
		case error.PERMISSION_DENIED:
			alert(mapdata.messages.geo_permission_denied);
		break;
		case error.UNKNOWN_ERROR:
			alert(mapdata.messages.geo_unknown_error);
		break;
		default: break;
	}
}
function calcRoute(Mode){
	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(
			function(position){
				var latitude = position.coords.latitude;
				var longitude = position.coords.longitude;
				var latlng = new google.maps.LatLng(latitude, longitude);
				var start = new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng));//document.getElementById("routeStart").value;
				var end = latlng;
				var selectedMode;// = document.getElementById('mode').value;
				if(typeof(Mode) != 'undefined'){
					selectedMode = Mode;
				} else {
					return false;
				}
				var request = {
						origin: end,
						destination: start,
						travelMode: google.maps.TravelMode[selectedMode]
				};
				directionsService.route(request, function(response, status) {
					if (status == 'ZERO_RESULTS') {
						alert(mapdata.messages.g_);
					} else if (status == 'UNKNOWN_ERROR') {
						alert(mapdata.messages.geo_unknown_error);
						//alert('A directions request could not be processed due to a server error. The request may succeed if you try again.');
					} else if (status == 'REQUEST_DENIED') {
						alert(mapdata.messages.g_request_denied);
					} else if (status == 'OVER_QUERY_LIMIT') {
						alert(mapdata.messages.g_over_query_limit);
					} else if (status == 'NOT_FOUND') {
						alert(mapdata.messages.g_not_found);
					} else if (status == 'INVALID_REQUEST') {
						alert(mapdata.messages.g_invalid_request);
					} else if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(response);
					} else {
						alert(mapdata.messages.g_no_status_found +" " + status);
					}
				});
			},geo_error_worn(error),options
		);
	}else{
		alert(mapdata.messages.geo_not_supported);
	}
}


function initialize2() {
// var fenway = new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng));
var fenway = new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng));

var mapOptions = {
	center: fenway,
	zoom: 20
};
var panoramaOptions = {
	position: fenway,
	pov: {
		heading: Number(mapdata.heading),
		pitch: Number(mapdata.pitch)
	}
};
var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano_'+mapdata.mapid), panoramaOptions);
//map.setStreetView(panorama);
}
google.maps.event.addDomListener(window, 'load', initialize2);

//Added By Yasunori Kawakami
function openPublicTransportMobile(){
	var me = $('#link');
	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position) {
			var href = me.attr('href');
			var latitude = position.coords.latitude;
			var longitude = position.coords.longitude;
			href = href.replace(mapdata.messages.client_location_request, latitude + ',' + longitude);
			location.href=href;
		},geo_error_worn(error),options
		);
	}
	return false;
}
function openPublicTransport(){
	var me = $('#link');
	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position) {
			var href = me.attr('href');
			var latitude = position.coords.latitude;
			var longitude = position.coords.longitude;
			href = href.replace(mapdata.messages.client_location_request, latitude + ',' + longitude);
			me.attr('href', href);
		},geo_error_worn(error),options);
	}
	return false;
}