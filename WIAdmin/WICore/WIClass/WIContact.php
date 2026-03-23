<?php
#[\AllowDynamicProperties]

/**
* Contact Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIContact  
{

    private $mailer;

    private $WIdb = null;

    function __construct() 
    {
         $this->WIdb = WIdb::getInstance();
        //create new object of WIEmail class
        $this->mailer = new WIEmail();

        $this->maint = new WIMaintenace();
    }

    public function sendMessage($name, $email, $subject, $message)
    {
    	$now = date("Y-m-d");
    	$this->WIdb->insert('wi_contact_message', array(
            "name"     => $name,
            "email"  => $email,
            "subject"  => $subject,
            "message" => $message,
            "time_sent" => $now
        ));
    		$msg = "Your MEssage has been successfully sent.";
         $result = array(
                "status" => "success",
                "msg"    => $msg
            );
            
            echo json_encode($result); 
    }

    public function mailCount()
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_contact_message`");
         if( count($result) >0)
        {
            return count($result);
        }else{
            return "0";
        }
    }

    public function Messages()
    {

        $result = $this->WIdb->bindfree("SELECT * FROM `wi_contact_message` ORDER BY `id` DESC");
        foreach ($result as $key => $value) {
            echo '<li class="unread" id="'.$value['id'].'"><!-- start message -->
                    
                    <div class="small-col"> <input type="checkbox" /> </div>
                    <div><i class="fa fa-star"></i> </div>
                    <div class="name"><a href="javascript:void(0);" onclick="WIContact.openMail(`'.$value['id'].'`)"> ' . $value['name'] . '</a></div>
                    <div class="subject">
                      <h4>
                        <a href="javascript:void(0);" onclick="WIContact.openMail(`'.$value['id'].'`)"> ' . $value['subject'] . '</a>
                      </h4>
                      </div>
                      <div class="time"><small><i class="fa fa-clock-o"></i></small>' . $value['time_sent'] . '</div>
                      <div class="hide" id="mess-'.$value['id'].'"><p>' . $value['message'] . '</p></div>
                      
                   
                  </li>';
        }
    }
 }
    