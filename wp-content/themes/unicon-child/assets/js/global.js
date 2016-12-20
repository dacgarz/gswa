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
    $posts: $('.blog-gswa .post, .yarpp-related .post'),
    init: function () {
      var self = this;
      if (self.$posts.length > 0) {
        self.$posts.matchHeight({
          byRow: true,
          property: 'min-height'
          // target: null,
          // remove: false
        })
      }
    }
  }.init();

});
