jQuery(document).ready(function($){

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

  var event_index = {
    $select_element: $('.tribe-bar-search-category-filter>select'),
    init: function(){
      var self = this;
      if (self.$select_element.length > 0) {
        self.$select_element.selectric();
      }
    }
  }.init();

  var blog_index = {
    elements: [
      '.blog-gswa .post',
      '.yarpp-related .post',
      '.events-row-wrapper .post .entry-image',
      '.events-row-wrapper .post .entry-title',
      '.events-row-wrapper .post .entry-content',
      '.our-mission-boxes .box',
      'body.home .boxes-beneath .iconbox h3',
      'body.home .boxes-beneath .iconbox .iconbox-content'
    ],
    init: function () {
      var self = this;
      self.elements.forEach(function (selector, index) {
        var element = $(selector);
        if (element.length > 0) {
          element.matchHeight({ byRow: true, property: 'min-height' })
        }
      });
    }
  }.init();

});
