var ambassadors_map, home_ambassadors_map, dealers_map, fiche_amb, googleMapJSLoaded = !1, pageScrolled = !1,
    mapInitialised = !1;

function googleMapLoaded() {
    googleMapJSLoaded = !0, initialiseMap()
}

function windowScrolled() {
    pageScrolled = !0, initialiseMap()
}

function initialiseMap() {
    googleMapJSLoaded && !mapInitialised && (!pageScrolled && window.amblab.features["map.load_on_scroll"] || (initMaps(), mapInitialised = !0))
}

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
    void 0 === window.mapStyles && (window.mapStyles = []), $(document).ready(function () {
        console.log("init maps..."), $(".js-home-ambassadors-map").length && (void 0 === window.homeAmbassadorsMapCenter && (window.homeAmbassadorsMapCenter = {
            lat: 46.52863469527167,
            lng: 2.43896484375
        }), void 0 === window.homeAmbassadorsMapZoom && (window.homeAmbassadorsMapZoom = 6), void 0 === window.homeAmbassadorsMapCountry && (window.homeAmbassadorsMapCountry = "France"), home_ambassadors_map = new HomeAmbassadorsMap), $(".js-dealers-map").length && (dealers_map = new DealersMap), $(".js-ambassadors-map").length && (ambassadors_map = new AmbassadorsMap)
    }), void 0 !== window.initCustomMaps && window.initCustomMaps()
}

