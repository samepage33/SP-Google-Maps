/**
 * 
 * 
 */
jQuery(document).ready(function($){
	
	var panorama;
	
	//Basic Steet Maps
	function dragableMaps(){
		
		//var LatLng = new google.maps.LatLng(23.727369,90.396604);
		var ll = mapdata.latlng;
		var LatLng = new google.maps.LatLng(Number(mapdata.lat),Number(mapdata.lng));
		
		var map = new google.maps.Map(document.getElementById("map"), {
			center: LatLng,
			zoom:13,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
	
		var marker = new google.maps.Marker({
			position: LatLng,
			title: "Your Location!",
			draggable: true
		});
		marker.setMap(map)	;
		google.maps.event.addListener( marker, 'dragend', function(ev){
			document.getElementById('maps-latlng').value = ev.latLng.lat()+','+ev.latLng.lng();
			panorama.setPosition(ev.latLng);
		});
	}
	google.maps.event.addDomListener(window, 'load', dragableMaps);
	
	//Street View Maps

	function initialize() {
		
		var LatLng = new google.maps.LatLng(mapdata.lat,mapdata.lng);

	  var panoramaOptions = {
	    position: LatLng,
	    pov: {
	      heading: Number(mapdata.heading),
	      pitch: Number(mapdata.pitch)
	    },
	    visible: true
	  };
	  
	  panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);

	  google.maps.event.addListener(panorama, 'pov_changed', function() {
		  /*
	      var headingCell = document.getElementById('heading_cell');
	      var pitchCell = document.getElementById('pitch_cell');
	      headingCell.firstChild.nodeValue = panorama.getPov().heading + '';
	      pitchCell.firstChild.nodeValue = panorama.getPov().pitch + '';
	      */
		  document.getElementById('maps-pov').value = (panorama.getPov().heading+','+panorama.getPov().pitch);
	  });
	}

	google.maps.event.addDomListener(window, 'load', initialize);
});