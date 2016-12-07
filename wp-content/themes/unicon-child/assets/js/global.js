jQuery(document).ready(function($){
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
