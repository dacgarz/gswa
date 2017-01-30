jQuery(document).ready(function($){

  var header = {
    $header: $('.fullheader-wrapper'),
    transparentHeader: function () {
      var self = this;
      if ($(document).scrollTop() >= 60) {
        self.$header.find('#header.header-v1').removeClass('header-transparent');
      } else {
        self.$header.find('#header.header-v1.stuck').addClass('header-transparent');
      }
    },
    init: function () {
      var self = this;
      if (self.$header.length > 0) {
        // if (/Android|BlackBerry|iPhone|iPad|iPod|webOS/i.test(navigator.userAgent) === false) {
          self.$header.waypoint('sticky');
          if ($("body").hasClass("header-is-transparent")) {
            $(document).scroll(function() { self.transparentHeader(); });
            self.transparentHeader();
          }
        // }
      }
    }
  }.init();

  var homepage_slider = {
    $slider: $('.rev_slider_wrapper.fullwidthbanner-container'),
    slides: null,
    init: function () {
      var self = this;
      if (self.$slider.length > 0) {
        self.slides = self.$slider.find('ul>li').length;
        if (self.slides == 1) {
          self.$slider.addClass('only-one-slide');
        }
      }
    }
  }.init();

  var selectric_init = {
    select_elements: [
      '.tribe-bar-search-category-filter>select',
      '.publications-gswa select'
      ],
    init: function(){
      var self = this;
      self.select_elements.forEach(function(selector, index) {
        var $element = $(selector);
        if ($element.length > 0) {
          $element.selectric();
        }
      });

    }
  }.init();

  var blog_index = {
    elements: [
      '.blog-gswa .post',
      '.yarpp-related .post',
      '.events-row-wrapper .post .entry-image',
      '.events-row-wrapper .post .entry-title',
      '.events-row-wrapper .post .entry-content',
      '.publications-gswa .post',
      '.publications-gswa .post-inner',
      '.publications-gswa .publications-index .entry-image',
      '.our-mission-boxes .box',
      'body.home .boxes-beneath .iconbox h3',
      'body.home .boxes-beneath .iconbox .iconbox-content'
    ],
    init: function () {
      var self = this;
      self.elements.forEach(function (selector, index) {
        var element = $(selector);
        if (element.length > 0) {
          element.matchHeight({ byRow: true })
        }
      });
    }
  }.init();

});
