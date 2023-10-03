jQuery(function ($) {
    $window = $(window);
    $wpwrap = $('#wpwrap');
    $adminmenu = $('#adminmenu');
    $body = $('body');
    $scrollbarwidth = 10;//17 for the default scrollbar
    mobile_width = 781;
    autofold_width = 960;
    admin_menu_editor_always_open_element = '.ws-ame-has-always-open-submenu';
    
    $adminmenu.data('wp-responsive', true);//disable wordpress sticky system
    
    //align the sub menu on scroll
    $adminmenu.on('scroll', function () {
        if ($adminmenu.find('li.opensub').length) {
            var $menuItemTemp = $adminmenu.find('li.opensub');
            var menuItemTempPos = $menuItemTemp.position();
            $menuItemTemp.find('.wp-submenu').css({
                top: menuItemTempPos.top,
            });
        }
    });
    
    // whenever we hover over a menu item that has a submenu and not an always open menu

    $adminmenu.find('>li').not(admin_menu_editor_always_open_element).on('mouseover', function () {

        if ($window.outerWidth() > mobile_width) {

            $adminmenu.find('li.opensub').removeClass('opensub');


            var $menuItem = $(this),
                    $submenuWrapper = $('> .wp-submenu', $menuItem);

            if (!$menuItem.hasClass('wp-menu-open') || $body.hasClass('folded') || $window.outerWidth() <= autofold_width && $body.hasClass('auto-fold')) {
                // grab the menu item's position relative to its positioned parent
                var menuItemPos = $menuItem.position();
                // place the submenu in the correct position relevant to the menu item
                $submenuWrapper.css({
                    top: menuItemPos.top,
                    left: menuItemPos.left + Math.round($menuItem.outerWidth()) //scrollbar
                });
                adjustSubmenu($menuItem);
                $menuItem.addClass('opensub');

            }

        }
    });

    $adminmenu.find('>li').on('mouseout', function () {
        if ($window.outerWidth() > mobile_width) {
            closeSubMenus();
            $adminmenu.find('>li').removeClass('opensub');
        }

    });

    function closeSubMenus() {
        if ($body.hasClass('folded')) {
            $adminmenu.find('>li').not(admin_menu_editor_always_open_element).find(' > .wp-submenu').css({top: '-1000em', left: 'auto'});
        } else {
            if ($window.outerWidth() <= autofold_width && $body.hasClass('auto-fold')) {
                $adminmenu.find('>li').not(admin_menu_editor_always_open_element+' , .wp-current-submenu').find(' > .wp-submenu').css({top: '-1000em', left: 'auto'});

            } else {
                $adminmenu.find('>li.wp-not-current-submenu').not(admin_menu_editor_always_open_element).find(' > .wp-submenu').css({top: '-1000em', left: 'auto'});
            }


        }
    }
    function adjustSubmenu($menuItem) {
        var bottomOffset, pageHeight, adjustment, theFold, menutop, wintop, maxtop,
                $submenu = $menuItem.find('.wp-submenu');

        menutop = $menuItem.offset().top;
        wintop = $window.scrollTop();
        maxtop = menutop - wintop - 30; // max = make the top of the sub almost touch admin bar.

        bottomOffset = menutop + $submenu.height() + 1;
        pageHeight = $wpwrap.height();
        adjustment = 60 + bottomOffset - pageHeight;
        theFold = $window.height() + wintop - 40;       // -40 (distance from the bottom)

        if (theFold < (bottomOffset - adjustment)) {
            adjustment = bottomOffset - theFold;
        }

        if (adjustment > maxtop) {
            adjustment = maxtop;
        }

        if (adjustment > 1 && $('#wp-admin-bar-menu-toggle').is(':hidden')) {
            $submenu.css('margin-top', '-' + adjustment + 'px');
        } else {
            $submenu.css('margin-top', '');
        }
    }
});