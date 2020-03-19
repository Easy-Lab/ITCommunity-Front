var ambassadors_map, home_ambassadors_map, dealers_map, fiche_amb;


///////// Début Gestion de l'inialisation de google map
// Variables destinées à connaitre l'état du chargement du js de l'api google map google, du scroll et de la réalisation de l'initialisation de la map
var googleMapJSLoaded = false;
var pageScrolled = false;
var mapInitialised = false;

// Fonction appelé lors du chargement js de l'api google
function googleMapLoaded() {
    googleMapJSLoaded = true;
    initialiseMap();
}

// Fonction lancée lorsque la page est scrollée
function windowScrolled() {
    pageScrolled = true;
    initialiseMap();
}

// Fonction permettant d'initialiser google map quand toute les conditions sont remplies
function initialiseMap() {
    // initMaps sera uniquement lancé quand un scroll a eu lieu et que le script js est chargé ET que l'on n'a pas déjà initialisé la map
    if (googleMapJSLoaded && !mapInitialised) {
        if (pageScrolled || !window.amblab.features['map.load_on_scroll']) {
            initMaps();
            mapInitialised = true;
        }
    }
}

// Au chargement de la page ajout
$(document).ready(function () {
    // Ajout d'un évènement au scroll de la fenêtre (windowScrolled)
    $(window).on('scroll', windowScrolled);
    // Si l'utilisateur a déjà scrollé, on lance windowScrolled
    if ($(document).scrollTop() > 1) {
        windowScrolled();
    }

});
///////// Fin Gestion de l'inialisation de google map


// TODO : infinite scroll dashboard Dealers
function initMaps() {
    window.mapStyles = [
        {
            "featureType": "all",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#ffffff"
                }
            ]
        },
        {
            "featureType": "all",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#000000"
                },
                {
                    "lightness": 13
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#000000"
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#144b53"
                },
                {
                    "lightness": 14
                },
                {
                    "weight": 1.4
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#08304b"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#0c4152"
                },
                {
                    "lightness": 5
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#000000"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#0b434f"
                },
                {
                    "lightness": 25
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#000000"
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#0b3d51"
                },
                {
                    "lightness": 16
                }
            ]
        },
        {
            "featureType": "road.local",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#000000"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#146474"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#021019"
                }
            ]
        }
    ];

    $(document).ready(function () {
        console.log('init maps...')

        if ($('.js-home-ambassadors-map').length) {
            if (typeof window.homeAmbassadorsMapCenter === 'undefined') window.homeAmbassadorsMapCenter = {
                lat: 46.52863469527167,
                lng: 2.43896484375
            };
            if (typeof window.homeAmbassadorsMapZoom === 'undefined') window.homeAmbassadorsMapZoom = 6;
            if (typeof window.homeAmbassadorsMapCountry === 'undefined') window.homeAmbassadorsMapCountry = 'France';

            //fiche_amb = new ficheAmb()
            home_ambassadors_map = new HomeAmbassadorsMap()
        }
        if ($('.js-dealers-map').length) {
            dealers_map = new DealersMap()
        }
        if ($('.js-ambassadors-map').length) {
            ambassadors_map = new AmbassadorsMap()
        }
    })

    if (typeof window.initCustomMaps !== 'undefined') {
        window.initCustomMaps()
    }
}

