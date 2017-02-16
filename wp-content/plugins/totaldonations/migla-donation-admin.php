<?php

require_once 'migla-functions.php';
require_once 'admin-files/admin-main.php';
require_once 'admin-files/admin-campaigns.php';
require_once 'admin-files/admin-reports.php';
require_once 'admin-files/admin-offline-donations.php';
require_once 'admin-files/admin-custom-theme.php';
require_once 'admin-files/admin-form-options.php';
require_once 'admin-files/admin-settings.php';
require_once 'admin-files/admin-paypal-settings.php';
require_once 'admin-files/admin-stripe.php';
require_once 'admin-files/admin-offline-settings.php';
require_once 'admin-files/admin-authorize.php';
require_once 'admin-files/admin-help.php';
//require_once 'admin-files/admin_campaign_creator.php';

$migla_top_level 	= new migla_top_level_class();
$migla_campaign_menu    = new migla_campaign_menu_class()    ;
$migla_reports          = new  migla_reports_class();
$migla_offline_donations = new migla_offline_donations_class();
$migla_customize_theme 	= new migla_customize_theme_class();
//$migla_campaign_creator = new migla_campaign_creator_menu_class();
$migla_form_settings	= new migla_form_settings_class();
$migla_settings		= new migla_settings_class();
$migla_paypal_settings		= new migla_paypal_settings_class();
$migla_stripe_settings		= new migla_stripe_setting_class();
$migla_authorize_settings = new migla_authorize_settings_class();
$migla_offline_settings   = new migla_offline_settings_class();
$migla_help		  = new migla_help_class();

?>