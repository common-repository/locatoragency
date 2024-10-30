<?php
/**
 * Class Locator contains different method to implement the management system of location
 * @category Wordpress
 * @copyright Copyright &copy; 2013, Amine BETARI (email: amine.betari@gmail.com)
 * @author abetari
 */

class Locator
{
	private $_postType = 'locator_agency';
	private $_zoomLevel = array(4,5,6,7,8,9,10,11,12,13,14,15,16);
	private $_mapType = array("roadmap" => "roadmap", "hybrid" => "hybrid", "satellite" => "satellite","terrain" => "terrain");


	function __construct()
	{
		// Load files of traduction
		add_action( 'plugins_loaded', array($this, 'locatorInit'));
		add_action( 'wp_enqueue_scripts', array($this, 'locatorScript'));
		add_action( 'wp_head', array($this, 'scriptJs'));
		add_action( 'admin_init', array($this, 'locator_meta_init'));
		add_action( 'admin_menu', array($this, 'lexique_settings_menu') );
		add_shortcode('LOCATOR', array($this, 'locatorShortCode'));
		add_action( 'allLocators', array($this, 'getLocators'));
	}


	/**
	 * Load traduction
	 */
	function locatorInit()
	{
		load_plugin_textdomain(LOCATOR, false, LANGUAGE_PATH_LOCATOR );
	}


	/**
	 * Load scripts js
	 */
	function locatorScript()
	{
		if(get_option('agency_locator_key')) {
			wp_enqueue_script( 'locator', 'http://maps.googleapis.com/maps/api/js?key='.get_option('agency_locator_key').'&sensor=false');
		}
		else {
			wp_enqueue_script( 'locator', 'http://maps.googleapis.com/maps/api/js?sensor=false');
		}
		// Load File CSS
		wp_enqueue_script( 'locator-script',  plugins_url('js/locator.js', dirname(__FILE__)), array('jquery'));
		wp_enqueue_style ( 'locator',  plugins_url('css/locator_front.css', dirname(__FILE__)));
	}


	/**
	 * Load File CSS and Creates the meta box
	 */
	function locator_meta_init()
	{
		wp_enqueue_style ( 'locator',  plugins_url('css/locator.css', dirname(__FILE__)));
		wp_enqueue_script( 'locator', includes_url().'js/jquery/jquery.js');

		add_meta_box('adresses_meta_locator', __('Adresse'), array($this, 'locator_adresses_meta_setup'), $this->_postType, 'normal', 'high');
	    add_action('save_post', array($this, 'locator_meta_save'));
	}


