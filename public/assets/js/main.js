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
    }),$("#contact-conseil").click(function () {
        console.log($("#contact-conseil-popup").toggle()), $("#contact-conseil-popup").toggle();
        var e = $("#contact-conseil-popup input[name=is_public_message]");
        e.prop("checked", !0);
        var t = e.parent("span.jcf-checkbox");
        t.removeClass("jcf-unchecked"), t.hasClass("jcf-checked") || t.addClass("jcf-checked")
    }))

    function l(e, t) {
        e.parent().find(".slider-label").html(t), e.parent().trigger("slide", [t])
    }

        (jQuery), $(document).ready(function () {
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
}),  $(document).ready(function () {
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
});
