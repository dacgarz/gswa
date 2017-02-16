<?php

include_once 'MCAPI.class.php';

class migla_mailchimp_class
{
   var $API_KEY;

   public function __construct(){
         $this->API_KEY = get_option('migla_mailchimp_apikey') ;
   }

   public function subscribe_contact( $postData , $flag ) 
   {
    
     $list_array =  (array)get_option('migla_mailchimp_list') ; //'0c76621e0d';
     $campaignId = 'YOUR MAILCHIMP CAMPAIGN ID - see campaigns() method';
     $apiUrl = 'http://api.mailchimp.com/1.3/';
     $retval = '';

     if( $list_array[0] != '' )
     {
       $api = new migla_MCAPI( $this->API_KEY  );

       $merge_vars = array('FNAME' => $postData['miglad_firstname'], 
                         'LNAME' => $postData['miglad_lastname'],
                         'EMAIL' => $postData['miglad_email']
                    );

       foreach( $list_array as $list )
       {
          if( $list['status'] == '2' )
          {
            $retval = $api->listSubscribe( $list['id'], $postData['miglad_email'] , $merge_vars );
          }else if ( $list['status'] == '3' )
          {
            if( $flag ){
                $retval = $api->listSubscribe( $list['id'], $postData['miglad_email'] , $merge_vars );
            }
          }
       }//foreach

     }//if array list not empty

       return $retval;
   } 

   public function get_contact_list()
   {
     $api    = new migla_MCAPI( $this->API_KEY );
     $retval = $api->lists();
     $out    = array(); $out[0] = ""; $count = 0;

     if ($api->errorCode){
     	$msg = $msg . "(Error) Unable to load lists()!";
	$msg = $msg . "\n\tCode=".$api->errorCode;
	$msg = $msg . "\n\tMsg=".$api->errorMessage."\n";
        $out[0] = $msg;
     } else {
	foreach ($retval['data'] as $list){
            $count++;
            $out[$count] = $list;
	}

     } 

      return json_encode( $out );
   }

}

?>