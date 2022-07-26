jQuery( document ).ready( function( $ ){
    
    $( window ).resize( function() {
        setBootstrapEnvironment();
    });
    
    setBootstrapEnvironment();
    sidebar_on_top_left();
    prettyPrint();

    $('a.add-site-prefix').each(function(){
        $(this).attr( 'href', siteurl + $(this).attr( 'href' ) );
    });
    // add a top margin to the menu in desktop mode (large screens)
    // fix sidebar position when desktop viewport
    $('body').append('<style type="text/css">body.desktop .the-sidebar-container {'
        + 'margin-top:' 
        +  (jQuery('[id^=post]:first').offset().top - jQuery('header').height() ) + 'px;'
        + ' }</style>');
});

/**
 * used to embed code files into <pre class="prettyprint">
 * It will inject the content of the file from the given url
 * into the pre of the given selector and call prettyprint
 * so that it is properly formatted
 */
function display_file_content(url, selector){
    jQuery( document ).ready( function($){
        $.get( url, function( data ) {
            $( selector ).text( data );
            jQuery(selector).addClass("prettyprint");
            //prettyPrintOne(selector);
            prettyPrint();
        });
    });
}


function include_post_file(url, selector){
    jQuery( document ).ready( function($){
        if( selector == null )
        selector = "#included-post";

    $.get( url, function( data ) {
        $( selector ).html( data );
        prettyPrint();
        init_scrollspy();
        $('body').scrollspy('refresh');
    });
    });
}

function setBootstrapEnvironment() {
    console.log("bootstrap");
    
    var envs = ['phone', 'tablet', 'desktop', 'desktop hd'];
    var envb = ['xs', 'sm', 'md', 'lg'];

    var body = jQuery('body'); 
    // removes the old class tags
    body.removeClass('phone tablet desktop');

    var el = jQuery('<div>');
    el.appendTo( body );
    
    // adds one of the class (preceded by hidden-) to the div. 
    // If the div is hidden, it means that we have found the proper env.
    // ( see the bootstrap classes attributes for more details ) 
    for ( var i = envs.length - 1; i >= 0; i-- ) {
        el.addClass( 'hidden-' + envb[i] );
        
        if ( el.is(':hidden') ) {
            // we found it ! adds the class to the body
            body.addClass( envs[i] );
        }
        
        el.removeClass( 'hidden-' + envb[i] );
    };    
}



function sidebar_on_top_left(){
    var $ = jQuery;
    $('#tertiary').detach().prependTo('#main');
    $('<style type="text/css"> ' + 
        '.desktop .entry-header,.desktop .entry-content, .desktop .entry-summary { '+ 
        'padding: 0 60px 0 376px }</style>')
        .appendTo('header');
    $('.site-main .widget-area').css('float', 'left');
}
