jQuery(document).ready(function($){
  var $posts = $('.blog-gswa .post, .yarpp-related .post');
  if ($posts.length > 0) {
    $posts.matchHeight({
      byRow: true,
      property: 'min-height'
      // target: null,
      // remove: false
    })
  }
});