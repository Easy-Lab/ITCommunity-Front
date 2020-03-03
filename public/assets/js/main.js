var Box = function (e, t, n) {
    var o = this, a = $(".boxes"), i = a.find("." + e);
    (null == t || t.length < 1) && (t = i.find("p").attr("data-default")), (null == n || n.length < 1) && (n = i.find("header").attr("data-default")), i.find(".btn-confirm").prop("onclick", null).off("click").on("click", function () {
        o.hide()
    }), i.find(".btn-cancel").prop("onclick", null).off("click").on("click", function () {
        o.hide()
    }), i.find(".btn-close").prop("onclick", null).off("click").on("click", function () {
        o.hide()
    }), this.show = function () {
        a.children().hide(), i.find("p").text(t), i.find("header").text(n), a.css("display", "flex"), i.show()
    }, this.onConfirm = function (e) {
        i.find(".btn-confirm").prop("onclick", null).off("click").on("click", function () {
            e(o)
        })
    }, this.onCancel = function (e) {
        i.find(".btn-cancel").prop("onclick", null).off("click").on("click", function () {
            e(o)
        })
    }, this.onClose = function (e) {
        i.find(".btn-cancel").prop("onclick", null).off("click").on("click", function () {
            e(o)
        })
    }, this.hide = function () {
        i.hide(), a.css("display", "none")
    }
};

function updatePhoneInputVisibility() {
    $("#is_reachable_by_phone").is(":checked") || $("#is_reachable_by_sms").is(":checked") ? $("#phoneInput").show() : $("#phoneInput").hide()
}

function updatetextPhoneInputVisibility() {
    $("#is_reachable_by_phone").is(":checked") || $("#is_reachable_by_sms").is(":checked") ? $("#phoneTextInput").show() : $("#phoneTextInput").hide()
}

function updateFbUrlVisibility() {
    $("#is_reachable_by_messenger").is(":checked") ? $("#facebook_url").show() : $("#facebook_url").hide()
}

function hideAllPopupStatus() {
    $("#contact-mail-popup, #contact-phone-popup, #contact-sms-popup, #contact-messenger-popup, #contact-conseil-popup, #contact-demo-popup").hide(), $("#contact-mail, #contact-phone, #contact-sms, #contact-messenger, #contact-conseil, #contact-demo").removeClass("contact-btn-selected")
}

function manageTooltips() {
    $.widget("ui.tooltip", $.ui.tooltip, {
        options: {
            content: function () {
                return $(this).prop("title")
            }
        }
    }), $('a.show-tooltip[data-toggle="tooltip"]').tooltip({
        animated: "fade",
        placement: "bottom",
        html: !0,
        container: "body"
    })
}

