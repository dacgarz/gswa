<?php

class migla_email_handler
{


  public function get_header( $en , $e )
  {
     $headers  = "MIME-Version: 1.0" . "\r\n";
     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
     //$headers .= "Reply-To: Admin <" . get_option('admin_email') .">\r\n"; 
     $headers .= "Reply-To: Admin <" . $e .">\r\n"; 
     $headers .= "Return-Path: Admin <" . get_option('admin_email') .">\r\n"; 
     $headers .= 'From: '.$en.' <'.$e.'>' . "\r\n";

     return $headers;
  }

  public function get_the_body( $body, $repeat, $anon, $signature, $replace, $isRepeat, $isAnon )
  {
      $thebody = "";
      $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );
      
      $thebody = $body ."<br>" ;

      if( $isRepeat == 'no' ||  $isRepeat == '' ){

      }else{
          $thebody .= "<br>". $repeat ; 
      }
      if( $isAnon == 'yes' ){ 
          $thebody .= "<br>". $anon  ; 
      }     

      $thebody .= "<br>" . $signature ."<br>" ;
     
      $content  =  str_replace($placeholder, $replace, $thebody );
      $content_ = $content;
      $content  =  str_replace( "\\" , "" , $content_ );
      $content  = "<html><body>" . $content . "</body></html>";      
      
