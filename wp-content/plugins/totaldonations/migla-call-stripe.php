<?php
require_once "inc_stripe/Stripe.php";

if( get_option('migla_stripe_verifySSL') == 'yes' ){
    Migla_Stripe::$verifySslCerts = true;  	
}else{
    Migla_Stripe::$verifySslCerts = false;
}

?>