var CustomMap = function () {
    var self = this

    this.element = null
    this.map = null
    this.markers = []
    this.cluster = null
    this.activeInfoWindow = null

    this.init = function (element, zoom, center) {
        if (typeof zoom === 'undefined') zoom = 6
        if (typeof center === 'undefined') center = {lat: 46.52863469527167, lng: 2.43896484375}

        self.element = element
        var props = {
            zoom: zoom,
            maxZoom: 11,
            center: center,
            styles: window.mapStyles,
            mapTypeControl: false,
            streetViewControl: false,
            rotateControl: false,
            zoomControl: false,
        }
        if (typeof window.homeAmbassadorsMapMaxZoom !== 'undefined')
            props['maxZoom'] = window.homeAmbassadorsMapMaxZoom
        if (typeof window.homeAmbassadorsMapMinZoom !== 'undefined')
            props['minZoom'] = window.homeAmbassadorsMapMinZoom
        self.map = new google.maps.Map(element, props);

        var zoomControlDiv = document.createElement('div');
        var zoomControl = new self.zoomControl(zoomControlDiv, self.map);

        zoomControlDiv.index = 1;
        self.map.controls[google.maps.ControlPosition.TOP_LEFT].push(zoomControlDiv);
    }

    this.addMarker = function (center, popup, icon, id) {
        var position = {lat: center.latitude, lng: center.longitude}
        var props = {
            map: self.map,
            position: position
        }

        if (icon !== null) props.icon = icon

        var marker = new google.maps.Marker(props);
        marker.set("id",id)

        if (popup !== null) {
            var infoWindow = new google.maps.InfoWindow({
                content: popup,
                maxWidth: 278
            });

            marker.addListener('click', function () {
                if (self.activeInfoWindow !== null) {
                    self.activeInfoWindow.close()
                }
                infoWindow.open(self.map, marker)
                self.activeInfoWindow = infoWindow
                self.lazyLoad()
            })
        }
        marker.addListener('click', function () {
            if (self.activeInfoWindow !== null) {
                self.activeInfoWindow.close()
            }
            $.ajax({
                method: 'post',
                url: '/mapping/popup_amb',
                data: {
                    id: marker.get("id")
                },
                success: function (json) {
                    console.log(json)
                    var infoWindow = new google.maps.InfoWindow({
                        content: json.popup,
                        maxWidth: 278
                    });
                    infoWindow.open(self.map, marker)
                    self.activeInfoWindow = infoWindow
                    self.lazyLoad()

                },
                error: function (json) {
                    console.log(json);
                }

            })
        })

        self.markers.push(marker)
    }

    this.setMarkers = function (markers) {
        self.resetMarkers()
        markers.map(function (marker) {
            self.addMarker(
                {
                    latitude: marker.latitude,
                    longitude: marker.longitude
                },
                typeof marker.popup !== 'undefined' ? marker.popup : null,
                typeof marker.icon !== 'undefined' ? marker.icon : null,
                typeof marker.id !== 'undefined' ? marker.id : null
            )

        })
        self.makeCluster()
    };

    this.resetMarkers = function () {
        self.markers.map(function (marker) {
            marker.setMap(null);
        })
        self.markers = []
    }

    this.makeCluster = function () {
        if (self.cluster !== null) {
            self.cluster.clearMarkers()
            self.cluster.addMarkers(self.markers)
        } else {
            var cluster_styles = [];
            for (var i = 1; i <= 5; i++) {
                cluster_styles.push({
                    width: 31,
                    height: 38,
                    url: typeof window.mapClustererImage !== 'undefined' ? window.mapClustererImage : 'https://itcommunity.fr/public/assets/images/clusterer.png',
                    textColor: 'white',
                    textSize: 24,
                    anchor: [-5, 0]
                })
            }

            self.cluster = new MarkerClusterer(
                self.map,
                self.markers,
                {
                    styles: cluster_styles,
                    maxZoom: window.amblab.features['map.clusters.max_zoom'],
                    gridSize: window.amblab.features['map.clusters.grid_size']
                    //imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
                }
            );
        }
    }

    this.focus = function (address, markers, callback) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({'address': address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                    if (results && results[0] && results[0].geometry && results[0].geometry.viewport) {
                        //self.map.fitBounds(results[0].geometry.viewport);

                        var nearest_marker = {geocode: null, distance: 0};


                        var base_geocode = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());


                        //TODO reprendre ici
                        var center = {
                            lat: results[0].geometry.location.lat(),
                            lng: results[0].geometry.location.lng()
                        };

                        //console.log(center);

                        if (callback) {
                            //console.log("CUSTOM MAP je vais appeller le callback");
                            callback(center);
                            //console.log("CUSTOM MAP callback appellé");
                        }


                        for (var marker in markers) {
                            var marker_geocode = new google.maps.LatLng(markers[marker].latitude, markers[marker].longitude);
                            var distance = google.maps.geometry.spherical.computeDistanceBetween(marker_geocode, base_geocode);
                            if (nearest_marker.geocode == null || distance < nearest_marker.distance) {
                                nearest_marker.geocode = marker_geocode;
                                nearest_marker.distance = distance;
                            }
                        }

                        var bounds = new google.maps.LatLngBounds();

                        bounds.extend(base_geocode);

                        if (nearest_marker.geocode != null) {
                            var offset_lat = nearest_marker.geocode.lat() - base_geocode.lat();
                            var offset_lng = nearest_marker.geocode.lng() - base_geocode.lng();
                            var nearest_opposite = new google.maps.LatLng(base_geocode.lat() - offset_lat, base_geocode.lng() - offset_lng);

                            bounds.extend(nearest_marker.geocode);
                            bounds.extend(nearest_opposite);
                        }

                        self.map.fitBounds(bounds);
                        self.map.setZoom(14);

                        // self.map.setZoom(self.map.getZoom() + 1)
                    } else if (results && results[0] && results[0].geometry && results[0].geometry.bounds) {
                        self.map.fitBounds(results[0].geometry.bounds);
                        // self.map.setZoom(self.map.getZoom() + 1)
                    }
                }
            }
        });

    }

    this.lazyLoad = function () {
        $(self.element).find('img[data-src]').each(function () {
            if ($(this).is(':visible')) {
                $(this).attr('src', $(this).attr('data-src'))
            }
        })
    }

    this.zoomControl = function (controlDiv, map) {
        // Container
        var controlUI = document.createElement('div');
        controlUI.className = 'map-zoom'
        controlDiv.appendChild(controlUI);

        // +
        var controlZoomUp = document.createElement('button');
        controlZoomUp.innerHTML = '+';
        controlUI.appendChild(controlZoomUp);

        // -
        var controlZoomDown = document.createElement('button');
        controlZoomDown.innerHTML = '-';
        controlUI.appendChild(controlZoomDown);

        controlZoomUp.addEventListener('click', function () {
            map.setZoom(map.getZoom() + 1)
        });

        controlZoomDown.addEventListener('click', function () {
            map.setZoom(map.getZoom() - 1)
        });
    }
}

