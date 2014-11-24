jQuery(document).foundation();

jQuery(document).ready(function($) {

    /* Mobile Button
    ===================================== */
    $( '.mobile-btn' ).click( function(){
        $( '.site-navigation' ).slideToggle( 200 );
        event.preventDefault();
    } );

    /* Submenu
    ===================================== */

   $( '.menu-item-has-children > a' ).click( function(){
        var $parent = $(this).parent(),
            $child = $parent.children( 'ul' );
            $child.toggleClass( 'active' );
            $parent.toggleClass('open');
            $(  '.menu-item-has-children' ).not($parent).removeClass('open');
            $( '.menu-item-has-children > ul').not($child).removeClass('active');
         if (event.preventDefault) { event.preventDefault(); } else { event.returnValue = false; } 
    });
    
    
    /* Setting the cookies
    ===================================== */

    // for top bar
    $('.cc-cookie-accept').click(function (e) {
        e.preventDefault();
        $.cookie("cc_cookie_accept", "cc_cookie_accept", {
            expires: 365,
            path: '/'
        });

        $("#cookie").fadeOut(function () {
            // reload page to activate cookies
            location.reload();
        });
    });

    //reset cookies
    $('a.cc-cookie-reset').click(function (f) {
        f.preventDefault();
        $.cookie("cc_cookie_accept", null, {
            path: '/'
        });
        $("#cookie").fadeIn(function () {
            // reload page to activate cookies
            location.reload();
        });
    });
    
});
