/**
 * @package WordPress
 * @subpackage SP Google Maps
 * @version 1.1
 * @since SP Google Maps 1.0
 */


/**
 * GeoCoding Error Callback Function
 */
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

var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var options = {
	enableHighAccuracy: true,
	timeout: 60000, //60 seconds
	maximumAge: 0
};
var styles = JSON.parse(mapdata.style);
var latitude = Number(mapdata.lat);
var longitude = Number(mapdata.lng);
var mwscroll = JSON.parse(mapdata.mwscroll);
var PublicTransitLink = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=r&saddr=" + latitude + "," + longitude + "&daddr=" + mapdata.messages.client_location_request;
var WalkingDirectionsLink = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=w&saddr=" + latitude + "," + longitude + "&daddr=" + mapdata.messages.client_location_request;
var DrivingDirectionLink = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=d&saddr=" + latitude + "," + longitude + "&daddr=" + mapdata.messages.client_location_request;

jQuery(document).ready(function($){
	$(".travelMode").click(function(event){
		var Mode = $(this).attr('data-travelMode');
		if (navigator.geolocation){
			navigator.geolocation.getCurrentPosition(function(position){
				var geolatitude = position.coords.latitude;
				var geolongitude = position.coords.longitude;
				if(Mode == 'TRANSIT'){
					PublicTransitLink = PublicTransitLink.replace(mapdata.messages.client_location_request, geolatitude + ',' + geolongitude);
					location.href = PublicTransitLink;
				}else{
					var latlng = new google.maps.LatLng(geolatitude, geolongitude);
					var start = latlng;
					var end = new google.maps.LatLng(latitude,longitude);//document.getElementById("routeStart").value;
				
					var request = {
							origin: start,
							destination: end,
							travelMode: google.maps.TravelMode[Mode]
					};
					
					directionsService.route(request, function(response, status) {
						if (status == 'ZERO_RESULTS') {
							alert(mapdata.messages.g_zero_results);
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
					
				}
			},geo_error_worn,options);
		}else{
			alert(mapdata.messages.geo_not_supported);
			//if geo coding is not supported replace the hash with google maps link
			$('*[data-travelMode="DRIVING"]').attr('href', DrivingDirectionLink);
			$('*[data-travelMode="WALKING"]').attr('href', WalkingDirectionsLink);
			$('*[data-travelMode="TRANSIT"]').attr('href', PublicTransitLink);
		}
		event.preventDefault();
	});
});

function initialize() {
	// Create an array of styles.
	var latlng = new google.maps.LatLng(latitude, longitude);
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myOptions = {
		zoom: 14,
		center: latlng,
		scrollwheel: mwscroll,
		styles: styles,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: true
	};
	var map = new google.maps.Map(document.getElementById("map_canvas_"+mapdata.mapid), myOptions);
	//map.setOptions({ scrollwheel: false });
	directionsDisplay.setMap(map);
	directionsDisplay.setPanel(document.getElementById("directionsPanel"));
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng)),
		map: map,
		icon: mapdata.icon,
		animation: google.maps.Animation.DROP,
		title: mapdata.title
	});
	if(mapdata.description != ''){
		var infowindow = new google.maps.InfoWindow({
			content: mapdata.description
		});
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});
	}
}

google.maps.event.addDomListener(window, 'load', initialize);

function initialize2() {
	// var fenway = new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng));
	var fenway = new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng));
	var mapOptions = {
		center: fenway,
		zoom: 20
	};
	var panoramaOptions = {
		position: fenway,
		scrollwheel: mwscroll,
		pov: {
			heading: Number(mapdata.heading),
			pitch: Number(mapdata.pitch)
		}
	};
	var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano_'+mapdata.mapid), panoramaOptions);
	//map.setStreetView(panorama);
}

google.maps.event.addDomListener(window, 'load', initialize2);
