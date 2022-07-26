/* ========================================================================
 * Bootstrap: affix.js v3.0.3
 * http://getbootstrap.com/javascript/#affix
 * ========================================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */
var $ = jQuery;
var _Affix = $.fn.affix;
var Affix = function( elements, options ) {
    _Affix.Constructor.apply( this, arguments );
}

Affix.prototype = $.extend({}, _Affix.Constructor.prototype, {
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

$.fn.affix = $.extend()

// override the old initialization with the new constructor
$.fn.affix = $.extend(function(option) {
    var args = $.makeArray(arguments),
    option = args.shift()

    // this is executed everytime element.modal() is called
    return this.each(function() {
        var $this = $(this)
        var data = $this.data('affix'),
            options = $.extend({}, _Affix.defaults, $this.data(), typeof option == 'object' && option)

        if (!data) {
            $this.data('Affix', (data = new Affix(this, options)))
        }
        if (typeof option == 'string') {
            data[option].apply( data, args )
        }
    });
}, $.fn.affix);


+function ($) { "use strict";

  // AFFIX CLASS DEFINITION
  // ======================

  var AffixCustom = function (element, options) {
    this.options = $.extend({}, AffixCustom.DEFAULTS, options)
    this.$window = $(window)
      .on('scroll.bs.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.bs.affix.data-api',  $.proxy(this.checkPositionWithEventLoop, this))

    this.$element = $(element)
    this.affixed  =
    this.unpin    = null

    this.checkPosition()
  }

  AffixCustom.RESET = 'affix affix-top affix-bottom'

  AffixCustom.DEFAULTS = {
    offset: 0
  }

  AffixCustom.prototype.checkPositionWithEventLoop = function () {
    setTimeout($.proxy(this.checkPosition, this), 1)
  }

  AffixCustom.prototype.checkPosition = function () {
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

    this.$element.removeClass(AffixCustom.RESET).addClass('affix' + (affix ? '-' + affix : ''))

    if (affix == 'bottom') {
      //this.$element.offset({ top: document.body.offsetHeight - offsetBottom - this.$element.height() })
      this.$element.offset({ top: scrollHeight - offsetBottom - this.$element.height() })
    }
  }


  // AFFIX PLUGIN DEFINITION
  // =======================

  var old = $.fn.affix_custom

  $.fn.affix_custom = function (option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.affix')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.affix', (data = new AffixCustom(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.affix_custom.Constructor = AffixCustom


  // AFFIX NO CONFLICT
  // =================

  $.fn.affix_custom.noConflict = function () {
    $.fn.affix_custom = old
    return this
  }


  // AFFIX DATA-API
  // ==============

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
      var data = $spy.data()

      data.offset = data.offset || {}

      if (data.offsetBottom) data.offset.bottom = data.offsetBottom
      if (data.offsetTop)    data.offset.top    = data.offsetTop

      $spy.affix(data)
    })
  })

}(jQuery);
