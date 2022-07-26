jQuery( document ).ready( function( $ ){
    init_scrollspy();
    init_affix();
});

function init_scrollspy(){
    var $ = jQuery;
    //console.log( $('#sidebar').size());
    if( $('#sidebar').size() == 0 ){
        var date = new Date();
        console.log("sidebar not exist: " + date.getTime());   
        $('article').wrapAll('<div class="row"><div class="col-md-8" role="main"></div></div>');
        $('div.row').prepend('<div class="col-md-2 the-sidebar-container">' 
            + '<div id="sidebar" class="bs-sidebar" data-spy="affix" data-offset-bottom="200">'  
            + '<ul class="nav bs-sidebar">'
            + '</ul></div></div>');
    }else{
        $('#sidebar > ul').html("");
    }


    $('article').each(function(){
        title = $(this).find('h1:first');
        title.addClass('anchored');
        li = $('<li><a href="#' + $(this).attr('id') + '">' + title.text() + '</a></li>' );
        ul = $('<ul class="nav"></ul>');
        $( "#" + $(this).attr('id') + " h2" ).each(function(){
            str = $(this).text().toLowerCase().trim()
            .replace(/ /g, "-").replace(/[^a-z]/gi, "");
        $(this).attr('id', str);
        $(this).addClass('anchored');
        $(ul).append('<li><a href="#' + 
            str + '">' + $(this).text() + '</a></li>' );
        });
        $(li).append(ul);
        $('#sidebar > ul').append(li);
    });


    $('body').scrollspy({ 
        target: '#sidebar' 
    });
    $('#sidebar li:first').addClass("active");
    $('#sidebar').parents('aside:first').css('background-color', 'transparent');
}

function init_affix($){
    //---------------- affix 

    var $ = jQuery;
    //setTimeout( function(){
        $('body').append('<style type="text/css">body.desktop .the-sidebar-container {'
            + 'margin-top:' 
            +  (jQuery('article:first').offset().top - jQuery('header').height() ) + 'px;'
            + ' }</style>');
        //$('#sidebar').parent().css('margin-top', 

 $.extend($.fn.affix.prototype, {
    checkPosition : function () {
    if (!this.$element.is(':visible')) return

    var scrollHeight = $(document).height()
    var scrollTop    = this.$window.scrollTop()
    var position     = this.$element.offset()
    var offset       = this.options.offset
    var offsetTop    = offset.top
    var offsetBottom = offset.bottom

    if (typeof offset != 'object')         offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function')    offsetTop    = offset.top()
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom()

    var affix = this.unpin   != null && (scrollTop + this.unpin <= position.top) ? false :
                offsetBottom != null && (position.top + this.$element.height() >= scrollHeight - offsetBottom) ? 'bottom' :
                offsetTop    != null && (scrollTop <= offsetTop) ? 'top' : false

    if (this.affixed === affix) return
    if (this.unpin) this.$element.css('top', '')

    this.affixed = affix
    this.unpin   = affix == 'bottom' ? position.top - scrollTop : null

    this.$element.removeClass(Affix.RESET).addClass('affix' + (affix ? '-' + affix : ''))

    if (affix == 'bottom') {
      //this.$element.offset({ top: document.body.offsetHeight - offsetBottom - this.$element.height() })
      this.$element.offset({ top: scrollHeight - offsetBottom - this.$element.height() })
    }
  }
});

        $('#sidebar').affix({
            offset: {
                top: function () {
                    return (this.top = jQuery('article:first').offset().top)
                }, 
                bottom: function () {
                    var plus = jQuery('.navigation:last').outerHeight(true) + 40; 
                    console.log(plus);
                    return (this.bottom = jQuery('#colophon').outerHeight(true) + 
                        plus );
                }
            }
        });
    //}, 100);
}


