<?php
/**
 * Filed of meta box address Locator
 * @author abetari
 */
?>
<div class="locator_meta_control">
	<p>
		<label><?php _e('Adress', LOCATOR)?></label>
		<input type="text" name="_locator_meta[numerovoie]" id="numerovoie" value="<?php if(!empty($metaLocator['numerovoie'])): echo $metaLocator['numerovoie']; endif;?>" />
	</p>
	<p>
		<label><?php _e('Code Postal', LOCATOR)?></label>
		<input type="text" name="_locator_meta[codepostal]" id="codepostal" value="<?php if(!empty($metaLocator['codepostal'])): echo $metaLocator['codepostal']; endif;?>" />
		<br />
		<label><?php _e('Ville', LOCATOR)?></label>
		<input type="text" name="_locator_meta[ville]" id="ville" value="<?php if(!empty($metaLocator['ville'])): echo $metaLocator['ville']; endif;?>" />
		<br />
		<input type="button" value="Tester" name="TesterGeo" id="testerGeo" />
		<input type="button" value="Obtenir les coordonnées géographiques" name="" id="getCoord"/>
		<div class="geometry">
			<p><label>Longitude : </label><span id="longitude"></span><input type="hidden" id="_longitude" name="_locator_meta[longitude]" value="<?php if(!empty($metaLocator['longitude'])): echo $metaLocator['longitude']; endif;?>" /></p>
			<p><label>Lattitude : </label><span id="lattitude"></span><input type="hidden" id="_lattitude" name="_locator_meta[lattitude]" value="<?php if(!empty($metaLocator['lattitude'])): echo $metaLocator['lattitude']; endif;?>" /></p>
		</div>

		<label><?php _e('Email', LOCATOR)?></label>
		<input type="text" name="_locator_meta[email]" id="email" value="<?php if(!empty($metaLocator['email'])): echo $metaLocator['email']; endif;?>" />
		<br />
		<label><?php _e('WebSite', LOCATOR)?></label>
		<input type="text" name="_locator_meta[site]" id="site" value="<?php if(!empty($metaLocator['site'])): echo $metaLocator['site']; else: echo 'http://'; endif;?>" />
		<br />
		<label><?php _e('Phone', LOCATOR)?></label>
		<input type="text" name="_locator_meta[phone]" id="phone" value="<?php if(!empty($metaLocator['phone'])): echo $metaLocator['phone']; endif;?>" />
		<br />
		<label><?php _e('Fax', LOCATOR)?></label>
		<input type="text" name="_locator_meta[fax]" id="fax" value="<?php if(!empty($metaLocator['fax'])): echo $metaLocator['fax']; endif;?>" />
		<input type="hidden" name="country_agency" id="country_agency" value="<?php echo get_option("agency_locator_country") ?>" />
	</p>

</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=fre"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		// TesterGeo
		jQuery("#testerGeo").click(function() {
				var address = jQuery("#numerovoie").val();
				var codepostal = jQuery("#codepostal").val();
				var ville = jQuery("#ville").val();
				initTest(address, codepostal, ville);
			});
		// GetCoorDonnees
		jQuery("#getCoord").click(function() {
				var address = jQuery("#numerovoie").val();
				var codepostal = jQuery("#codepostal").val();
				var ville = jQuery("#ville").val();
				FindCoord(address, codepostal, ville);
			});
	});
	// Tester Adress
	function initTest(address, codepostal, ville) {
		// Checking if value is undefined or null
		adressVerif = address + ', ' + codepostal+' ' +ville+ ', '+ jQuery("#country_agency").val();
		window.open("http://maps.google.com/?q="+adressVerif+"+&sensor=false");
	}

	// Find Coordonnées
	function FindCoord(address, codepostal, ville) {
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address':address + ', ' + codepostal + ' ' + ville + ', Maroc'}, function(results, status){
				  if (status == google.maps.GeocoderStatus.OK){
				  	 if (results.length == 1) {
				  		jQuery("#longitude").html(results[0].geometry.location.lng());
				  		jQuery("#lattitude").html(results[0].geometry.location.lat());

				  		//*** set champs hidden ***
				  		jQuery("#_longitude").attr('value',results[0].geometry.location.lng());
				  		jQuery("#_lattitude").attr('value',results[0].geometry.location.lat());
				  		jQuery(".geometry").show();
				  	 }else{
				  	 	alert('Plusieurs résultats possible pour l\'adresse saisie, Veuillez affiner votre demande !');
				  	 	alert('Nous allons prendre le premier résultat ');
				  	 	jQuery("#longitude").html(results[0].geometry.location.lng());
				  		jQuery("#lattitude").html(results[0].geometry.location.lat());

				  		//*** set champs hidden ***
				  		jQuery("#_longitude").attr('value',results[0].geometry.location.lng());
				  		jQuery("#_lattitude").attr('value',results[0].geometry.location.lat());
				  		jQuery(".geometry").show();
				  	 }

				  }else{
					alert('Les coordonnées géographiques du programme n\'ont pas pu être déterminées avec les informations saisies');
				  	jQuery(".geometry").hide();
				  }
			});

	}
</script>