var HomeAmbassadorsMap = function () {

    var self = this

    self.$element = $('.js-home-ambassadors-map')
    self.map = new CustomMap()
    self.map.init(self.$element.get(0), window.homeAmbassadorsMapZoom, window.homeAmbassadorsMapCenter)
    self.loading = false
    self.page = 1
    self.geolocation = new CustomGeolocation()

    self.load = function (page, zipCodeElement) {

        var zipCodeElement = typeof zipCodeElement === 'undefined' ? $('.js-map-search-submit').eq(0) : zipCodeElement;
        console.log('HomeAmbassadorsMap load', page);

        self.loading = true
        self.page = page
        //var zipcode = $('.js-map-search-zipcode').val()

        var form = {page: page}
        var mapToInit = true;

        zipCodeElement.parents('.home-map-search').find('input, select').each(function () {
            if (typeof $(this).attr('name') !== typeof undefined && $(this).attr('name') !== false && $(this).attr('name').length > 0) {
                var value = $(this).val();
                if ($(this).attr('pattern')) {
                    var pattern = new RegExp($(this).attr('pattern'));
                    if (!pattern.test(value))
                        return;
                }
                if (value.length == 2){
                    if (value == '50'){
                    value = value + "000";
                    }
                }
                form[$(this).attr('name')] = value;
            }
        })


        var appelAmbassador = function () {

            console.log(self.$element.attr('data-endpoint'));
            $.ajax({

                method: 'post',
                url: self.$element.attr('data-endpoint'),
                data: form,
                success: function (data) {
                    self.loading = false

                    if (self.page == 1) {

                        console.log(data.results)

                        if (data.results.length == 0) {
                            $('.js-home-ambassadors-listing').addClass('empty-result')

                            if (window.location.hostname === 'sebes.ambassadorslab.com') {
                                $('.js-home-ambassadors-listing').html("<p>No hay un Embajador con los criterios solicitados. Modificar su búsqueda.</p>")
                            } else if (window.location.hostname === 'embajadorescompanion.es') {
                                $('.js-home-ambassadors-listing').html("<p>No hay un Embajador con los criterios solicitados. Modificar su búsqueda.</p>")
                            } else if (window.location.hostname === 'www.embajadorescompanion.es') {
                                $('.js-home-ambassadors-listing').html("<p>No hay un Embajador con los criterios solicitados. Modificar su búsqueda.</p>")
                            } else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                                $('.js-home-ambassadors-listing').html("<p>Non ci sono ambassadors con i criteri richiesti. Modifica la tua ricerca</p>")
                            } else if (window.location.hostname === 'ambassadorcompanion.it') {
                                $('.js-home-ambassadors-listing').html("<p>Non ci sono ambassadors con i criteri richiesti. Modifica la tua ricerca</p>")
                            } else if (window.location.hostname === 'www.ambassadorcompanion.it') {
                                $('.js-home-ambassadors-listing').html("<p>Non ci sono ambassadors con i criteri richiesti. Modifica la tua ricerca</p>")
                            } else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                                $('.js-home-ambassadors-listing').html("<p>Es gibt keinen Botschafter mit den geforderten Kriterien. Ändern Sie Ihre Suche.</p>")
                            } else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                                $('.js-home-ambassadors-listing').html("<p>Es gibt keinen Botschafter mit den geforderten Kriterien. Ändern Sie Ihre Suche.</p>")
                            } else {

                                $('.js-home-ambassadors-listing').html("<p>Il n'y a pas d'Ambassadeur avec les critères demandés. Modifiez votre recherche.</p>")
                            }
                        } else {
                            $('.js-home-ambassadors-listing').removeClass('empty-result')
                            $('.js-home-ambassadors-listing').html('')
                        }


                        // Si on est en phase d'initialisation on fait un appel Ajax et on initialise la map. Dans le cas contraire on focus et actualise l'affichage
                        try {
                            self.map.setMarkers(data.markers)
                            if (typeof form.zipcode !== 'undefined' && form.zipcode !== null && form.zipcode.length) {
                                self.map.focus(form.zipcode + ', ' + homeAmbassadorsMapCountry, data.markers, function (center) {
                                });
                            }

                        } catch (e) {
                            console.log(e)
                        }
                    } else console.log('not page 1')

                    data.results.map(function (result) {
                       $('.js-home-ambassadors-listing').append(result.html)
                    })

                    $(".homepage section.map a.profile-mapping-result-see-more").click(function () {
                        var hidden_profile = $(".homepage section.map div.profile-holder-responsive");

                        if (hidden_profile.length <= 10) {
                            $(this).hide();
                        }

                        hidden_profile.each(function (idx) {
                            if (idx < 10) {
                                $(this).show();
                            } else {
                                return false;
                            }
                        })
                    });
                }
            })
        };

        // console.log(form.zipcode, form.zipcode.length)

        // phase de recherche
        if (typeof form.zipcode !== 'undefined' && form.zipcode !== null && form.zipcode.length) {
            // Appel à google map pour avoir les coordonnées de la localisation demandée par l'utilisateur
            try {
                //TODO à lancer une fois que l'apel ajax vers ambassador est réalisé
                //self.map.setMarkers(data.markers)
                self.map.focus(form.zipcode + ', ' + homeAmbassadorsMapCountry, [] /*data.markers*/, function (center) {

                    if (center && (center.lat) && (center.lng)) {
                        form["latitude"] = center.lat;
                        form["longitude"] = center.lng;
                    } else {
                        form["latitude"] = null;
                        form["longitude"] = null;
                    }

                    console.log("Contenu du formulaire après update center : ", form);

                    appelAmbassador(false);

                });
            } catch (e) {
                console.log(e)
            }
        } else {
            appelAmbassador();
        }
    }

    self.load(1);

    $('.js-map-search-submit').click(function () {
        console.log('.js-map-search-submit click', 'disabled=', $(this).hasClass('disabled'));
        if (!$(this).hasClass('disabled'))
            self.load(1, $(this))
    });

    $('.js-map-search-zipcode').on('keyup', function (e) {
        if (e.keyCode == 13)
            $(this).parents('.home-map-search').find('.js-map-search-submit').click();
    });

    $('.js-home-map-search').on('change', 'select', function () {
        console.log('.js-home-map-search select');
        self.load(1)
    })

    $('.js-map-search-product').change(function () {
        console.log('.js-map-search-product change');
        self.updateLevels()
    })

    self.sublevels = $('.js-map-search-product-sublevel').html()

    self.updateLevels = function () {
        console.log('updateLevels ');
        if ($('.js-map-search-product').val() == '_') {
            $('.js-map-search-product-sublevel').parent().hide()
        } else {
            $('.js-map-search-product-sublevel').html(self.sublevels)
            $('.js-map-search-product-sublevel option').each(function () {
                if ($(this).attr('data-parent') != $('.js-map-search-product').val() && $(this).val() != '_')
                    $(this).remove()
            })
            $('.js-map-search-product-sublevel').parent().show()
            window.updateCustomControls()
        }
    }

    self.updateLevels()

    // Tryba only

    if ($('body').hasClass('TRYBA')) {
        self.levels = $('.js-map-search-product').html()

        $('.js-map-search-environment_type').change(function () {
            $('.js-map-search-product').html(self.levels)
            if ($(this).val() == 'appartment') {
                $('.js-map-search-product').each(function () {
                    if ($(this).val() == '_') return;
                    if (['doors', 'garage_doors'].indexOf($(this).attr('data-name')) !== -1) $(this).remove()
                })
            }
            window.updateCustomControls()
            self.updateLevels()
        })
    }

    // SEB only

    if ($('body').hasClass('SEB')) {
        self.products = $('.js-map-search-product').html()

        $('.js-map-search-connected').change(function () {
            $('.js-map-search-product').html(self.products)

            if ($(this).val() != '_') {
                var ids = $(this).val().split(',')
                $('.js-map-search-product option').each(function () {
                    if ($(this).val() == '_') return;
                    if (ids.indexOf($(this).val()) === -1) $(this).remove()
                })
            }

            window.updateCustomControls()
            self.load(1)
        })
    }

    // Geoloc
   // $('.js-geolocation').click(function () {
        //if ($('.js-geolocation').hasClass('disabled')) return false;
    console.log(window.amblab.features['map.geoloc']);
   if (window.amblab.features['map.geoloc']){
        console.log('Geolocation...')
        //$('.js-geolocation').addClass('disabled')
        self.geolocation.getCurrentPosition(false, function (error, position) {
            if (error) {
                $('.js-geolocation').removeClass('disabled')
                return false;
            }

            console.log(position)

            var latlng = new google.maps.LatLng(position.latitude, position.longitude);
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({'latLng': latlng}, function (results, status) {
                var success = false
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        for (j = 0; j < results[0].address_components.length; j++) {
                            if (results[0].address_components[j].types[0] == 'postal_code') {
                                $('.js-map-search-zipcode').val(results[0].address_components[j].short_name)
                                self.load(1)
                                success = true
                            }
                        }
                    }
                }
                if (success) {
                    if (window.location.hostname === 'embajadorescompanion.es') {
                        self.geolocation.notify('Posición detectada')
                    }
                    else if (window.location.hostname === 'sebes.ambassadorslab.com') {
                        self.geolocation.notify('Posición detectada')
                    }
                    else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                        self.geolocation.notify('Posizione rilevata')
                    }

                    else if (window.location.hostname === 'ambassadorcompanion.it') {
                        self.geolocation.notify('Posizione rilevata')
                    }
                    else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                        self.geolocation.notify('Position erkannt')

                    }

                    else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                        self.geolocation.notify('Position erkannt')

                    }

                    else {
                        self.geolocation.notify('Position détectée')
                    }
                }
                else {
                    if (window.location.hostname === 'embajadorescompanion.es') {
                        self.geolocation.notify('Error al localizar')
                    }
                    else if (window.location.hostname === 'sebes.ambassadorslab.com') {
                        self.geolocation.notify('Error al localizar')
                    }
                    else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                        self.geolocation.notify('Impossibile localizzare')
                    }

                    else if (window.location.hostname === 'ambassadorcompanion.it') {
                        self.geolocation.notify('Impossibile localizzare')
                    }
                    else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                        self.geolocation.notify('Fehler beim Suchen')

                    }

                    else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                        self.geolocation.notify('Fehler beim Suchen')

                    }

                    else {
                        self.geolocation.notify('Échec de la localisation')
                    }
                }
                $('.js-geolocation').removeClass('disabled')
            });

        })
   }
   // })

    // Infinite scroll

    $('.js-home-ambassadors-listing').scroll(function (e) {
        var scrollY = $(this).get(0).scrollHeight - $(this).get(0).scrollTop;
        var height = $(this).get(0).offsetHeight;
        var offset = height - scrollY;

        if (offset == 0 || offset == 1) {
            if (!self.loading) self.load(self.page + 1)
        }
    })
}

