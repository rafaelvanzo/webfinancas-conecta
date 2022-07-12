<?php
echo '
		<!-- Libs -->
		<script src="site/vendor/jquery.js"></script>
		<script src="site/js/plugins.js"></script>
		<script src="site/vendor/jquery.easing.js"></script>
		<script src="site/vendor/jquery.appear.js"></script>
		<script src="site/vendor/jquery.cookie.js"></script>
		
		<script src="site/vendor/bootstrap.js"></script>
		<script src="site/vendor/twitterjs/twitter.js"></script>
		<script src="site/vendor/owl-carousel/owl.carousel.js"></script>
		<script src="site/vendor/jflickrfeed/jflickrfeed.js"></script>
		<script src="site/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="site/vendor/jquery.validate.js"></script>

		<!-- Page Scripts -->
		<script src="site/js/views/view.contact.js"></script>

		<!-- Theme Initializer -->
		<script src="site/js/theme.js"></script>

		<!-- Custom JS -->
		<script src="site/js/custom.js"></script>

		<script src="https://maps.google.com/maps/api/js?sensor=false"></script>
		<script src="site/vendor/jquery.gmap.js"></script>

		 <script>

			/*
			Map Settings

				Find the Latitude and Longitude of your address:
					- http://universimmedia.pagesperso-orange.fr/geo/loc.htm
					- http://www.findlatitudeandlongitude.com/find-address-from-latitude-and-longitude/

			*/

			// Map Markers
			var mapMarkers = [{
				address: "-20.2847865, -40.2971275",
				html: "<strong>Web 2 Business</strong><br>Av. Francisco Generoso da Fonseca, 374, Jardim da Penha, Vitória / ES - Brasil",
				icon: {
					image: "https://www.webfinancas.com/site/img/pin.png",
					iconsize: [26, 46],
					iconanchor: [12, 46],
				}
			}];

			// Map Initial Location
			var initLatitude = -20.2847865;
			var initLongitude = -40.2971275;

			// Map Extended Settings
			var mapSettings = {
				controls: {
					panControl: true,
					zoomControl: true,
					mapTypeControl: true,
					scaleControl: true,
					streetViewControl: true,
					overviewMapControl: true
				},
				scrollwheel: false,
				markers: mapMarkers,
				latitude: initLatitude,
				longitude: initLongitude,
				zoom: 16
			};

			var map = $("#googlemaps").gMap(mapSettings);

			// Map Center At
			var mapCenterAt = function(options, e) {
				e.preventDefault();
				$("#googlemaps").gMap("centerAt", options);
			}

		</script>
    
';
?>