	/**
	 * Display the content of view of meta box
	 */
	function locator_adresses_meta_setup()
	{
		global $post;
		$metaLocator = get_post_meta($post->ID, '_locator_meta', true);
		include (PLUGIN_PATH_LOCATOR. '/view/meta_adresse_locator.php');
		echo '<input type="hidden" name="locator_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	}


	/**
	 * Save Form data (post meta)
	 */
	function locator_meta_save($post_id)
	{
		$locator_meta_noncename = isset($_POST['locator_meta_noncename']) ? $_POST['locator_meta_noncename'] : '';
		// make sure data came from our meta box
		if (!wp_verify_nonce($locator_meta_noncename,__FILE__)) return $post_id;
		$currentData = get_post_meta($post_id, '_locator_meta', true);
		$newData = $_POST['_locator_meta'];
		if($currentData) {
			if(empty($newData)) delete_post_meta($post_id,'_locator_meta');
			else update_post_meta($post_id,'_locator_meta',$newData);
		} else {
			add_post_meta($post_id,'_locator_meta',$newData,true);
		}
	}


	/**
	 * Add Options Page
	 */
	function lexique_settings_menu()
	{
		add_options_page(__('Settings Agency Locator', LOCATOR), __('Locator Option', LOCATOR), 'manage_options', 'slug-agency-locator', array($this, 'pageConfig'));
	}


	/**
	 * Display the configuration of Plugin
	 */
	function pageConfig()
	{

		$erreurs = false;
		if (isset($_POST["update_settings"]) && $_POST["update_settings"]!= '' ) {
			// API KEY
			$apiKey = $_POST["agency_locator_key"];
			update_option("agency_locator_key", $apiKey);
			// Zoom
			$zoom = $_POST["agency_locator_zoom"];
			update_option("agency_locator_zoom", $zoom);
			// map height
			$mapHeight =  $_POST["agency_locator_map_height"];
			update_option("agency_locator_map_height", $mapHeight);
			// map width
			$mapWidth =  $_POST["agency_locator_map_width"];
			update_option("agency_locator_map_width", $mapWidth);
			// Default Map
			$defaultMap =  $_POST["agency_locator_map_type"];
			update_option("agency_locator_map_type", $defaultMap);
			// Icons
			$icon =  $_POST["agency_locator_icon"];
			update_option("agency_locator_icon", $icon);
			// Center Map
			$centerMap = $_POST['agency_locator_center'];
			update_option("agency_locator_center", $centerMap);
			// Country
			$country = $_POST['agency_locator_country'];
			update_option("agency_locator_country", $country);
			//singular
			$singular = $_POST['agency_locator_singular'];
			update_option("agency_locator_singular", $singular);
			//plural
			$plural = $_POST['agency_locator_plural'];
			update_option("agency_locator_plural", $plural);
			// Tester s'il a'git d'une erreur
			if($erreurs) : ?>
				<div id="message" class="error"><?php _e('error seizure', LOCATOR) ?></div>
			<?php else: ?>
				<div id="message" class="updated"><?php _e('Settings saved', LOCATOR) ?></div>
			<?php endif;
		}
		?>
		<h2> <?php screen_icon('themes'); ?> <?php _e('Settings Agency Locator', LOCATOR) ?></h2>
		<form  method="POST" action="">
			<div>
				<input type="hidden" name="update_settings" value="Y" />
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label><?php _e('Google API Key', LOCATOR)?></label></th>
							<td><input type="text" value="<?php echo get_option('agency_locator_key') ?>" size="35"  name="agency_locator_key" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Name of Locator', LOCATOR)?></label></th>
							<td><input type="text" value="<?php echo get_option('agency_locator_singular') ?>" size="35"  name="agency_locator_singular" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Name of Locator (plural)', LOCATOR)?></label></th>
							<td><input type="text" value="<?php echo get_option('agency_locator_plural') ?>" size="35"  name="agency_locator_plural" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Zoom Level', LOCATOR)?></label></th>
							<td>
								<select name="agency_locator_zoom">
								<?php foreach($this->_zoomLevel as $key => $zoomLevel): ?>
								<?php
									$selected = get_option('agency_locator_zoom');
									if($selected == $zoomLevel):
										$selected = 'selected';
									else:
										$selected = '';
									endif;
									echo '<option value="'.$zoomLevel.'" '.$selected.'>'.$zoomLevel.'</option>';
								?>
								<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Map Height', LOCATOR)?></label></th>
							<td><input type="text" value="<?php echo get_option('agency_locator_map_height') ?>" size="10"  name="agency_locator_map_height" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Map Width', LOCATOR)?></label></th>
							<td><input type="text" value="<?php echo get_option('agency_locator_map_width') ?>" size="10"  name="agency_locator_map_width" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label><?php _e('Default Map Type', LOCATOR)?></label></th>
							<td>
								<select name="agency_locator_map_type">
									<?php foreach($this->_mapType as $key => $mapType): ?>
									<?php
										$selected = get_option('agency_locator_map_type');
										if($selected == $mapType):
												$selected = 'selected';
										else:
												$selected = '';
										endif;
										echo '<option value="'.$mapType.'" '.$selected.'>'.$mapType.'</option>';
									?>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e('Center Map At ', LOCATOR)?></label>
							</th>
							<td>
								<?php if(get_option('agency_locator_center') === false): ?>
								<textarea name="agency_locator_center" rows="2" cols="50" wrap="off">Oujda, Maroc</textarea>
								<?php else: ?>
								<textarea name="agency_locator_center" rows="2" cols="50" wrap="off"><?php echo get_option('agency_locator_center') ?></textarea>
								<?php endif; ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e('Your country ', LOCATOR)?></label>
							</th>
							<td><input type="text" value="<?php echo get_option('agency_locator_country') ?>" size="25" name="agency_locator_country" id="agency_locator_country" /></td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label><?php _e('Icon', LOCATOR)?> <br />
								<?php $this->getAllIcone() ?></label>
							</th>
							<td><input type="text" value="<?php echo get_option('agency_locator_icon') ?>" size="115" name="agency_locator_icon" id="agency_locator_icon" /></td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" value="<?php _e('Submit', LOCATOR) ?>" class="button-primary" />
				</p>
			</div>
		</form>
		<?php
	}


	/**
	 * ShortCode of Plugin : that allows to display maps and informations
	 * @return string
	 */
	function locatorShortCode()
	{
		// get All Post of locator-agency
		$allPosts = $this->getLocators();

		$width = (int) get_option('agency_locator_map_width');
		$height = (int)get_option('agency_locator_map_height');

		$output = '';
		$output .= '<input type="hidden" name="country_agency" id="country_agency" value="'.get_option("agency_locator_country").'"  />';
		$output .= '<div>';
			$output .= '<div id="map-canvas" style="width:'. $width.'px; height: '.$height.'px;"></div>';
		$output .= '</div>';
		$output .= '<div class="agency_result" style="width:'. $width.'px;">';
		$output .= '<table width='.$width.'px>';
			$output .= '<tbody>';
				foreach($allPosts as $key => $post) {
					$metaLocator = get_post_meta($post->ID, '_locator_meta', true);
					$output .= '<tr class="ligne_agency_result">';
						$output.= '<input type="hidden" value="'.$metaLocator['longitude'].'" name="agency_locator_longitude'.$key.'" />';
						$output.= '<input type="hidden" value="'.$metaLocator['lattitude'].'" name="agency_locator_lattitude'.$key.'"  />';
						$output.= "<input type='hidden' value='".json_encode($metaLocator, JSON_HEX_APOS)."' name='agency_array_info'  />";

						$output .= '<td class="agency_result">';
							$output .= '<span>'; $output .= $post->post_title; $output .= '</span>';
							$output .= '<br />';
							$output .= '<span>'; $output .= $metaLocator['numerovoie']; $output .= '</span>';
						$output .= '</td>';
						$output .= '<td class="agency_result">';

							if(!empty($metaLocator['phone'])) $output .= '<span><em>'.__('Phone', LOCATOR).'  :'.$metaLocator['phone'].'</em></span>';
							$output .= '<br />';
							if(!empty($metaLocator['fax'])) $output .= '<span><em>'.__('Fax', LOCATOR).' :'.$metaLocator['fax'].'</em></span>';
							$output .= '<br />';
							if(!empty($metaLocator['email'])) $output .= '<span><em>'.__('Email', LOCATOR).' :'.$metaLocator['email'].'</em></span>';
							$output .= '<br />';
							if(!empty($metaLocator['site'])) $output .= '<span><em>'.__('WebSite', LOCATOR).' :<a href='.$metaLocator['site'].' target="_blank">'.$metaLocator['site'].'</a></em></span>';
						$output .= '</td>';
					$output .= '</tr>';
				}
			$output .= '</tbody>';
		$output .= '</table>';
		$output .= '</div>';
		return $output;
	}


	/**
	 * Get Allo Icons to display in maps
	 */
	function getAllIcone()
	{
		$dir = PLUGIN_PATH_LOCATOR.'/images/icons';

		if(is_dir(PLUGIN_PATH_LOCATOR.'/images/icons')) {
			if($dh = opendir($dir)) {
				 while (($file = readdir($dh)) !== false) {
				 	if($file == '.' || $file == '..') continue;
				 	echo '<div class="agency_icon"><img src="'.plugins_url('images/icons/'.$file, dirname(__FILE__)).'"  onclick=document.getElementById("agency_locator_icon").value=this.src  align="top" /></div>';
				 }
				 closedir($dh);
			}
		}
	}


	/**
	 * getAllLocators whose postType is locator_agency
	 * @return object
	 */
	function getLocators()
	{
		$args = array('posts_per_page' => -1, 'post_type' => $this->_postType, 'post_status' => 'publish');
 		$postsArray = get_posts( $args );
 		return $postsArray;
	}


	/**
	 * Display maps and markers
	 */
	 function scriptJs()
	 {
		// get All Post of locator-agency
		$allPosts = $this->getLocators();

	 	// Center of Maps
	 	$centerMap = get_option('agency_locator_center');
	 	$typeMap = get_option('agency_locator_map_type');
	 	$zoomMap = get_option('agency_locator_zoom');
	 	$iconMap = get_option('agency_locator_icon');
		?>
		<script type="text/javascript">
			var geocoder;
			var map;
			var markers = new Array();
			var i = 0;
			var gmarkers = [];
			var gmarkers1 = [];
			var bool = false;
			function initialize() {
				geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'address': '<?php echo $centerMap; ?>'}, function(results, status) {
 					if (status == google.maps.GeocoderStatus.OK) {
 						map.setCenter(results[0].geometry.location);
 						marker = new google.maps.Marker({
						    map: map,
						    position: results[0].geometry.location,
 						});
 						// All Marker
 						<?php  foreach($allPosts as $key => $post): ?>
 						<?php
 						// Meta key
						$metaLocator = get_post_meta($post->ID, '_locator_meta', true);
						if(empty($metaLocator['lattitude']) || empty($metaLocator['longitude'])) continue;
 						?>
 							var pts = new google.maps.LatLng(<?php echo $metaLocator['lattitude'] ?>, <?php echo $metaLocator['longitude'] ?>);
 							info = <?php echo json_encode($metaLocator)?>;
							createMarker(pts, map, info);
 						<?php  endforeach; ?>
 					} else {
      					alert("Le geocodage n\'a pu etre effectue pour la raison suivante: " + status);
     				}
				});

				var mapOptions = {
					zoom: <?php echo $zoomMap ?>,
					mapTypeId: google.maps.MapTypeId.<?php echo strtoupper($typeMap) ?>,
				};


			  	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

			  	// Cas Maroc
		  		var center = "<?php echo strtoupper($centerMap); ?>";
		  		var resultat = center.indexOf('MAR');

				if(resultat != '-1') {
				  	var marocStyles = [{featureType: "administrative.country",stylers: [{ visibility: "off" }]}];
					var marocMapType = new google.maps.StyledMapType(marocStyles ,{name: "Maroc"});
				  	map.mapTypes.set('maroc', marocMapType );
					map.setMapTypeId('maroc');
					/*code affichage frontier maroc*/
					layer = new google.maps.FusionTablesLayer({
						query: {
							select: 'geometry',
							from: '1S4aLkBE5u_WS0WMVSchhBgMLdAARuPEjyW4rs20',
							where: "col1 contains 'MAR'"
						},
						styles: [{
							polylineOptions: {
								strokeColor: "#6E6E6E",
								strokeWeight: 1
							}
						}]
					});
					layer.setMap(map);
				}
				// Cas Maroc

				}

