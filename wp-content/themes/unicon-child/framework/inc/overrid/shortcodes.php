<?php

function minti_iconbox_function( $atts, $content = null ) {
  extract(shortcode_atts(array(
    'icon'      	=> 'fa-phone',
    'iconimg'		=> '',
    'title'			=> '',
    'iconcolor'		=> 'accent',
    'icon_animation' => 'none',
    'textcolor'		=> 'dark',
    'customcolor'	=> '',
    'url'			=> '',
    'style'			=> '1',
    'class'			=> ''
  ), $atts));

  if($url == '' || $url == 'http://'){
    $link1 = '';
    $link2 = '';
  } else {
    $link1 = '<div onclick="location.href=\''.$url.'\';" style="cursor:pointer;">';
    $link2 = '</div>';
  }

  if($customcolor != ''){
    $output_css = 'color: '.$customcolor.';';
  } else {
    $output_css = '';
  }

  if($iconimg == ''){
    $symbol = '<i class="fa '. esc_attr($icon) .' boxicon" style="'. esc_attr($output_css) .'"></i>';
  }
  else{
    $img_src = wp_get_attachment_image_src($iconimg, 'full');
    $symbol = "<img src='".esc_url($img_src[0])."' class='iconimg' />";
  }

  if($style == '1') {
    $out = '<div class="iconbox '.esc_attr($class).' wpb_content_element iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' animation-'.esc_attr($icon_animation).'"><h3>'.$symbol.'<span>'. esc_html($title) .'</span></h3><p>'. do_shortcode($content) . '</p></div>';
  }
  elseif($style == '2') {
    $out = '<div class="iconbox '.esc_attr($class).' wpb_content_element iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix"><div class="iconbox-icon">'.$symbol.'</div><div class="iconbox-content"><h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></div></div>';
  }
  elseif($style == '3') {
    $out = '<div class="iconbox '.esc_attr($class).' wpb_content_element iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).'">'.$symbol.'<h3>'. esc_html($title) .'</h3><div class="iconbox-content"><p>'. do_shortcode($content) . '</p></div></div>';
  }
  elseif($style == '4') {
    $out = '<div class="iconbox '.esc_attr($class).' wpb_content_element iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix"><div class="iconbox-icon">'.$symbol.'</div><div class="iconbox-content"><h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></div></div>';
  }
  elseif($style == '5') {
    $out = '<div class="iconbox '.esc_attr($class).' wpb_content_element iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix"><div class="iconbox-icon">'.$symbol.'</div><div class="iconbox-content"><h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></div></div>';
  }
  elseif($style == '6') {
    $out = '<div class="iconbox '.esc_attr($class).' wpb_content_element iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix">'.$symbol.'<h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></div>';
  }
  elseif($style == '7') {
    $out = '<div class="iconbox '.esc_attr($class).' iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix">'.$symbol.'<h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></div>';
  }
  elseif($style == '8') {
    $out = '<div class="iconbox '.esc_attr($class).' iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix"><h3>'. esc_html($title) .'</h3>'.$symbol.'<p>'. do_shortcode($content) . '</p></div>';
  }
  elseif($style == '9') {
    $out = '<div class="flip"><div class="iconbox '.esc_attr($class).' iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' card clearfix"><div class="iconbox-box1 face front"><table><tr><td>'.$symbol.'<h3>'. esc_html($title) .'</h3></td></tr></table></div><div class="iconbox-box2 face back"><table><tr><td><h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></td></tr></table></div></div></div>';
  }
  elseif($style == '10') {
    $out = '<div class="iconbox '.esc_attr($class).' iconbox-style-'.esc_attr($style).' icon-color-'.esc_attr($iconcolor).' color-'.esc_attr($textcolor).' clearfix">'.$symbol.'<h3>'. esc_html($title) .'</h3><p>'. do_shortcode($content) . '</p></div>';
  }
  else{
    $out = '';
  }

  $out = $link1.$out.$link2;

  return $out;
}
add_shortcode('minti_iconbox', 'minti_iconbox_function');