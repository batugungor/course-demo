jQuery(document).ready(function ($) {
    const $activeElement = $('.nwire-dev-learn-management-system--course-sidebar-section-lesson-active');
    const wp_admin_bar_height = $("#wpadminbar").length > 0 ? $("#wpadminbar").height() : 0;


    if ($activeElement.length > 0) {
        var scrollPosition = $activeElement.position().top;
        console.log(scrollPosition)
        $('.nwire-dev-learn-management-system--course-sidebar-sections').scrollTop(scrollPosition - 400);
    }

    let isMobile = $(window).width() <= 1400;
    const content_body = $(".nwire-dev-learn-management-system--course-main-container");
    const sidebar_body = $("aside");
    const toggle = $(".nwire-dev-learn-management-system--sidebar-toggle");

    let sidebar = !(isMobile);

    toggle.on("click", function () {
        if (isMobile) {
            if (sidebar) {

               sidebar_body.toggleClass('sidebar-toggled');
                sidebar = false;
                return;
            }

            if (!sidebar) {

                sidebar_body.toggleClass('sidebar-toggled');
                sidebar = true;
                return;
            }
        }

        if (!isMobile) {
            if (sidebar) {
                $(this).find('img').attr("src", $(this).data('icon-expand'));

                sidebar_body.attr('style', 'margin-left: -13vw !important');
                content_body.attr('style', 'padding-left: 0 !important; width: 1350px; padding-right: 150px !important');


                sidebar = false;
                sidebar_body.addClass("nwire-dev-learn-management-tool-sidebar-toggled")
                return;
            }

            if (!sidebar) {

                $(this).find('img').attr("src",$(this).data('icon-minify'));
                // sidebar_body.attr('style', 'position: absolute !important; margin-left: 0 !important; top: calc(var(--navbar-height) + ' + wp_admin_bar_height + 'px)' );
                sidebar_body.attr('style', 'margin-left: 0 !important');

                content_body.attr('style', '');

                // content_body.css('padding-right', '5em');

                sidebar_body.removeClass("nwire-dev-learn-management-tool-sidebar-toggled")
                sidebar = true;
                return;
            }
        }
    });

    $(window).on('resize', function () {
        var win = $(this);

        isMobile = win.width() <= 1200;

        if (isMobile) {
            if (sidebar) {

            }

            if (!sidebar) {

            }
        }

    });

    // $(".nwire-dev-learn-management-system--sidebar-toggle").on("click", function () {
    //     if (isMobile) {
    //         if (sidebar) {
    //             $(".nwire-dev-learn-management-tool--sidebar").css('margin-left', '-100vw');
    //             sidebar = false;
    //         } else {
    //             $(".nwire-dev-learn-management-tool--sidebar").css('margin-left', '0');
    //             sidebar = true;
    //         }
    //     } else {
    //         if (sidebar) {
    //             $(".nwire-dev-learn-management-tool--sidebar").css('margin-left', '-12vw');
    //             $(".nwire-dev-learn-management-tool--sidebar-main").css('padding-margin-left', '10em');
    //             $(".nwire-dev-learn-management-tool--sidebar-main").css('padding-right', '10em');
    //
    //             sidebar = false;
    //         } else {
    //             $(".nwire-dev-learn-management-tool--sidebar").css('margin-left', '0');
    //             $(".nwire-dev-learn-management-tool--sidebar-main").css('padding-margin-left', '15em');
    //             $(".nwire-dev-learn-management-tool--sidebar-main").css('padding-right', '5em');
    //             sidebar = true;
    //
    //         }
    //     }


    // });
});
