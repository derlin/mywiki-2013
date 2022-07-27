---
title: "Bootstrap sidebar in wordpress"
date: "2013-12-27"
---

## Prerequisites

You need the following scripts and css included/enqueued:

- `bootstrap.css`
- `bootstrap.[min].js`, including the affix and scrollspy plugins
- `jquery.[min].js`
- `docs.css`: the css used in bootstrap's official site

**Warning !** I implemented it on the twenty thirteen theme. Be sure to adapt the jquery selectors present in this post to yours.

## ScrollSpy functionality

##### The js

First, you need the following html structure, or at lead one ul element:

```html
<ul>
 	<li><code>bootstrap.css</code></li>
 	<li><code>bootstrap.[min].js</code>, including the affix and scrollspy plugins</li>
 	<li><code>jquery.[min].js</code></li>
 	<li><code>docs.css</code>: the css used in bootstrap's official site</li>
</ul>
```

We'll use a script to dynamically create the list and initialise the scrollspy:
```js
function init_scrollspy(){
    var $ = jQuery;
    // if no sidebar div &gt; ul, create one
    if( $('#sidebar').size() == 0 ){
        var date = new Date();
        console.log("sidebar not exist: " + date.getTime());   
        $('article').wrapAll('<div class="row"><div class="col-md-8" role="main"></div></div>');
        $('div.row').prepend('<div class="col-md-2 the-sidebar-container">' 
            + '<div id="sidebar" class="bs-sidebar" data-spy="affix" data-offset-bottom="200">'  
            + '<ul class="nav bs-sidebar">'
            + '</ul></div></div>');
    }else{ // else, start fresh
        $('#sidebar > ul').html("");
    }


    $('article').each(function(){
        title = $(this).find('h1:first');
        title.addClass('anchored');
        li = $('<li><a href="#' + $(this).attr('id') + '">' + title.text() + '</a></li>' );
        ul = $('<ul class="nav"></ul>');
        // each h2 becomes a second-level menu item
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


    // simply init the scrollspy on the body
    // !! the body must have a position: relative attribute
    $('body').scrollspy({ 
        target: '#sidebar' 
    });
    // the first link is selected by default
    $('#sidebar li:first').addClass("active");
    $('#sidebar').parents('aside:first').css('background-color', 'transparent');
}
```

Be sure to have the body position set to relative.

Also, to allow the title to be at the top of the page (and not out of screen) when you click on an anchor link, here is a simple trick that works pretty well:
```css
/* 
   assuming that all the titles have the class anchored 
   you can of course replace the selector to whatever
   you want
*/
.anchored:before {
    content: "";
    display: block;
    height: 50px;
    margin: -30px 0 0;
}
```

## Affix functionality

The initialisation:

```js
function init_affix(){

    var $ = jQuery;

    $('#sidebar').affix({
    offset: {
        top: function () {
            // the offset is relative to the document 
            return (this.top = $('article:first').offset().top)
        }, 
        bottom: function () {
            // try to calculate dynamically the size of the footer
            return (this.bottom = $('#colophon').outerHeight(true) + 
                $('.navigation:last').outerHeight(true) + 40 );
        }
    }
    });
}
```

The bugfix to do in the jquery, version line 1553, in the if (affix == 'bottom') block:
```js
    if (affix == 'bottom') {
      // WRONG
      this.$element.offset({ 
        top: document.body.offsetHeight // this one must be changed
            - offsetBottom - this.$element.height() })
      // RIGHT
      this.$element.offset({ 
        top: scrollHeight // this one works
            - offsetBottom - this.$element.height() })
    }
  }
```
Finally, add the following css and you are done! Note that I changed the colors from the orginal one in the bootstrap's site. //TODO
