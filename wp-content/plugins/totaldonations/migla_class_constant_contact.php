<?php

  // require the autoloader
  require_once 'constant_contact/src/Ctct/autoload.php';
  use Ctct\ConstantContact;
  use Ctct\Components\Contacts\Contact;
  use Ctct\Components\Contacts\ContactList;
  use Ctct\Components\Contacts\EmailAddress;
  use Ctct\Exceptions\CtctException;

  //define("CC_APIKEY", get_option('migla_constantcontact_apikey') );
  //define("CC_ACCESS_TOKEN", get_option('migla_constantcontact_token') );

class migla_constant_contact_class
{
    var $CC_ACCESS_TOKEN;
    var $CC_APIKEY;

    public function __construct(){
        $this->CC_APIKEY       = get_option('migla_constantcontact_apikey');
        $this->CC_ACCESS_TOKEN = get_option('migla_constantcontact_token');
    }
 
	public function get_milist()
	{
	  $cc = new ConstantContact( $this->CC_APIKEY );
	  $theList = $cc->getLists( $this->CC_ACCESS_TOKEN );
	  return json_encode( $theList);
	}

	public function add_to_milist_test( $email, $fname, $lname , $flag )
	{
		$list1  = get_option('migla_constantcontact_list1');
		$list2  = get_option('migla_constantcontact_list2');
		$logic1 = count($list1) > 0 && $list1 != '';
		$logic2 = count($list2) > 0 && $list2 != '';
		$result = "";		
		
		try {
			if( $logic1 || $logic2 )
			{			
				$cc = new ConstantContact( $this->CC_APIKEY );
				
				$contact = new Contact();
				$contact->addEmail( $email );
			
				if( $logic1 )
				{
					foreach( (array)$list1 as $l1 )
					{
						$contact->addList($l1['id']);
					}
				}
				
				if( $logic2 )
				{
					if( $flag )
					{
						foreach( (array)$list2 as $l2 )
						{
							$contact->addList($l2['id']);
						}
					}
				}
					$contact->first_name =  $fname;
					$contact->last_name =  $lname;

					$returnContact = $cc->addContact( $this->CC_ACCESS_TOKEN, $contact ,  true);
					$result 		= $returnContact ;
			}
		
		} catch (CtctException $ex) {
			  $result = $ex->getErrors();
		}
		  
		return $result;		
	}
	
	public function add_to_milist( $email, $fname, $lname , $flag )
	{

		$list1  = (array)get_option('migla_constantcontact_list1');
		$list2  = (array)get_option('migla_constantcontact_list2');
		$logic1 = count($list1) > 0 && $list1[0] != '';
		$logic2 = count($list2) > 0 && $list2[0] != '';

		 $cc = new ConstantContact( $this->CC_APIKEY );
		 $result = "";		
		
	  if( $logic1 || $logic1)
	  {

		 // $theList = $cc->getLists( $this->CC_ACCESS_TOKEN ) ;

		 try {

			// checks to see if a contact with the email address already exists in the account
			$response = $cc->getContactByEmail( $this->CC_ACCESS_TOKEN, $email );

			$result = $response->results;

			// creates a new contact if one does not exist
			if ( empty($response->results) ) {


				$contact = new Contact();
				$contact->addEmail( $email );

				if( $logic1 )
				{
				  foreach( (array)$list1 as $l1 )
				  {
					  $contact->addList($l1['id']);
				  }
				}

				if( $flag )
				{
				   if( $logic2 )
				   {
					  foreach( (array)$list2 as $l2 )
					  {
						  $contact->addList($l2['id']);
					  }
				   }
				}

				$contact->first_name =  $fname;
				$contact->last_name =  $lname;

				$returnContact = $cc->addContact( $this->CC_ACCESS_TOKEN, $contact ,  true);
				$result = $returnContact ;


			} else {
				
				$contact = $response->results[0];

				if( $logic1 )
				{
				  foreach( (array)$list1 as $l1 )
				  {
					  $contact->addList($l1['id']);
				  }
				}

				if( $flag )
				{
				   if( $logic2 )
				   {
					  foreach( (array)$list2 as $l2 )
					  {
						  $contact->addList($l2['id']);
					  }
				   }
				}

				$contact->first_name = $fname;
				$contact->last_name = $lname;

				$returnContact = $cc->updateContact( $this->CC_ACCESS_TOKEN, $contact, true);  
				$result = $returnContact ;  

			}

			 // catchs any exceptions thrown during the process and prints the errors to screen
		  } catch (CtctException $ex) {
			  $result = $ex->getErrors();
		  }

	   }//check if there is a list
	   
	   return $result;
	}

}
?>