$(document).ready(function () {
    $(window).on("scroll", windowScrolled), $(document).scrollTop() > 1 && windowScrolled()
});
var CustomMap = function () {
    var o = this;
    this.element = null, this.map = null, this.markers = [], this.cluster = null, this.activeInfoWindow = null, this.init = function (a, e, s) {
        void 0 === e && (e = 6), void 0 === s && (s = {lat: 46.52863469527167, lng: 2.43896484375}), o.element = a;
        var t = {
            zoom: e,
            maxZoom: 11,
            center: s,
            styles: window.mapStyles,
            mapTypeControl: !1,
            streetViewControl: !1,
            rotateControl: !1,
            zoomControl: !1
        };
        void 0 !== window.homeAmbassadorsMapMaxZoom && (t.maxZoom = window.homeAmbassadorsMapMaxZoom), void 0 !== window.homeAmbassadorsMapMinZoom && (t.minZoom = window.homeAmbassadorsMapMinZoom), o.map = new google.maps.Map(a, t);
        var n = document.createElement("div");
        new o.zoomControl(n, o.map);
        n.index = 1, o.map.controls[google.maps.ControlPosition.TOP_LEFT].push(n)
    }, this.addMarker = function (a, e, s, t) {
        var n = {lat: a.latitude, lng: a.longitude}, i = {map: o.map, position: n};
        null !== s && (i.icon = s);
        var l = new google.maps.Marker(i);
        if (l.set("id", t), null !== e) {
            var r = new google.maps.InfoWindow({content: e, maxWidth: 278});
            l.addListener("click", function () {
                null !== o.activeInfoWindow && o.activeInfoWindow.close(), r.open(o.map, l), o.activeInfoWindow = r, o.lazyLoad()
            })
        }
        l.addListener("click", function () {
            null !== o.activeInfoWindow && o.activeInfoWindow.close(), $.ajax({
                method: "post",
                url: "/mapping/popup_amb",
                data: {id: l.get("id")},
                success: function (a) {
                    var e = new google.maps.InfoWindow({content: a.popup, maxWidth: 278});
                    e.open(o.map, l), o.activeInfoWindow = e, o.lazyLoad()
                },
                error: function (o) {
                    console.log(o)
                }
            })
        }), o.markers.push(l)
    }, this.setMarkers = function (a) {
        o.resetMarkers(), a.map(function (a) {
            o.addMarker({
                latitude: a.latitude,
                longitude: a.longitude
            }, void 0 !== a.popup ? a.popup : null, void 0 !== a.icon ? a.icon : null, void 0 !== a.id ? a.id : null)
        }), o.makeCluster()
    }, this.resetMarkers = function () {
        o.markers.map(function (o) {
            o.setMap(null)
        }), o.markers = []
    }, this.makeCluster = function () {
        if (null !== o.cluster) o.cluster.clearMarkers(), o.cluster.addMarkers(o.markers); else {
            for (var a = [], e = 1; e <= 5; e++) a.push({
                width: 31,
                height: 38,
                url: void 0 !== window.mapClustererImage ? window.mapClustererImage : "https://itcommunity.fr/assets/images/clusterer.png",
                textColor: "white",
                textSize: 24,
                anchor: [-5, 0]
            });
            o.cluster = new MarkerClusterer(o.map, o.markers, {
                styles: a,
                maxZoom: window.amblab.features["map.clusters.max_zoom"],
                gridSize: window.amblab.features["map.clusters.grid_size"]
            })
        }
    }, this.focus = function (a, e, s) {
        (new google.maps.Geocoder).geocode({address: a}, function (a, t) {
            if (t == google.maps.GeocoderStatus.OK && t != google.maps.GeocoderStatus.ZERO_RESULTS) if (a && a[0] && a[0].geometry && a[0].geometry.viewport) {
                var n = {geocode: null, distance: 0},
                    i = new google.maps.LatLng(a[0].geometry.location.lat(), a[0].geometry.location.lng()),
                    l = {lat: a[0].geometry.location.lat(), lng: a[0].geometry.location.lng()};
                for (var r in s && s(l), e) {
                    var c = new google.maps.LatLng(e[r].latitude, e[r].longitude),
                        d = google.maps.geometry.spherical.computeDistanceBetween(c, i);
                    (null == n.geocode || d < n.distance) && (n.geocode = c, n.distance = d)
                }
                var m = new google.maps.LatLngBounds;
                if (m.extend(i), null != n.geocode) {
                    var p = n.geocode.lat() - i.lat(), u = n.geocode.lng() - i.lng(),
                        h = new google.maps.LatLng(i.lat() - p, i.lng() - u);
                    m.extend(n.geocode), m.extend(h)
                }
                o.map.fitBounds(m), o.map.setZoom(14)
            } else a && a[0] && a[0].geometry && a[0].geometry.bounds && o.map.fitBounds(a[0].geometry.bounds)
        })
    }, this.lazyLoad = function () {
        $(o.element).find("img[data-src]").each(function () {
            $(this).is(":visible") && $(this).attr("src", $(this).attr("data-src"))
        })
    }, this.zoomControl = function (o, a) {
        var e = document.createElement("div");
        e.className = "map-zoom", o.appendChild(e);
        var s = document.createElement("button");
        s.innerHTML = "+", e.appendChild(s);
        var t = document.createElement("button");
        t.innerHTML = "-", e.appendChild(t), s.addEventListener("click", function () {
            a.setZoom(a.getZoom() + 1)
        }), t.addEventListener("click", function () {
            a.setZoom(a.getZoom() - 1)
        })
    }
}, HomeAmbassadorsMap = function () {
    var o = this;
    o.$element = $(".js-home-ambassadors-map"), o.map = new CustomMap, o.map.init(o.$element.get(0), window.homeAmbassadorsMapZoom, window.homeAmbassadorsMapCenter), o.loading = !1, o.page = 1, o.geolocation = new CustomGeolocation, o.load = function (a, e) {
        e = void 0 === e ? $(".js-map-search-submit").eq(0) : e;
        console.log("HomeAmbassadorsMap load", a), o.loading = !0, o.page = a;
        var s = {page: a};
        e.parents(".home-map-search").find("input, select").each(function () {
            if (void 0 !== $(this).attr("name") && !1 !== $(this).attr("name") && $(this).attr("name").length > 0) {
                var o = $(this).val();
                if ($(this).attr("pattern")) if (!new RegExp($(this).attr("pattern")).test(o)) return;
                2 == o.length && "50" == o && (o += "000"), s[$(this).attr("name")] = o
            }
        });
        var t = function () {
            $.ajax({
                method: "post", url: o.$element.attr("data-endpoint"), data: s, success: function (a) {
                    if (o.loading = !1, 1 == o.page) {
                        console.log(a.results), 0 == a.results.length ? ($(".js-home-ambassadors-listing").addClass("empty-result"), "sebes.ambassadorslab.com" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>No hay un Embajador con los criterios solicitados. Modificar su búsqueda.</p>") : "embajadorescompanion.es" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>No hay un Embajador con los criterios solicitados. Modificar su búsqueda.</p>") : "www.embajadorescompanion.es" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>No hay un Embajador con los criterios solicitados. Modificar su búsqueda.</p>") : "sebit.ambassadorslab.com" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>Non ci sono ambassadors con i criteri richiesti. Modifica la tua ricerca</p>") : "ambassadorcompanion.it" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>Non ci sono ambassadors con i criteri richiesti. Modifica la tua ricerca</p>") : "www.ambassadorcompanion.it" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>Non ci sono ambassadors con i criteri richiesti. Modifica la tua ricerca</p>") : "sebde.ambassadorslab.com" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>Es gibt keinen Botschafter mit den geforderten Kriterien. Ändern Sie Ihre Suche.</p>") : "prep-and-cook-botschafter.de" === window.location.hostname ? $(".js-home-ambassadors-listing").html("<p>Es gibt keinen Botschafter mit den geforderten Kriterien. Ändern Sie Ihre Suche.</p>") : $(".js-home-ambassadors-listing").html("<p>Il n'y a pas d'Ambassadeur avec les critères demandés. Modifiez votre recherche.</p>")) : ($(".js-home-ambassadors-listing").removeClass("empty-result"), $(".js-home-ambassadors-listing").html(""));
                        try {
                            o.map.setMarkers(a.markers), void 0 !== s.zipcode && null !== s.zipcode && s.zipcode.length && o.map.focus(s.zipcode + ", " + homeAmbassadorsMapCountry, a.markers, function (o) {
                            })
                        } catch (o) {
                            console.log(o)
                        }
                    } else console.log("not page 1");
                    a.results.map(function (o) {
                        $(".js-home-ambassadors-listing").append(o.html)
                    }), $(".homepage section.map a.profile-mapping-result-see-more").click(function () {
                        var o = $(".homepage section.map div.profile-holder-responsive");
                        o.length <= 10 && $(this).hide(), o.each(function (o) {
                            if (!(o < 10)) return !1;
                            $(this).show()
                        })
                    })
                }
            })
        };
        if (void 0 !== s.zipcode && null !== s.zipcode && s.zipcode.length) try {
            o.map.focus(s.zipcode + ", " + homeAmbassadorsMapCountry, [], function (o) {
                o && o.lat && o.lng ? (s.latitude = o.lat, s.longitude = o.lng) : (s.latitude = null, s.longitude = null), console.log("Contenu du formulaire après update center : ", s), t()
            })
        } catch (o) {
            console.log(o)
        } else t()
    }, o.load(1), $(".js-map-search-submit").click(function () {
        console.log(".js-map-search-submit click", "disabled=", $(this).hasClass("disabled")), $(this).hasClass("disabled") || o.load(1, $(this))
    }), $(".js-map-search-zipcode").on("keyup", function (o) {
        13 == o.keyCode && $(this).parents(".home-map-search").find(".js-map-search-submit").click()
    }), $(".js-home-map-search").on("change", "select", function () {
        console.log(".js-home-map-search select"), o.load(1)
    }), $(".js-map-search-product").change(function () {
        console.log(".js-map-search-product change"), o.updateLevels()
    }), o.sublevels = $(".js-map-search-product-sublevel").html(), o.updateLevels = function () {
        console.log("updateLevels "), "_" == $(".js-map-search-product").val() ? $(".js-map-search-product-sublevel").parent().hide() : ($(".js-map-search-product-sublevel").html(o.sublevels), $(".js-map-search-product-sublevel option").each(function () {
            $(this).attr("data-parent") != $(".js-map-search-product").val() && "_" != $(this).val() && $(this).remove()
        }), $(".js-map-search-product-sublevel").parent().show(), window.updateCustomControls())
    }, o.updateLevels(), $("body").hasClass("TRYBA") && (o.levels = $(".js-map-search-product").html(), $(".js-map-search-environment_type").change(function () {
        $(".js-map-search-product").html(o.levels), "appartment" == $(this).val() && $(".js-map-search-product").each(function () {
            "_" != $(this).val() && -1 !== ["doors", "garage_doors"].indexOf($(this).attr("data-name")) && $(this).remove()
        }), window.updateCustomControls(), o.updateLevels()
    })), $("body").hasClass("SEB") && (o.products = $(".js-map-search-product").html(), $(".js-map-search-connected").change(function () {
        if ($(".js-map-search-product").html(o.products), "_" != $(this).val()) {
            var a = $(this).val().split(",");
            $(".js-map-search-product option").each(function () {
                "_" != $(this).val() && -1 === a.indexOf($(this).val()) && $(this).remove()
            })
        }
        window.updateCustomControls(), o.load(1)
    })), console.log(window.amblab.features["map.geoloc"]), window.amblab.features["map.geoloc"] && (console.log("Geolocation..."), o.geolocation.getCurrentPosition(!1, function (a, e) {
        if (a) return $(".js-geolocation").removeClass("disabled"), !1;
        console.log(e);
        var s = new google.maps.LatLng(e.latitude, e.longitude);
        (new google.maps.Geocoder).geocode({latLng: s}, function (a, e) {
            var s = !1;
            if (e == google.maps.GeocoderStatus.OK && a[0]) for (j = 0; j < a[0].address_components.length; j++) "postal_code" == a[0].address_components[j].types[0] && ($(".js-map-search-zipcode").val(a[0].address_components[j].short_name), o.load(1), s = !0);
            s ? "embajadorescompanion.es" === window.location.hostname ? o.geolocation.notify("Posición detectada") : "sebes.ambassadorslab.com" === window.location.hostname ? o.geolocation.notify("Posición detectada") : "sebit.ambassadorslab.com" === window.location.hostname ? o.geolocation.notify("Posizione rilevata") : "ambassadorcompanion.it" === window.location.hostname ? o.geolocation.notify("Posizione rilevata") : "sebde.ambassadorslab.com" === window.location.hostname ? o.geolocation.notify("Position erkannt") : "prep-and-cook-botschafter.de" === window.location.hostname ? o.geolocation.notify("Position erkannt") : o.geolocation.notify("Position détectée") : "embajadorescompanion.es" === window.location.hostname ? o.geolocation.notify("Error al localizar") : "sebes.ambassadorslab.com" === window.location.hostname ? o.geolocation.notify("Error al localizar") : "sebit.ambassadorslab.com" === window.location.hostname ? o.geolocation.notify("Impossibile localizzare") : "ambassadorcompanion.it" === window.location.hostname ? o.geolocation.notify("Impossibile localizzare") : "sebde.ambassadorslab.com" === window.location.hostname ? o.geolocation.notify("Fehler beim Suchen") : "prep-and-cook-botschafter.de" === window.location.hostname ? o.geolocation.notify("Fehler beim Suchen") : o.geolocation.notify("Échec de la localisation"), $(".js-geolocation").removeClass("disabled")
        })
    })), $(".js-home-ambassadors-listing").scroll(function (a) {
        var e = $(this).get(0).scrollHeight - $(this).get(0).scrollTop, s = $(this).get(0).offsetHeight - e;
        0 != s && 1 != s || o.loading || o.load(o.page + 1)
    })
}, DealersMap = function () {
    var o = this;
    o.$element = $(".dealers-map"), o.map = new CustomMap, o.map.init(o.$element.get(0)), o.load = function () {
        var a = $(".js-map-search-zipcode").val();
        $.ajax({
            method: "post", url: o.$element.attr("data-endpoint"), data: {zipcode: a}, success: function (e) {
                o.map.setMarkers(e.markers), a.length && o.map.focus(a + ", " + homeAmbassadorsMapCountry, e.markers, null)
            }
        })
    }, o.load(), $(".js-map-search-submit").click(function () {
        o.load()
    })
}, AmbassadorsMap = function () {
    var o = this;
    o.$element = $(".js-ambassadors-map"), o.map = new CustomMap, o.map.init(o.$element.get(0)), o.page = 1, o.id = o.$element.attr("data-id");
    var a = {page: o.page};
    a.id = o.id, o.load = function () {
        console.log(a), $.ajax({
            method: "post",
            url: $(".js-ambassadors-map").attr("data-endpoint"),
            data: a,
            success: function (a) {
                o.map.setMarkers(a.markers), $(".js-ambassadors-listing").html(""), a.results.map(function (o) {
                    $(".js-ambassadors-listing").append(o.html)
                })
            }
        })
    }, o.load(1)
}, CustomGeolocation = function () {
    var o = this;
    $(".js-geolocation-status").length || $("body").append('<div class="geolocation-status"><span class="js-geolocation-status"></span></div>'), o.$status = $(".js-geolocation-status"), o.statusTimeout = null, o.notify = function (a, e) {
        clearTimeout(o.statusTimeout), o.$status.text(a), o.$status.fadeIn(), e || (o.statusTimeout = setTimeout(function () {
            o.$status.fadeOut()
        }, 3e3))
    }, o.check = function () {
        return !!navigator.geolocation || (o.notify("Votre navigateur ne dispose pas de cette fonctionnalité"), !1)
    }, o.getCurrentPosition = function (a, e) {
        if (!o.check()) return e(!0);
        "embajadorescompanion.es" === window.location.hostname ? o.notify("Ubicación...", !0) : "sebes.ambassadorslab.com" === window.location.hostname ? o.notify("Ubicación...", !0) : "sebit.ambassadorslab.com" === window.location.hostname ? o.notify("Posizione...", !0) : "ambassadorcompanion.it" === window.location.hostname ? o.notify("Posizione...", !0) : "sebde.ambassadorslab.com" === window.location.hostname ? o.notify("Lage...", !0) : "prep-and-cook-botschafter.de" === window.location.hostname ? o.notify("Lage...", !0) : o.notify("Localisation...", !0), navigator.geolocation.getCurrentPosition(function (s) {
            a && ("embajadorescompanion.es" === window.location.hostname ? o.notify("Posición detectada") : "sebes.ambassadorslab.com" === window.location.hostname ? o.notify("Posición detectada") : "sebit.ambassadorslab.com" === window.location.hostname ? o.notify("Posizione rilevata") : "ambassadorcompanion.it" === window.location.hostname ? o.notify("Posizione rilevata") : "sebde.ambassadorslab.com" === window.location.hostname ? o.notify("Position erkannt") : "prep-and-cook-botschafter.de" === window.location.hostname ? o.notify("Position erkannt") : o.notify("Position détectée")), e(!1, s.coords)
        }, function (a) {
            switch (a.code) {
                case a.PERMISSION_DENIED:
                    "embajadorescompanion.es" === window.location.hostname ? o.notify("Primero debes permitir el acceso a tu ubicación.") : "sebes.ambassadorslab.com" === window.location.hostname ? o.notify("Primero debes permitir el acceso a tu ubicación.") : "sebit.ambassadorslab.com" === window.location.hostname ? o.notify("Devi prima consentire l'accesso alla tua posizione") : "ambassadorcompanion.it" === window.location.hostname ? o.notify("Devi prima consentire l'accesso alla tua posizione") : "sebde.ambassadorslab.com" === window.location.hostname ? o.notify("Sie müssen zuerst den Zugriff auf Ihren Standort zulassen") : "prep-and-cook-botschafter.de" === window.location.hostname ? o.notify("Sie müssen zuerst den Zugriff auf Ihren Standort zulassen") : o.notify("Vous devez d'abord autoriser l'accès à votre position");
                    break;
                case a.POSITION_UNAVAILABLE:
                    "embajadorescompanion.es" === window.location.hostname ? o.notify("Servicio de localización no disponible.") : "sebes.ambassadorslab.com" === window.location.hostname ? o.notify("Servicio de localización no disponible.") : "sebit.ambassadorslab.com" === window.location.hostname ? o.notify("Servizio di localizzazione non disponibile") : "ambassadorcompanion.it" === window.location.hostname ? o.notify("Servizio di localizzazione non disponibile") : "sebde.ambassadorslab.com" === window.location.hostname ? o.notify("Standortdienst nicht verfügbar") : "prep-and-cook-botschafter.de" === window.location.hostname ? o.notify("Standortdienst nicht verfügbar") : o.notify("Service de localisation indisponible");
                    break;
                default:
                    "embajadorescompanion.es" === window.location.hostname ? o.notify("Error al localizar") : "sebes.ambassadorslab.com" === window.location.hostname ? o.notify("Error al localizar") : "sebit.ambassadorslab.com" === window.location.hostname ? o.notify("Impossibile localizzare") : "ambassadorcompanion.it" === window.location.hostname ? o.notify("Impossibile localizzare") : "sebde.ambassadorslab.com" === window.location.hostname ? o.notify("Fehler beim Suchen") : "prep-and-cook-botschafter.de" === window.location.hostname ? o.notify("Fehler beim Suchen") : o.notify("Échec de la localisation")
            }
            e(!0)
        })
    }
};
