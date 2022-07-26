jQuery( document ).ready( function( $ ){
    init_scrollspy(true);
    init_affix();
});

function init_scrollspy( display_home ){
    var $ = jQuery;

    sidebar = $('#widgbs_sidebar > ul');
    // check that the container (div id="sidbar") actually exists
    if( sidebar.size() == 0 ){
        console.log("Bootstrap Sidebar Widget: [error] init_scrollspy: no #widgbs_sidebar found (???)");
        return;
    }
    // clear its content in case it is called multiple times (refresh)
    sidebar.html("");


    if( typeof display_home != 'undefined' && display_home == true ){
        $('body').attr('id', 'body');
        sidebar.append('<li><a href="#body">Home</a></li>');
    }

    // for each article, get its titles (h1, h2), create the anchors 
    // and add them to the menu
    $('.post').each(function(){
        title = $(this).find('h1:first');
        title.addClass('anchored');
        // create the li element holding the h1 title 
        primary_li = $('<li><a href="#' + $(this).attr('id') + '">' + 
            title.text() + '</a></li>' );

        // create a submenu to hold the h2 titles
        inner_ul = $('<ul class="nav"></ul>');
        $(this).find('h2').each(function(){
            // remove special chars for the anchor
            str = $(this).text().toLowerCase().trim()
            .replace(/ /g, "-").replace(/[^a-z]/gi, ""); 
        // add anchor and append a new li item to the inner ul
        $(this).attr('id', str);
        $(this).addClass('anchored'); 
        $(inner_ul).append('<li><a href="#' + 
            str + '">' + $(this).text() + '</a></li>' );
        });

        // add li to the html
        $(primary_li).append(inner_ul);   
        $('#widgbs_sidebar > ul').append(primary_li); 
    });

    //---------------- scrollspy 
    $('body').scrollspy({ 
        target: '#widgbs_sidebar' 
    });
    // the first item is active by default
    $('#widgbs_sidebar li:first').addClass("active");
    // custom: remove the title background, since it is not nice 
    $('#widgbs_sidebar').parents('aside:first').css('background-color', 'transparent');
}

//---------------- affix 
function init_affix($){
    var $ = jQuery;

    // use the fixed version of bootstrap affix plugin
    $('#widgbs_sidebar').affix_fix({
        offset: {
            top: function () {
                // the top is the first article's heading
                return (this.top = jQuery('[id^=post]:first').offset().top)
            }, 
        bottom: function () {
            // the bottom is the footer + the bottom naviguation
            var b = $(document).height() -
                   ( $('[id^=post]:last').offset().top + $('[id^=post]:last').height() );
            return b;
        }
        }
    });
    //}, 100);
}


