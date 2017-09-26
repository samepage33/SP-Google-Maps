/**
 * Frontend Scripts for SP Google Maps
 * 
 * @package     SP Google Maps
 * @author      Kudratullah
 * @version     1.1.2
 * @since       SP Google Maps 1.0
 * @copyright   2017 SamePage Inc.
 * @license     GPL-2.0+ 
 */
(function($){
	"use strict";
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
	// Veriables
	var directionsDisplay,
		directionsService = new google.maps.DirectionsService(),
		options = {
			enableHighAccuracy: true,
			timeout: 60000, //60 seconds
			maximumAge: 0
		},
		styles = ( typeof(mapdata.style) != "undefined" )? JSON.parse(mapdata.style) : "",
		latitude = Number(mapdata.lat),
		longitude = Number(mapdata.lng),
		mwscroll = JSON.parse(mapdata.mwscroll),
		sv_latitude = Number(mapdata.sv_lat),
		sv_longitude = Number(mapdata.sv_lng),
		PublicTransitLink = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=r&saddr=" + encodeURI(mapdata.messages.client_location_request) + "&daddr=" + latitude + "," + longitude,
		WalkingDirectionsLink = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=w&saddr=" + encodeURI(mapdata.messages.client_location_request) + "&daddr=" + latitude + "," + longitude,
		DrivingDirectionLink = "https://www.google.com/maps?hl=en&ie=UTF8&f=d&dirflg=d&saddr=" + encodeURI(mapdata.messages.client_location_request) + "&daddr=" + latitude + "," + longitude;

	$(document).ready(function(){
		$(".travelMode").click(function(event){
			var elem = $(this),
				Mode = elem.attr('data-travelMode');
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position){
					var geolatitude = position.coords.latitude,
						geolongitude = position.coords.longitude,
						start = new google.maps.LatLng(geolatitude, geolongitude),
						end = new google.maps.LatLng(latitude,longitude), //document.getElementById("routeStart").value;
						request = {
							origin: start,
							destination: end,
							travelMode: google.maps.TravelMode[Mode],
							drivingOptions: {
								departureTime: new Date(Date.now()),  // for the time N milliseconds from now.
								trafficModel: "optimistic",
							},
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
				},geo_error_worn,options);
				event.preventDefault();
			} else {
				alert(mapdata.messages.geo_not_supported);
				//if geo coding is not supported replace the hash with google maps link
				$('*[data-travelMode="DRIVING"]').attr('href', DrivingDirectionLink);
				$('*[data-travelMode="WALKING"]').attr('href', WalkingDirectionsLink);
				$('*[data-travelMode="TRANSIT"]').attr('href', PublicTransitLink);
			}
		});
	});

	function initMap() {
		// Create an array of styles.
		var latlng = new google.maps.LatLng(latitude, longitude);
		directionsDisplay = new google.maps.DirectionsRenderer();
		var myOptions = {
			center: latlng,
			scrollwheel: mwscroll,
			styles: styles,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: true,
			streetViewControl: false,
			zoom: Number(mapdata.maps_zoom),
		};
		var map = new google.maps.Map(document.getElementById("map_canvas_"+mapdata.mapid), myOptions);
		directionsDisplay.setMap(map);
		directionsDisplay.setPanel(document.getElementById("directionsPanel"));
		var marker = new google.maps.Marker({
			position: latlng,//new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng)),
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
		// fires if maps bounds changes
		google.maps.event.addListener( map, 'bounds_changed', function(){
			// if mouse scroll zoom is disabled, then enable it in full screen mode
			if( mwscroll == false ) {
				if ( $(map.getDiv()).children().eq(0).height() == window.innerHeight && $(map.getDiv()).children().eq(0).width()  == window.innerWidth ) {
					map.setOptions({ scrollwheel: true });
				} else {
					map.setOptions({ scrollwheel: false });
				}
			}
		} );
	}
	function initStreetView() {
		var fenway = new google.maps.LatLng(sv_latitude,sv_longitude);
		var panoramaOptions = {
			position: fenway,
			scrollwheel: mwscroll,
			pov: {
				heading: Number(mapdata.heading),
				pitch: Number(mapdata.pitch)
			},
			zoom: Number(mapdata.sv_zoom),
		};
		var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano_'+mapdata.mapid), panoramaOptions);
	}
	google.maps.event.addDomListener(window, 'load', initMap);
	google.maps.event.addDomListener(window, 'load', initStreetView);
})(jQuery);