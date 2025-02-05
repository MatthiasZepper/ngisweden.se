jQuery(function($) {

    // Enable bootstrap tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // Override the nav search bar click event
    $('#main_navbar .wpdreams_asl_container .probox .promagnifier').click(function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if($(window).width() >= 992) {
            $('#main_navbar .wpdreams_asl_container .probox form input.orig').trigger('focus');
            $('#main_navbar .wpdreams_asl_container').addClass('ngifocus');
            $('#menu-main-navigation, #menu-main-order-btn').fadeOut(100);
        }
    });
    $('#main_navbar .wpdreams_asl_container .probox form input').blur(function(e){
        $('#main_navbar .wpdreams_asl_container').removeClass('ngifocus');
        $('#menu-main-navigation, #menu-main-order-btn').delay(400).fadeIn();
    });

    // Deep linking to pill navs
    let url = location.href.replace(/\/$/, "");
    if (location.hash) {
        const hash = url.split("#");
        if($('#'+hash[1]).length == 1 && $('#'+hash[1]).hasClass('tab-pane')){
            // Highlight the nav pill buttons
            $('.nav-pills a.nav-link').removeClass("active");
            $('.nav-pills a.nav-link[href="#'+hash[1]+'"]').addClass("active");
            // Show the content
            $('.tab-content .tab-pane').removeClass('show active');
            $('#'+hash[1]).tab('show');
            url = location.href.replace(/\/#/, "#");
            history.replaceState(null, null, url);
        }
    }
    $('a[data-toggle="pill"]').on("click", function() {
        let newUrl;
        const hash = $(this).attr("href");
        if(hash == "#home") {
            newUrl = url.split("#")[0];
        } else {
            newUrl = url.split("#")[0] + hash;
        }
        history.replaceState(null, null, newUrl);
    });
});
