jQuery(function ($) {
    var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);

    document.addEventListener("smartbanner.init", function () {
        $("html").css("margin-top", "0 !important");
        setTimeout(function () {
            var currentOptions = smartbanner.options;
            if (typeof currentOptions !== "undefined") {
                var platform = smartbanner.platform;
                var html = "";
                if (platform === "ios") {
                    html += "<a href='javascript:void(0);' class='smartbanner__exit js_smartbanner__exit' aria-label='" + smartbanner.closeLabel + "'></a>";
                }
                if (typeof smartbanner.icon !== "undefined") {
                    let onClick = 'window.open("' + smartbanner.buttonUrl + '", "_blank")';
                    html += "<div class='smartbanner__icon' style='background-image: url(" + smartbanner.icon + ");' onclick='" + onClick + "'></div>";
                }
                html += "<div class='smartbanner__info'><div>";
                if (typeof currentOptions.title !== "undefined") {
                    html += "<div class='smartbanner__info__title'>" + currentOptions.title + "</div>";
                }
                if (typeof currentOptions.author !== "undefined") {
                    html += "<div class='smartbanner__info__author'>" + currentOptions.author + "</div>";
                }
                if (typeof currentOptions.price !== "undefined") {
                    //html += "<div class='smartbanner__info__price'>" + currentOptions.price + smartbanner.priceSuffix + "</div>";
                }
                if (platform === 'ios') {
                    html += "<div class='smartbanner__info__star'><span></span></div>";
                }
                html += "</div></div>";
                if (platform === 'android') {
                    currentOptions.button = 'INSTALL';
                    html += "<a href='javascript:void(0);' class='smartbanner__close js_smartbanner__exit' aria-label='" + smartbanner.closeLabel + "'>No Thanks</a>";
                }
                if (typeof smartbanner.buttonUrl !== "undefined" && typeof currentOptions.button !== "undefined") {
                    html += "<a href='" + smartbanner.buttonUrl + "' target='_blank' class='smartbanner__button js_smartbanner__button' rel='noopener' aria-label='" + currentOptions.button + "'>" +
                        "<span class='smartbanner__button__label'>" + currentOptions.button + "</span>" +
                        "</a>";
                }

                if ((!isSafari && iOS()) || !iOS()) {
                    smartbanner.publish();
                }

                $("body").find(".smartbanner").prependTo("body").html(html).show();
                let stickyHeader = window.msabStickyElement;
                if (stickyHeader) {
                    if ($(stickyHeader).length && ("fixed" === $(stickyHeader).css("position") || "flex" === $(stickyHeader).css("position")) && $(".smartbanner").length) {
                        let bannerHeight = $(".smartbanner").innerHeight(),
                            bodyMargin = $('#wpadminbar').length > 0 ? bannerHeight - 46 : bannerHeight;
                        $("<div class='msab-spacer'></div>").insertBefore(stickyHeader);
                        $("body").addClass("msab-active");
                        let styleForStickyHeader = "<style type='text/css' id='msab-custom-css'>";
                        styleForStickyHeader += ".msab-active{padding-top:" + bodyMargin + "px;}";
                        styleForStickyHeader += ".smartbanner{position:absolute !important;}";
                        styleForStickyHeader += stickyHeader + "{top:" + bannerHeight + "px;}";
                        styleForStickyHeader += "</style>";
                        $("head").append(styleForStickyHeader);

                        $(window).scroll(function () {
                            let stickyHeaderTop = (bannerHeight - window.pageYOffset) > 0 ? bannerHeight - window.pageYOffset : 0;
                            if (!$("body").find(".smartbanner").length) {
                                stickyHeaderTop = 0;
                            }
                            $(".smartbanner").css({"top": "-" + window.pageYOffset});
                            $(stickyHeader).css({"top": stickyHeaderTop});

                        })
                    }

                }
                //Add event to close banner on Android
                var closeIcon = document.querySelector(".js_smartbanner__exit");
                if (closeIcon) {
                    closeIcon.addEventListener("click", function (event) {
                        $(".msab-spacer").remove();
                        $(".msab-active").css({"margin-top": ""});
                        $("body").removeClass("msab-active");
                        $(stickyHeader).css({"top": ""});
                        $("#msab-custom-css").remove();
                        smartbanner.exit();
                    });
                }
            }
        }, 100);
    });

    function iOS() {
        return [
                "iPad Simulator",
                "iPhone Simulator",
                "iPod Simulator",
                "iPad",
                "iPhone",
                "iPod"
            ].includes(navigator.platform)
            // iPad on iOS 13 detection
            || (navigator.userAgent.includes("Mac") && "ontouchend" in document && window.navigator.userAgent.indexOf("Mobile") === -1)
    }
});