var DealersMap = function () {
    var self = this

    self.$element = $('.dealers-map')
    self.map = new CustomMap()
    self.map.init(self.$element.get(0))

    self.load = function () {
        var zipcode = $('.js-map-search-zipcode').val()
        $.ajax({
            method: 'post',
            url: self.$element.attr('data-endpoint'),
            data: {
                zipcode: zipcode,
            },
            success: function (data) {
                self.map.setMarkers(data.markers)

                if (zipcode.length) {
                    self.map.focus(zipcode + ', ' + homeAmbassadorsMapCountry, data.markers, null)
                }
            }
        })
    }

    self.load()

    $('.js-map-search-submit').click(function () {
        self.load()
    })
}

var AmbassadorsMap = function () {
    var self = this

    self.$element = $('.js-ambassadors-map')
    self.map = new CustomMap()
    self.map.init(self.$element.get(0))

    self.page = 1
    self.id = self.$element.attr('data-id')
    var form = {page: self.page}

    form['id'] = self.id;

    self.load = function () {
        console.log(form)
        $.ajax({
            method: 'post',
            url: $('.js-ambassadors-map').attr('data-endpoint'),
            data: form,
            success: function (data) {

                self.map.setMarkers(data.markers)
                $('.js-ambassadors-listing').html('')

                data.results.map(function (result) {
                    $('.js-ambassadors-listing').append(result.html)
                })
            }
        })
    }

    self.load(1)
}