      return $content;
  }

 public function send_thankyou_email( $postdata, $e, $en )
 {
    $subject = get_option( 'migla_thankSbj' );

    $default 		= get_option('migla_default_timezone');
    $language 		= get_option('migla_default_datelanguage');
    $date_format 	= get_option('migla_default_dateformat');
    $the_date = migla_get_date( $default, $language , $date_format, false, $postData['miglad_date'] );	

    $donor_email = $postdata['miglad_email']; 
    $amount      = $postdata['miglad_amount'];
    $replace     = array( $postdata['miglad_firstname'], 
                          $postdata['miglad_lastname'] , 
                          $amount, 
                          $the_date , 
                          '<br>' );

    $content = $this->get_the_body( get_option( 'migla_thankBody' ), 
			                        get_option( 'migla_thankRepeat' ), 
						get_option( 'migla_thankAnon' ), 
						get_option( 'migla_thankSig' ), 
						$replace, 
						$postdata['miglad_repeating'], 
						$postdata['miglad_anonymous'] );
												   						
    $header  = $this->get_header( $en , $e );

    if (function_exists('wp_mail'))
    {
      $status = wp_mail( $donor_email , $subject , $content, $header );
    }else{
      $status = mail( $donor_email , $subject , $content, $header );
    }

    return $status;
  }
  
  
 public function send_notif_email( $postdata, $e, $en, $ne)
 {
    $subject = __("You have just received a donation","migla-donation");

    $default 		= get_option('migla_default_timezone');
    $language 		= get_option('migla_default_datelanguage');
    $date_format 	= get_option('migla_default_dateformat');
    $the_date           = migla_get_date( $default, $language , $date_format, false, $postdata['miglad_date'] );	

    $donor_email = $postdata['miglad_email']; 
    $amount      = $postdata['miglad_amount'];
    $replace     = array( $postdata['miglad_firstname'], 
                          $postdata['miglad_lastname'] , 
                          $amount, 
                          $the_date,
                          '<br>' );
					  
						  
    $body = "<html><body>";
    $body .= __("You have just received a donation. Here is the information about that donation: ","migla-donation"). "<br><br>" ;
    $body .= "<table>";
    
	//$keys = array_keys( $postdata ); 
	//$i 		= 0;
	
	if( $postdata['miglad_form_id'] == '' )
	{
		$form_fields = get_option('migla_form_fields');
	}else{
		$form_fields = get_post_meta( $postdata['miglad_form_id'] , 'migla_form_fields' );
		$form_fields = $form_fields[0];
	}
	
	foreach( (array)$form_fields as $fields )
	{
		$children = $fields['child'];
		foreach( (array)$children as $child )
		{
			$value = $postdata[($child['code'].$child['id'])];
			if( !empty($value) )
				$body .= "<tr><td>". str_replace( "[q]", "'", $child['label'] ) ." </td><td>: ". str_replace( "[q]", "'", $value) ."</td></tr>";  
		}
	}
	
    /*	
	foreach( (array)$postdata as $value)
	   {
			  if( !empty($value) )
			 {  
				if ( $keys[$i]=='miglad_session_id' || $keys[$i]=='miglad_session_id_' || $keys[$i]=='miglad_mg_add_to_milist')
				{
				}
                                else if (  $keys[$i] == 'miglad_campaign')
				{
					$body .= "<tr><td>".substr( $keys[$i], 7) ." </td><td>: ".str_replace("[q]", "'", $value)."</td></tr>";
				}
				else if (  $keys[$i] == 'miglad_date')
				{
					$body .= "<tr><td>".substr( $keys[$i], 7) ." </td><td>: ".date("F jS, Y", strtotime($value))."</td></tr>";
				}else{
					$body .= "<tr><td>".substr( $keys[$i], 7) ." </td><td>: ". $value ."</td></tr>";  
				}
			 }
			  $i++;
       }
	*/
	
    $body .= "</table>";

    $content  = $body;
    $body     = str_replace( "\\" , "" , $content );
    $body    .= "<br><br>";
    $url      = get_admin_url();
    $url     .= "admin.php?page=migla_reports_page";
    $body    .= "<a href=".$url.">". __("Click here for more detailed information about this donation", "migla-donation") ."</a>";
    $body    .= "<br>";	
    $body    .= "</body></html>";   	
 
    $headers  = $this->get_header( $en , $e );
	
	$ne_clean	= str_ireplace(" ", "", $ne);
    $Emails 	= explode(',', $ne_clean);

    if (function_exists('wp_mail'))
    {
      foreach ( (array)$Emails as $notifyEmail )
      {
 	  $notifyEmail = trim($notifyEmail);
	  if (!empty($notifyEmail))
	  {
 		$mail_sent = wp_mail( $notifyEmail, $subject, $body, $headers );
	   }
      }
    }else{
      foreach ( (array)$Emails as $notifyEmail )
      {
 	  $notifyEmail = trim($notifyEmail);
	  if (!empty($notifyEmail))
	  {
 		$mail_sent = mail( $notifyEmail, $subject, $body, $headers );
	   }
      }
    }//IF FUNCTION EXIST
  }  
  
 public function send_hletter( $e, $en , $to, $content, $repeat, $anon, $firstname, $lastname, $amount, $honoreename, $date )
 {
	$subject = get_option('migla_honoreESbj');
	$body = get_option( 'migla_honoreEBody' ) ."<br>" ;

	if( $content != '' )
	{
	   $body .= get_option('migla_honoreECustomIntro');
	   $body .= "<br>\"" . $content . "\"<br><br>";
	}     
   
    $default 		= get_option('migla_default_timezone');
    $language 		= get_option('migla_default_datelanguage');
    $date_format 	= get_option('migla_default_dateformat');
    $the_date           = migla_get_date( $default, $language , $date_format, false, $postdata['miglad_date'] );	   

   $placeholder = array( '[honoreename]' ,'[amount]' ,'[date]', '[newline]', '[firstname]','[lastname]' );
   $replace     = array(  $honoreename    ,$amount , $the_date ,    '<br>',     $firstname,   $lastname );

  if( $repeat != 'no' ){
	$body .= "<br>".get_option( 'migla_honoreERepeat' ) ."<br>"  ;
  }
  
  if( $anon == 'yes' ){
	$body .= get_option( 'migla_honoreEAnon' ) ."<br>" ;
  }

   $body .= "<br>". get_option( 'migla_honoreESig' ) ."<br>" ;  

   $content =  str_replace($placeholder, $replace, $body);

   $header  = $this->get_header( $en , $e );

   if (function_exists('wp_mail'))
   { 
     $status = wp_mail( $to , $subject, $content, $header );
   }else{
     $status = mail( $to , $subject, $content, $header );
   }

   return $status;
}  

 public function send_offline_first_email( $postdata , $e, $en )
 {
   $subject = get_option('migla_offmsg_thankSbj') ;
   
    $default 		= get_option('migla_default_timezone');
    $language 		= get_option('migla_default_datelanguage');
    $date_format 	= get_option('migla_default_dateformat');
    $the_date           = migla_get_date( $default, $language , $date_format, false, $postdata['miglad_date'] );	   

   $donor_email = $postdata['miglad_email']; 
   $amount      = $postdata['miglad_amount'];
   $replace     = array(  $postdata['miglad_firstname'], 
                          $postdata['miglad_lastname'] ,
                          $amount,
                          $the_date, 
                          '<br>' );

   $content = $this->get_the_body( get_option(' migla_offmsg_body') , 
						"", 
						"", 
						get_option( 'migla_offmsg_signature' ), 
						$replace, 
						$postdata['miglad_repeating'], 
						$postdata['miglad_anonymous'] ); 

    $header  = $this->get_header( $en , $e );

	if (function_exists('wp_mail'))
	{ 
		$status = wp_mail( $donor_email, $subject, $content, $header);
	}else{
		$status = mail( $donor_email, $subject, $content, $header);
	}	
	
 
    return $status;

  }


  function send_thankyou_email_offline( $postdata, $e, $en )
  {

  }

}//END OF CLASS

?>