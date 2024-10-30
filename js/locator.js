jQuery(document).ready( function() {
	jQuery(".ligne_agency_result").click( function(event) {
		var $this = jQuery(this);
		longitude = $this.children().eq(0).val();
		lattitude =  $this.children().eq(1).val();
		info = $this.children().eq(2).val();
		var pts = new google.maps.LatLng(lattitude, longitude);
		createInfo(info, pts);

	});
});