//

var CustomGeolocation = function () {
    var self = this

    if (!$('.js-geolocation-status').length) {
        $('body').append('<div class="geolocation-status"><span class="js-geolocation-status"></span></div>')
    }

    self.$status = $('.js-geolocation-status')
    self.statusTimeout = null

    self.notify = function (message, keep) {
        clearTimeout(self.statusTimeout)
        self.$status.text(message)
        self.$status.fadeIn()
        if (!keep) {
            self.statusTimeout = setTimeout(function () {
                self.$status.fadeOut()
            }, 3000)
        }
    }

    self.check = function () {
        if (!navigator.geolocation) {
            self.notify('Votre navigateur ne dispose pas de cette fonctionnalité')
            return false;
        }
        return true;
    }

    self.getCurrentPosition = function (notify, callback) {
        if (!self.check()) return callback(true);

        if (window.location.hostname === 'embajadorescompanion.es') {
            self.notify('Ubicación...', true)
        }
        else if (window.location.hostname === 'sebes.ambassadorslab.com') {
            self.notify('Ubicación...', true)
        }
        else if (window.location.hostname === 'sebit.ambassadorslab.com') {
            self.notify('Posizione...', true)
        }

        else if (window.location.hostname === 'ambassadorcompanion.it') {
            self.notify('Posizione...', true)
        }
        else if (window.location.hostname === 'sebde.ambassadorslab.com') {
            self.notify('Lage...', true)

        }

        else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
            self.notify('Lage...', true)

        }

        else {
            self.notify('Localisation...', true)
        }

        navigator.geolocation.getCurrentPosition(function (position) {
            if (notify)
            {
                if (window.location.hostname === 'embajadorescompanion.es') {
                    self.notify('Posición detectada')
                }
                else if (window.location.hostname === 'sebes.ambassadorslab.com') {
                    self.notify('Posición detectada')
                }
                else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                    self.notify('Posizione rilevata')
                }

                else if (window.location.hostname === 'ambassadorcompanion.it') {
                    self.notify('Posizione rilevata')
                }
                else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                    self.notify('Position erkannt')

                }

                else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                    self.notify('Position erkannt')

                }

                else {
                    self.notify('Position détectée')
                }
            }
            callback(false, position.coords)
        }, function (error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    if (window.location.hostname === 'embajadorescompanion.es') {
                        self.notify("Primero debes permitir el acceso a tu ubicación.")
                    }
                    else if (window.location.hostname === 'sebes.ambassadorslab.com') {
                        self.notify("Primero debes permitir el acceso a tu ubicación.")
                    }
                    else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                        self.notify("Devi prima consentire l'accesso alla tua posizione")
                    }

                    else if (window.location.hostname === 'ambassadorcompanion.it') {
                        self.notify("Devi prima consentire l'accesso alla tua posizione")
                    }
                    else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                        self.notify("Sie müssen zuerst den Zugriff auf Ihren Standort zulassen")

                    }

                    else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                        self.notify("Sie müssen zuerst den Zugriff auf Ihren Standort zulassen")

                    }

                    else {
                        self.notify("Vous devez d'abord autoriser l'accès à votre position")
                    }
                    break;
                case error.POSITION_UNAVAILABLE:
                    if (window.location.hostname === 'embajadorescompanion.es') {
                        self.notify('Servicio de localización no disponible.')
                    }
                    else if (window.location.hostname === 'sebes.ambassadorslab.com') {
                        self.notify('Servicio de localización no disponible.')
                    }
                    else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                        self.notify('Servizio di localizzazione non disponibile')
                    }

                    else if (window.location.hostname === 'ambassadorcompanion.it') {
                        self.notify('Servizio di localizzazione non disponibile')
                    }
                    else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                        self.notify('Standortdienst nicht verfügbar')

                    }

                    else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                        self.notify('Standortdienst nicht verfügbar')

                    }

                    else {
                        self.notify('Service de localisation indisponible')
                    }
                    break;
                default:
                    if (window.location.hostname === 'embajadorescompanion.es') {
                        self.notify('Error al localizar')
                    }
                    else if (window.location.hostname === 'sebes.ambassadorslab.com') {
                        self.notify('Error al localizar')
                    }
                    else if (window.location.hostname === 'sebit.ambassadorslab.com') {
                        self.notify('Impossibile localizzare')
                    }

                    else if (window.location.hostname === 'ambassadorcompanion.it') {
                        self.notify('Impossibile localizzare')
                    }
                    else if (window.location.hostname === 'sebde.ambassadorslab.com') {
                        self.notify('Fehler beim Suchen')

                    }

                    else if (window.location.hostname === 'prep-and-cook-botschafter.de') {
                        self.notify('Fehler beim Suchen')

                    }

                    else {
                        self.notify('Échec de la localisation')
                    }
                    break;
            }
            callback(true)
        });
    }
}