				// Load Function
				window.onload = function () {
					initialize();
				};

				// CreateMarker
				function createMarker(pts, map, info)
				{

					//Info bulle
					var infoBulle = new google.maps.InfoWindow();
					// OptionsMarker
					var optionsMarker = {position: pts, map: map, icon: "<?php echo $iconMap ?>"}
					// Markeur
					var marqueur = new google.maps.Marker(optionsMarker);
					var content = '';
					// display content in infoWindow
					if(typeof info.phone != "undefined") content += "<strong><?php _e('Phone', LOCATOR) ?> : </strong>" + info.phone +"<br />";
					if(typeof info.fax != "undefined") content += "<strong><?php _e('Fax', LOCATOR) ?>: </strong>" + info.fax +"<br />";
					if(typeof info.email != "undefined") content += "<strong><?php _e('Email', LOCATOR) ?> : </strong>" + info.email +"<br />";
					if(typeof info.site != "undefined") content += "<strong><?php _e('WebSite', LOCATOR) ?> : </strong><a href="+info.site+" target='_blank'>" + info.site +"</a><br />";
					// Listener
					google.maps.event.addListener(marqueur, 'click', function() {
						for (var i=0; i<gmarkers.length; i++) {
							gmarkers[i].close();
						}
						gmarkers.push(infoBulle);
					    infoBulle.setContent(content);
					    infoBulle.open(map, marqueur);
					});
					return marqueur;
				}

