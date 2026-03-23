<?php
#[\AllowDynamicProperties]
/**
* Database Class
* Created by Warner Infinity
* Author Jules Warner
*/

/**
* 
*/
class WIContact 
{

	private $mailer;

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		//create new object of WIEmail class
        $this->mailer = new WIEmail();
	}

	public function Contact($data)
	{
    //var_dump($data);
		$cont = $data['contactData'];

		// put into database
        $this->WIdb->insert('wi_contact_message', array(
            "email"     => $cont['email'],
            "name"  => $cont['name'],
            "subject" => $cont['subject'],
            "message" => $cont['message'],
            "time_sent" => date("Y-m-d")
            
        )); 
		//send email
		$this->mailer->contactEmail($cont['email'], $cont['name'], $cont['subject'], $cont['message']);

		$msg = WILang::get('successfully_send_contact_message');

		 $result = array(
                "status" => "success",
                "msg"    => $msg
            );
            
            echo json_encode($result);
		
	}


	    public function contact_us()
    {
        echo '<div class="alert alert-success hide alert-dismissable" id="contactSuccess">              
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>             
   <strong>Success!</strong> Your message has been sent to us.            
   </div>                       
   <div class="alert alert-danger hide" id="contactError">              
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>            
    <strong>Error!</strong> <span class="errorMessage">There was an error sending your message.</span>            
    </div>                        
    <form id="contactForm" novalidate="novalidate" class="form-horizontal Contact">   
    <fieldset>
    <style>
    input, textarea, .uneditable-input {
    width: 78% !important;
}
    </style>

    <div class="control-group form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label class="col-lg-2 col-md-2 col-sm-2 col-xs-2" for="your_name" >
         <span class="input-group-text"> 
         <i class="fa fa-user" title="Name"></i> 
        </span></label>
    <input type="text" class="cfield" id="name" name="name" class="form-control inputname" data-msg-required="Please enter your name." value="" placeholder="Your Name" required >

                            </div>
                        </div>

    <div class="control-group  form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label class="col-lg-2 col-md-2 col-sm-2 col-xs-2" for="your_email" >
              <span class="input-group-text"> 
                    <i class="fa fa-envelope" title="email"></i> 
                        </span></label>
                            
                       <input type="email" class="cfield" id="email" name="email" class="form-control inputname" maxlength="100" data-msg-email="Please enter a valid email address." data-msg-required="Please enter your email address." value="" placeholder="Your E-mail" required> 
                            </div>
                        </div>

     <div class="control-group  form-group">
     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label class="col-lg-2 col-md-2 col-sm-2 col-xs-2" for="your_subject" >
              <span class="input-group-text"> 
                    <i class="fa fa-envelope" title="subject"></i> 
                        </span></label>
                            
                         <input type="text" class="cfield" id="subject" name="subject" class="form-control inputname" maxlength="100" data-msg-required="Please enter the subject." value="" placeholder="Subject" required>
                            </div>
                        </div>

     <div class="control-group  form-group">
     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label class="col-lg-2 col-md-2 col-sm-2 col-xs-2" for="your_message" >
              <span class="input-group-text"> 
                    <i class="fa fa-message" title="message"></i> 
                        </span></label>
                            
                        <textarea id="message" class="nfield" name="message" rows="8" cols="50" data-msg-required="Please enter your message." maxlength="5000" placeholder="Type your message here." required class="message"> </textarea> 
                            </div>
                        </div>

            
    <div class="col-md-12 col-lg-12 col-sm-12">                 
    <input type="submit" data-loading-text="Loading..." class="btn btn-primary" id="contact" value="Send Message">                
    </div>  
                
    </form>
';
    }





}