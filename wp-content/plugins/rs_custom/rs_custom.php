<?php

/*
Plugin Name: RS Custom
Plugin URI: 
Description: 
Version: 0.01
Author: Tony
Author URI: 
*/

require_once "vendor/autoload.php";
$plate_engine = new League\Plates\Engine(__DIR__ . "/templates");

require_once "classes/events.class.php";
new rsEvents($plate_engine);

require_once "classes/events.vc.class.php";
new homepageEventsWidget($plate_engine);