				function createInfo(info, pts)
				{
					// Convert JSON to Array Javascript
					info = jQuery.parseJSON(info);
					//Info bulle
					var infoBulle = new google.maps.InfoWindow();
					// OptionsMarker
					var optionsMarkers = {position: pts, map: map, icon: "<?php echo $iconMap ?>"}
					// Markeur
					var marqueurs = new google.maps.Marker(optionsMarkers);
					var content = '';
					// display content in infoWindow
					if(typeof info.phone != "undefined") content += "<strong><?php _e('Phone', LOCATOR) ?> : </strong>" + info.phone +"<br />";
					if(typeof info.fax != "undefined") content += "<strong><?php _e('Fax', LOCATOR) ?>: </strong>" + info.fax +"<br />";
					if(typeof info.email != "undefined") content += "<strong><?php _e('Email', LOCATOR) ?> : </strong>" + info.email +"<br />";
					if(typeof info.site != "undefined") content += "<strong><?php _e('WebSite', LOCATOR) ?> : </strong><a href="+info.site+" target='_blank'>" + info.site +"</a><br />";
					for (var i=0; i<gmarkers.length; i++) {
						gmarkers[i].close();
					}
					gmarkers.push(infoBulle);
					infoBulle.setContent(content);
					infoBulle.open(map, marqueurs);
					return marqueurs;
				}
		</script>
		<?php
	 }
}
