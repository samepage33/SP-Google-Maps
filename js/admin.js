/**
 * Admin Scripts for SP Google Maps
 * 
 * @package     SP Google Maps
 * @author      Kudratullah
 * @version     1.1.2
 * @since       SP Google Maps 1.0
 * @copyright   2017 SamePage Inc.
 * @license     GPL-2.0+ 
 */
(function($){
	var panorama;
	//Basic Steet Maps
	var initializeDragableMarkerMaps = function(){
		//var LatLng = new google.maps.LatLng(23.727369,90.396604);
		var LatLng = new google.maps.LatLng(Number(mapdata.maps_lat),Number(mapdata.maps_lng));
		
		var map = new google.maps.Map(document.getElementById("map"), {
			center: LatLng,
			zoom:Number(mapdata.maps_zoom),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			streetViewControl: false,
		});
	
		var marker = new google.maps.Marker({
			position: LatLng,
			//title: "Your Location!",
			draggable: true
		});
		marker.setMap(map)	;
		//on zoom input field change
		$("#maps-zoom").change(function(){
			map.setZoom(Number($(this).val()));
		});
		//on latlan input field change
		$("#maps-latlng").change(function(){
			latlan = $(this).val().split(',');
			//var LatLng = new google.maps.LatLng(23.727369,90.396604);
			var LatLng = new google.maps.LatLng(Number(latlan[0]),Number(latlan[1]));
			marker.setPosition(LatLng);
			map.panTo(marker.getPosition());
			panorama.setPosition(LatLng);
		});
		google.maps.event.addListener( marker, 'dragend', function(ev){
			map.panTo(marker.getPosition());
			document.getElementById('maps-latlng').value = ev.latLng.lat()+','+ev.latLng.lng();
			panorama.setPosition(ev.latLng);
		});
		google.maps.event.addListener(map, 'zoom_changed', function() {
			map.panTo(marker.getPosition());
			document.getElementById('maps-zoom').value = map.getZoom();
		});
	};
	//Street View Maps
	var initializeStreetView = function() {
		var LatLng = new google.maps.LatLng(Number(mapdata.sv_lat),Number(mapdata.sv_lng));
		var panoramaOptions = {
			position: LatLng,
			pov: {
				heading: Number(mapdata.heading),
				pitch: Number(mapdata.pitch)
			},
			zoom:Number(mapdata.sv_zoom),
		};
		panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
		//on pov input field change
		$("#maps-pov").change(function(){
			pov = $(this).val().split(',');
			panorama.setPov(({
				heading: Number(pov[0]),
				pitch: Number(pov[1]),
			}));
		});
		//on zoom input field change
		$("#maps-SV-zoom").change(function(){
			panorama.setZoom(Number($(this).val()));
		});
		//on latlan input field change
		$("#maps-SV-latlng").change(function(){
			latlan = $(this).val().split(',');
			//var LatLng = new google.maps.LatLng(23.727369,90.396604);
			var LatLng = new google.maps.LatLng(Number(latlan[0]),Number(latlan[1]));
			panorama.setPosition(LatLng);
		});
		google.maps.event.addListener(panorama, 'pov_changed', function() {
			/*
			var headingCell = document.getElementById('heading_cell');
			var pitchCell = document.getElementById('pitch_cell');
			headingCell.firstChild.nodeValue = panorama.getPov().heading + '';
			pitchCell.firstChild.nodeValue = panorama.getPov().pitch + '';
			*/
			document.getElementById('maps-pov').value = (panorama.getPov().heading+','+panorama.getPov().pitch);
			document.getElementById('maps-SV-zoom').value = (panorama.getPov().zoom);
		});
		google.maps.event.addListener(panorama, 'position_changed', function() {
			document.getElementById('maps-SV-latlng').value = (panorama.getPosition().lat()+','+panorama.getPosition().lng());
		});
	};
	google.maps.event.addDomListener(window, 'load', initializeDragableMarkerMaps);
	google.maps.event.addDomListener(window, 'load', initializeStreetView);
})(jQuery);