window.updateControls = function () {
    $("select").each(function () {
        var e = !0;
        $(this).find("option:not(:first-child)").each(function () {
            $(this).prop("selected") && $(this).hasClass("js-control-analyzed") && (e = !1), $(this).addClass("js-control-analyzed")
        }), $(this).attr("data-selected") || $(this).attr("data-value") || !e || $(this).find("option").first().is(":disabled") && $(this).find("option").first().prop("selected", !0)
    })
}, window.updateCustomControls = function () {
    window.updateControls(), jcf.replace(".custom-control"), jcf.refresh(".custom-control"), jcf.replace("input[type=checkbox]"), jcf.refresh("input[type=checkbox]")
}, $(window).ready(function () {

    var e, t, n, o, a, i = $(window).width(), s = $(".sticky-part-holder"), c = s.length ? s.offset().top : 0;
    if ($(".contact-btn, #contact-popup-close").click(function () {
        var e = "#" + this.id + "-popup", t = $("#contact-mail-popup input[name=is_public_message]");
        if ("contact-mail" == this.id && t.length > 0) {
            t.prop("checked", !1);
            var n = t.parent("span.jcf-checkbox");
            n.removeClass("jcf-checked"), n.hasClass("jcf-checked") && n.addClass("jcf-unchecked")
        }
        if ($(e).is(":visible")) hideAllPopupStatus(), $("body").css("overflow", "auto"); else {
            hideAllPopupStatus(), $(e).show(), $(this).is("#contact-popup-close") || ($(this).addClass("contact-btn-selected"), $("body").css("overflow", "auto"));
            try {
                var o = $(".sticky-part-holder.scroll-wrapper.sticky-scroll"), a = $(e);
                if (o.length > 0) {
                    var l = a.offset().top - a.offsetParent().offset().top;
                    o.stop().animate({scrollTop: l}), $("body").css("overflow", "auto")
                } else $("html, body").stop().animate({scrollTop: $(".sticky-part-holder").offset().top + 50}, 300, "swing", function () {
                    sticky_scroll(i, s, c), $("body").css("overflow", "auto")
                })
            } catch (e) {
            }
        }
    }), $("#contact-public").click(function () {
        $("#contact-mail-popup").toggle();
        var e = $("#contact-mail-popup input[name=is_public_message]");
        e.prop("checked", !0);
        var t = e.parent("span.jcf-checkbox");
        t.removeClass("jcf-unchecked"), t.hasClass("jcf-checked") || t.addClass("jcf-checked")
    }), $("#contact-conseil").click(function () {
        console.log($("#contact-conseil-popup").toggle()), $("#contact-conseil-popup").toggle();
        var e = $("#contact-conseil-popup input[name=is_public_message]");
        e.prop("checked", !0);
        var t = e.parent("span.jcf-checkbox");
        t.removeClass("jcf-unchecked"), t.hasClass("jcf-checked") || t.addClass("jcf-checked")
    }), $(".event-register").click(function () {
        $(this).next("div.event-register-popup").toggle()
    }), $(".register-popup-close").click(function () {
        $(this).find("div.event-register-popup").toggle()
    }), $(".js-facebook-url").keyup(function () {
        $(this).val().length > 0 ? $("#is_reachable_by_messenger").prop("checked", !0) : $("#is_reachable_by_messenger").prop("checked", !1)
    }), $(".js-facebook-login").length) {
        window.fbAsyncInit = function () {
            FB.init({
                appId: $(".js-facebook-login").attr("data-facebook-app-id"),
                cookie: !0,
                xfbml: !0,
                version: "v2.12"
            }), FB.AppEvents.logPageView()
        }, e = document, t = "script", n = "facebook-jssdk", a = e.getElementsByTagName(t)[0], e.getElementById(n) || ((o = e.createElement(t)).id = n, o.src = "https://connect.facebook.net/en_US/sdk.js", a.parentNode.insertBefore(o, a))
    }

    function l(e, t) {
        e.parent().find(".slider-label").html(t), e.parent().trigger("slide", [t])
    }

    $("[data-background-url]").each(function () {
        $(this).css("background-image", "url(" + $(this).attr("data-background-url") + ")")
    }), $(".js-serial-number-container").length && function (e) {
        var t = e(".js-serial-number-container");

        function n() {
            t.html("")
        }

        function o(o) {
            t.text("Chargement..."), e.getJSON(t.attr("data-endpoint"), {
                family: o,
                serial: t.attr("data-serial")
            }, function (e) {
                e.success ? (t.html(e.html), window.updateCustomControls(), t.attr("data-serial", "")) : n()
            })
        }

        e(document).on("change", ".js-product-family", function () {
            n(), 1 == e(this).find("option:selected").attr("data-registrable") && o(e(this).val())
        }), 1 == e(".js-product-family").last().find("option:selected").attr("data-registrable") && o(e(".js-product-family").last().val())
    }(jQuery), $(".js-product-families").length && function (e) {
        var t = e(".js-product-families");

        function n(o) {
            t.find("select").removeAttr("name"), t.find("select").last().attr("name", "product_family");
            var a = o.find("option:selected"), i = a.length && 1 == a.attr("data-registrable");
            !i && null != o.val() && e.ajax({
                type: "POST",
                url: t.attr("data-endpoint"),
                data: {id: o.val()},
                dataType: "json",
                success: function (e) {
                    !function (e, o) {
                        t.append('<div class="form-group"><select class="js-product-family form-control custom-control" data-level="' + e + '"></select></div>');
                        var a = t.find(".js-product-family").last();
                        a.append("<option disabled>Choisir</option>"), o.map(function (e) {
                            a.append('<option value="' + e.id + '" data-registrable="' + (e.registrable ? 1 : 0) + '">' + e.label + "</option>")
                        }), window.updateCustomControls(), n(t.find("select").last())
                    }(parseInt(o.attr("data-level")) + 1, e)
                }
            }), i ? e(".js-product-family-submit").prop("disabled", !1) : e(".js-product-family-submit").prop("disabled", !0)
        }

        1 == t.attr("data-multiple") && (e(".js-product-family-submit").prop("disabled", !0), e(document).on("change", ".js-product-family", function () {
            var o = e(this).attr("data-level");
            t.find("[data-level]").each(function () {
                e(this).attr("data-level") > o && e(this).closest(".form-group").remove()
            }), n(e(this))
        }))
    }(jQuery), $(".js-dealer-selector").length && function (e) {
        var t = e(".js-dealer-selector");

        function n() {
            t.find(".js-dealer-zipcode-selector").length && ("_custom" == t.find(".js-dealer").val() ? t.find(".js-dealer-zipcode-selector").length && t.find(".js-dealer-zipcode-selector").show() : (t.find(".js-dealer-zipcode-selector").length && t.find(".js-dealer-zipcode-selector").hide(), t.find(".js-custom-dealer-selector").hide())), t.find(".js-commercial").length || e(".js-require-dealer").prop("disabled", !1), a()
        }

        function o() {
            if (!t.find(".js-dealer-zipcode-selector").length) return !1;
            t.find(".js-custom-dealer-selector").hide(), t.find(".js-commercial-selector").length && t.find(".js-commercial-selector").hide();
            var n = t.find(".js-dealer-zipcode").val();
            e.post(t.find(".js-dealer-zipcode-selector").attr("data-endpoint"), {zipcode: n}, function (n) {
                n.success && (t.find(".js-custom-dealer option:not(:first-child)").remove(), n.dealers.map(function (e) {
                    t.find(".js-custom-dealer").append('<option value="' + e.id + '">' + e.pseudo + " - " + e.city + "</option>")
                }), t.find(".js-custom-dealer-selector").show(), window.updateCustomControls(), t.find(".js-commercial").length ? a() : e(".js-require-dealer").prop("disabled", !e(".js-custom-dealer").val()))
            })
        }

        function a() {
            if (!t.find(".js-commercial-selector").length) return !1;
            e(".js-require-dealer").prop("disabled", !1), t.find(".js-commercial-selector").hide();
            var n = "_custom" == t.find(".js-dealer").val() ? t.find(".js-custom-dealer").val() : t.find(".js-dealer").val();
            if (null === n || !n.length) return !1;
            console.log(t.find(".js-commercial-selector").attr("data-endpoint"), {dealer: n}), e.post(t.find(".js-commercial-selector").attr("data-endpoint"), {dealer: n}, function (n) {
                if (console.log(n), n.success) {
                    t.find(".js-commercial").find("option:not(:first-child)").remove();
                    var o, a, i, s = n.commercials.length, c = 0;
                    n.commercials.map(function (e) {
                        c += 1, "autre" == e.firstname || "autre" == e.lastname || "Autre" == e.firstname || "Autre" == e.lastname ? (o = e.id, a = e.firstname, i = e.lastname) : t.find(".js-commercial").append('<option value="' + e.id + '">' + e.firstname + " " + e.lastname + "</option>"), c == s && t.find(".js-commercial").append('<option value="' + o + '">' + a + " " + i + "</option>")
                    }), 0 == n.commercials.length ? (t.find(".js-commercial-selector").hide(), e(".js-require-dealer").prop("disabled", !1)) : (window.updateCustomControls(), t.find(".js-commercial-selector").show(), t.find(".js-commercial").val(t.find(".js-commercial").attr("data-selected")), window.updateCustomControls(), t.find(".js-commercial").val() && e(".js-require-dealer").prop("disabled", !1))
                }
            })
        }

        t.find(".js-dealer").change(function () {
            n()
        }), t.find(".js-dealer-zipcode").length && (t.find(".js-dealer-zipcode").keydown(function (e) {
            if (13 == e.keyCode) return e.preventDefault(), o(), !1;
            t.find(".js-custom-dealer-selector").hide(), t.find(".js-commercial-selector").hide()
        }), t.find(".js-dealer-search").click(function (e) {
            return e.preventDefault(), o(), !1
        }), t.find(".js-custom-dealer-selector").hide()), t.find(".js-commercial").length ? (t.find(".js-commercial").change(function () {
            e(".js-require-dealer").prop("disabled", !e(this).val())
        }), t.find(".js-custom-dealer").change(function () {
            a()
        })) : t.find(".js-custom-dealer").change(function () {
            e(".js-require-dealer").prop("disabled", !e(this).val())
        }), t.find(".js-dealer").val() && "_custom" != t.find(".js-dealer").val() && (t.find(".js-commercial").length && !t.find(".js-commercial").val() || e(".js-require-dealer").prop("disabled", !1)), a(), n()
    }(jQuery), $(document).ready(function () {
        $(".add-product-serial-number").trigger("click")
    }), $(".add-product-serial-number").click(function () {
        var e = $(this).attr("data-id");
        $("#edit-serial-number-holder-" + e).toggle(), "sebde.ambassadorslab.com" === window.location.hostname ? $(this).text($("#edit-serial-number-holder-" + e).is(":visible") ? "ABBRECHEN" : "Entrez l'identifiant") : "prep-and-cook-botschafter.de" === window.location.hostname ? $(this).text($("#edit-serial-number-holder-" + e).is(":visible") ? "ABBRECHEN" : "Entrez l'identifiant") : $(this).text($("#edit-serial-number-holder-" + e).is(":visible") ? "Annuler" : "Entrez l'identifiant"), $(this).next("button").toggle()
    }), $("#expand-add-new-product").click(function () {
        $(this).fadeOut(function () {
            $("#add-new-product-holder").fadeIn()
        })
    }), $("span.expand-edit-message").click(function () {
        $(this).siblings("p.answer").fadeOut(), $(this).fadeOut(function () {
            $(this).next("span.hide-edit-message").fadeIn(), $(this).siblings("button").fadeIn(), $(this).prev("div").fadeIn()
        })
    }), $("span.hide-edit-message").click(function () {
        $(this).next("button").fadeOut(), $(this).siblings("div").fadeOut(function () {
            $(this).siblings("p.answer").fadeIn(), $(this).siblings("span.expand-edit-message").fadeIn()
        }), $(this).fadeOut()
    });
    var r = window.navigator.userAgent.indexOf("MSIE ") > 0 || navigator.userAgent.match(/Trident.*rv\:11\./) ? "change" : "input";
    $('input[type="range"]').each(function () {
        var e = $(this);
        e.on(r, function () {
            l(e, this.value)
        }), l(e, e.val())
    })
}), $(document).ready(function () {
    var e, t, n = [];
    $("._marquee").length && (e = function () {
        var e = $("._marquee").attr("delay"), t = $("._marquee").attr("speed");
        !function e(t, o, a) {
            var i = $("._marquee-content-holder"), s = i.offset().left;
            i.offset({left: s - o});
            var c = $("._marquee").width(), l = $("._marquee-content").last();
            $("._marquee-content").each(function () {
                var e = $(this).offset().left - a;
                if ($(this).offset().left > l.offset().left && (l = $(this)), e <= 0 && ($(this).is(":first-child") && (0 == n.length ? (n.push(s), $("._marquee-content").each(function () {
                    n.push($(this).offset().left)
                })) : s < 0 && e > -o && (i.offset({left: n[0]}), $("._marquee-content").each(function (e) {
                    $(this).offset({left: n[e + 1]})
                }))), e <= -$(this).width())) {
                    var t = l.offset().left < c ? c : l.offset().left + l.width() + parseInt(l.css("marginRight")) + parseInt($(this).css("marginRight"));
                    $(this).offset({left: t})
                }
            }), setTimeout(function () {
                e(t, o, a)
            }, t)
        }(void 0 !== e && !1 !== e ? e : 30, void 0 !== t && !1 !== t ? t : 10, $("._marquee-content-holder").offset().left)
    }, t = $("._marquee-label").length ? $("._marquee-label").width() + parseInt($("._marquee-label").css("marginRight")) + parseInt($("._marquee-content-holder").css("paddingLeft")) : 0, console.log("init_marquee::offset => " + t), $("._marquee-content").each(function () {
        t += parseInt($(this).css("marginLeft")), $(this).offset({left: t}), t += $(this).width() + parseInt($(this).css("marginRight")), console.log("init_marquee::element_offset => " + $(this).offset().left)
    }), e && e())
}), $(document).ready(function () {
    (window.navigator.userAgent.indexOf("MSIE") > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) && $("input[type='date']").datepicker()
}), $(document).ready(function () {
    function e(e, t, n) {
        if (e < 992) return !1;
        var o = 1 == $("nav.fixed-top").length ? $(".fixed-top").height() : 0,
            a = $("#scrollbg").offset().top + $("#scrollbg").height(), i = $("#scrollbg").height(),
            s = window.pageYOffset + o, c = $(window).height(), l = t.offset().top, r = t.width(),
            d = t.hasClass(".scroll-wrapper") ? c : t.height(), p = i < c ? a - s : c;
        !t.hasClass("scroll-wrapper") && i > c && d > p && s > n ? (t.addClass("scroll-wrapper"), t.height(p)) : t.hasClass("scroll-wrapper") && (d < p || s < n) && (t.removeClass("scroll-wrapper"), t.css("height", "inherit")), !t.hasClass("sticky-scroll") && d <= p && (i > c || d < i) && s > n ? (t.addClass("sticky-scroll"), t.width != r && t.width(r)) : t.hasClass("sticky-scroll") && s < n && (t.removeClass("sticky-scroll"), t.hasClass("scroll-wrapper") && (t.removeClass("scroll-wrapper"), t.css("height", "inherit"))), !t.hasClass("sticky-scroll-bottom") && i > c && s > n && l + d > a ? t.addClass("sticky-scroll-bottom") : t.hasClass("sticky-scroll-bottom") && (s < n || s < l) && t.removeClass("sticky-scroll-bottom")
    }

    $("div.event-collapse").click(function () {
        $(this).next("div.event-container").toggle()
    }), $(".show-more-btn").click(function () {
        var e = $(this).siblings("div.hidden"), t = e.hasClass("col-6") ? 6 : 5;
        e.each(function (e) {
            if (!(e < t)) return !0;
            $(this).removeClass("hidden")
        }), e.length <= t && $(this).hide()
    }), window.sticky_scroll = e, function () {
        if ($(".sticky-part-holder").length > 0) {
            var t = $(window).width(), n = $(".sticky-part-holder");
            if (t > 991) {
                var o = $("#contact-mail, #contact-phone, #contact-sms, #contact-messenger, #contact-public, #contact-conseil, #contact-demo"),
                    a = n.offset().top;
                window.onscroll = function () {
                    e(t, n, a)
                }, o.click(function () {
                    e(t, n, a)
                })
            } else n.css("height", "inherit"), n.removeClass("scroll-wrapper"), n.removeClass("sticky-scroll"), n.removeClass("sticky-scroll-bottom")
        }
    }()
}), $("#evaluations-hidden-trigger").click(function () {
    $(".evaluation-hidden:lt(5)").removeClass("evaluation-hidden"), $(".evaluation-hidden").length < 1 && $(this).hide()
}), $(document).ready(function () {
    $("button.navbar-toggler").click(function () {
        var e = $(this).attr("aria-controls");
        $("div.navbar-collapse").each(function () {
            this.id != e && $(this).hasClass("show") && $(this).removeClass("show")
        })
    })
}), $(document).ready(function () {
    if ($(window).width() > 991) return !1;
    var e = $(".responsive-sticky-header"),
        t = $("#contact-mail, #contact-phone, #contact-sms, #contact-messenger, #contact-public, #contact-conseil, #contact-demo"),
        n = e.offset().top, o = e.css("top");
    t.click(function () {
        var e = window.pageYOffset, t = !1;
        $(".profile .contact .contact-popup").each(function () {
            $(this).is(":visible") && (e > n && $("body").css("overflow", "hidden"), t = !0, !0)
        }), t || ($("body").css("overflow", "auto"), !1)
    }), window.onscroll = function () {
        window.pageYOffset > n ? (e.css("display", "block"), e.css("position", "fixed"), e.css("top", "0"), e.css("padding", "5px 20px"), e.css("box-shadow", "grey 0px 2px 5px")) : (e.css("display", "none"), e.css("position", "absolute"), e.css("top", o), e.css("padding", "0"), e.css("box-shadow", "initial"))
    }
}), $(document).ready(function () {
    var e = $(".profile .contact .contact-popup form.ajax-contact-data-form");
    if (e.length > 0) {
        function t(e, t) {
            var n = e.attr("name"), o = e.val();
            t[n] = o
        }

        e.submit(function (e) {
            return e.preventDefault(), !!n($(this)) && (console.log($(".contact-data-form-holder")), function e(n, o, a, i, s) {
                if (!s) {
                    s = {};
                    var c = n.find('input[type="text"]'), l = n.find('input[type="email"]'),
                        r = n.find('input[type="checkbox"]'), d = n.find("textarea");
                    c.each(function () {
                        t($(this), s)
                    }), l.each(function () {
                        t($(this), s)
                    }), r.each(function () {
                        var e;
                        e = $(this), s[e.attr("name")] = e.is(":checked")
                    }), d.each(function () {
                        t($(this), s)
                    })
                }
                if (parent = n.closest(a), parent.length < 1) return !0;
                parent = parent.first(), console.log(s), $.ajax({
                    url: n.attr("action"),
                    type: n.attr("method"),
                    data: s,
                    dataType: "json",
                    success: function (t) {
                        console.log(t), t.success && (parent.html(t.html.content), i && a && ("string" == typeof a ? $(a) : a).each(function () {
                            var t = $(this).find(o);
                            if (t.length < 1) return !0;
                            (t = t.first()).is(n) || e(t, o, a, !1, s)
                        }))
                    },
                    error: function (e) {
                        console.log("Fail with"), console.log(e)
                    }
                })
            }($(this), "form.ajax-contact-data-form", ".contact-data-form-holder", !0, null), console.log($(this).attr("action")), !1)
        })
    }

    function n(e) {
        var t = !0;
        return e.find(".js-ambassador-contact-check-email").each(function () {
            var e = $(this).val();
            $(this).parent().find(".alert-danger").remove();
            /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(e).toLowerCase()) || (t = !1, "embajadorescompanion.es" === window.location.hostname ? $(this).parent().append('<p class="alert alert-danger mt-1">Esta dirección de correo electrónico no es válida</p>') : "sebes.ambassadorslab.com" === window.location.hostname ? $(this).parent().append('<p class="alert alert-danger mt-1">Esta dirección de correo electrónico no es válida</p>') : "sebit.ambassadorslab.com" === window.location.hostname ? 11 === e.match(/\d/g).length && 11 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Questo indirizzo e-mail non è valido</p>')) : "ambassadorcompanion.it" === window.location.hostname ? 11 === e.match(/\d/g).length && 11 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Questo indirizzo e-mail non è valido</p>')) : $(this).parent().append('<p class="alert alert-danger mt-1">Adresse e-mail invalide</p>'))
        }), e.find(".js-ambassador-contact-check-phone").each(function () {
            var e = $(this).val();
            $(this).parent().find(".alert-danger").remove(), "sebes.ambassadorslab.com" === window.location.hostname ? 9 === e.match(/\d/g).length && 9 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">El número de teléfono debe comenzar por 6 o 7 y debe contener 9 cifras</p>')) : "embajadorescompanion.es" === window.location.hostname ? 9 === e.match(/\d/g).length && 9 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">El número de teléfono debe comenzar por 6 o 7 y debe contener 9 cifras</p>')) : "sebit.ambassadorslab.com" === window.location.hostname ? 11 === e.match(/\d/g).length && 11 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Il numero di telefono deve iniziare con 03 e deve includere 11 cifre</p>')) : "ambassadorcompanion.it" === window.location.hostname ? 11 === e.match(/\d/g).length && 11 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Il numero di telefono deve iniziare con 03 e deve includere 11 cifre</p>')) : "sebde.ambassadorslab.com" === window.location.hostname ? 13 === e.match(/\d/g).length && 13 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Die Telefonnummer muss mit 01 beginnen und 13 Ziffern enthalten</p>')) : "prep-and-cook-botschafter.de" === window.location.hostname ? (e.match(/\d/g).length > 13 || e.length < 11) && (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Die Telefonnummer muss mit 01 beginnen und 13 Ziffern enthalten</p>')) : 10 === e.match(/\d/g).length && 10 === e.length || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Numéro de téléphone invalide</p>'))
        }), e.find(".js-ambassador-contact-check-zipcode").each(function () {
            var e = $(this).val(),
                n = void 0 === window.ambassadorContactZipcodesAllowed || -1 !== window.ambassadorContactZipcodesAllowed.indexOf(e) || -1 !== window.ambassadorContactZipcodesAllowed.indexOf(e.substr(0, 2));
            console.log(n, window.ambassadorContactZipcodesAllowed), $(this).parent().find(".alert-danger").remove();
            5 === e.length && /^([0-9]{5})$/.test(String(e)) ? n || (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Code postal hors zone (' + window.ambassadorContactZipcodesAllowed.join(", ") + ")</p>")) : (t = !1, $(this).parent().append('<p class="alert alert-danger mt-1">Code postal invalide</p>'))
        }), t
    }

    $(".js-ambassador-contact-check-form").each(function () {
        var e = $(this);
        e.submit(function (t) {
            if (t.preventDefault(), !n(e)) return !1;
            t.target.submit()
        })
    })
});
var userAgent = window.navigator.userAgent;
(userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) && $("body").addClass("ios-device"), $(document).ready(function () {
    $("#profile-product-js")[0] && (console.log("test"), "_custom" == $(".js-dealer-selector").find(".js-dealer").val() && $(".js-dealer-selector").find(".js-dealer-zipcode-selector").length && $(".js-dealer-selector").find(".js-custom-dealer-selector").show())
}), $(document).ready(function () {
    console.log($("[data-confirm]")), $("[data-confirm]").each(function (e) {
        var t = $(this);
        t.click(function (e) {
            if ("1" === t.attr("data-confirming")) return t.attr("data-confirming", "0"), console.log("confirmed, trigger"), !0;
            console.log("not confirmed"), e.preventDefault(), t.attr("data-confirming", "1");
            var n = new Box("confirm", t.attr("data-confirm"));
            return n.onConfirm(function (e) {
                e.hide(), console.log("confirm"), t.get(0).click()
            }), n.onCancel(function (e) {
                e.hide(), console.log("cancel"), t.attr("data-confirming", "0")
            }), n.show(), !1
        })
    })
});
