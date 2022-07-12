/*
 * Aprende Google Maps Geocoding através de exemplos
 * Miguel Marnoto
 * 2015 - www.marnoto.com
 *
 */


function searchAddress() {

    var Endereco = $('.MapsLogradouro').val() + ', ' + $('.MapsBairro').val() + ', ' + $('.MapsCidade').val() + ', ' + $('.MapsUf').val();

    var address = Endereco;
	var geocoder = new google.maps.Geocoder();

	geocoder.geocode({address: address}, function(results, status) {

		if (status == google.maps.GeocoderStatus.OK) {

		    var Lat = results[0].geometry.location.lat();

		    var Lng = results[0].geometry.location.lng();

		    $('.MapsLatitude').val(Lat);
		    $('.MapsLongitude').val(Lng);
		}
	});

}
