/**
 * 
 * 
 */

var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
function initialize() {
	// Create an array of styles.
	var styles = mapdata.style;
		var latitude = Number(mapdata.lat);
		var longitude = Number(mapdata.lng);

		var routeLatLng = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=r&saddr=" + latitude + "," + longitude + "&daddr=Type Your Location";
		
		var a = document.getElementById('link');
		a.href = routeLatLng;

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

function calcRoute(Mode) {
	navigator.geolocation.getCurrentPosition(function(position) {
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
		alert('No route could be found between the origin and destination.');
		} else if (status == 'UNKNOWN_ERROR') {
		alert('A directions request could not be processed due to a server error. The request may succeed if you try again.');
		} else if (status == 'REQUEST_DENIED') {
		alert('This webpage is not allowed to use the directions service.');
		} else if (status == 'OVER_QUERY_LIMIT') {
		alert('The webpage has gone over the requests limit in too short a period of time.');
		} else if (status == 'NOT_FOUND') {
		alert('At least one of the origin, destination, or waypoints could not be geocoded.');
		} else if (status == 'INVALID_REQUEST') {
		alert('The DirectionsRequest provided was invalid.');
		} else if (status == google.maps.DirectionsStatus.OK) {
		directionsDisplay.setDirections(response);
		} else {
		if (status == 'ZERO_RESULTS') {
			alert("Could not calculate a route to or from one of your destinations.");
		} else {
			alert("There was an unknown error in your request. Requeststatus: " + status);
		}
		}
	});
	});
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