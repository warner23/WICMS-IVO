<?php

/**
* 
*/
class profile_tabs
{
	
	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->Profile  = new WIProfile();
    $this->mod     = new WIModules();
    $this->image  = new WIImage();
	}

	public function mod_name()
	{
        $thisRandNum = rand(9999999999999,999999999999999999);
        		$userId = WISession::get("user_id");
        	$friendId = WISession::get("friendId");
        	if($friendId > 0){

        		echo '
             <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script>
          $( function() {
            $( "#tabs" ).tabs();
          } );
          </script>

        <div id="tabs">
          <ul>
            <li><a href="#tabs-1">' . WILang::get('timeline') . '</a></li>
            <li><a href="#tabs-2">' . WILang::get('about'). ' </a></li>
            <li><a href="#tabs-3">' . WILang::get('saved'). ' </a></li>
            <li><a href="#tabs-4">' . WILang::get('media'). ' </a></li>
            <li><a href="#tabs-5">' . WILang::get('mess'). ' </a></li>
            
          </ul>
          <div id="tabs-1">';
            self::Timeline($friendId);
              echo '
          </div>
          <div id="tabs-2">';
            self::About($friendId);
              echo '
          </div>
          <div id="tabs-3">';
            self::Saved($friendId);
              echo '
          </div>
          <div id="tabs-4">';
            self::Media($friendId);
              echo '
          </div>
          <div id="tabs-5">';
            self::Message($friendId);
              echo '
          </div>';
        	}else{
            echo '
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script>
          $( function() {
            $( "#tabs" ).tabs();
          } );
          </script>

        <div id="tabs">
          <ul>
            <li><a href="#tabs-1">' . WILang::get('timeline') . '</a></li>
            <li><a href="#tabs-2">' . WILang::get('about'). ' </a></li>
            <li><a href="#tabs-3">' . WILang::get('saved'). ' </a></li>
            <li><a href="#tabs-4">' . WILang::get('media'). ' </a></li>
            <li><a href="#tabs-5">' . WILang::get('mess'). ' </a></li>
            
          </ul>
          <div id="tabs-1">';
            self::Timeline($friendId);
              echo '
          </div>
          <div id="tabs-2">';
            self::About($friendId);
              echo '
          </div>
          <div id="tabs-3">';
            self::Saved($friendId);
              echo '
          </div>
          <div id="tabs-4">';
            self::Media($friendId);
              echo '
          </div>
          <div id="tabs-5">';
            self::Message($friendId);
              echo '
          </div>';
          }

        }


        public function About($friendId)
        {
          echo '
            <div class="card">
              <div class="card-body">
                <p class="card-title font-weight-bold">About</p>
                <hr>
                <p class="card-description info">User Information</p>

                <a href="javascript:void(0);" onclick="WIProfile.EditInformation();" class="card-title font-weight-bold"><p class="card-description editinfo">Edit</p></a>
                ';
                 $this->Profile->AboutUser();

                 $this->Profile->EditAboutUser();
                      
                echo '<p class="card-description info">Contact Information</p>
                <a href="javascript:void(0);" onclick="WIProfile.EditContactInformation();" class="card-title font-weight-bold"><p class="card-description editinfo">Edit</p></a>
                ';
                 $this->Profile->ContactUserInfo();
                 $this->Profile->EditContactUserInfo();
                      
                echo '<p class="card-description info">Basic Information</p>
                <a href="javascript:void(0);" onclick="WIProfile.EditBasicInformation();" class="card-title font-weight-bold"><p class="card-description editinfo">Edit</p></a>
                ';
                 $this->Profile->BasicUserInfo();
                 $this->Profile->EditBasicUserInfo();
                      
                echo '


              </div></div>';
        }

        public function Timeline($friendId)
        {
          $thisRandNum = rand(9999999999999,999999999999999999);
          $userId = WISession::get('user_id');
          echo 'Profile Wall

                 <div width="67%" valign="top">
                  <span style="font-size:10px; font-weight:800;">';
                  echo $mainNameLine = $this->Profile->Display_name($friendId);
                  echo '</span>';
                       echo $interactionBox = $this->Profile->interactionBox($userId, $friendId);
                       echo '</div>

                  <div class="interactive" id="friends_requests">
         
                  <div class="interactContainers" id="add_friend">
                        <div  class="cancel" align="right"><a href="#" onclick="WIProfile.toggleInteractContainers(`add_friend`);">cancel</a> </div>
                        Add';
                        $this->Profile->getInfo($friendId, "username" );
                        echo ' as a friend? &nbsp;
                        <a href="#" onclick="WIProfile.addAsFriend(' . $userId . ', ' . $friendId . ')" >Yes</a>
                        <span id="add_friend_loader"><img class="friend" src="../WIAdmin/WIMedia/Img/loading.gif" width="28" height="10" alt="Loading" /></span>
                  </div>
                  
                  <div class="interactContainers" id="remove_friend">
                        <div align="right"><a href="#" onclick="return false" onmousedown="profile.toggleInteractContainers(`remove_friend`);">cancel</a> </div>
                        Remove ';
                        $this->Profile->getInfo($friendId, "username");
                        echo 'from your friend list? &nbsp;
                        <a href="#" onclick="return false" onmousedown="javascript:removeAsFriend(' . $friendId . ',' . $friendId . ',' . $thisRandNum. ');">Yes</a>
                        <span id="remove_friend_loader"><img class="loading_image" src="../WIAdmin/WIMedia/Img/loading.gif" alt="Loading" /></span>
                  </div>
                  <!-- START DIV that serves as an interaction status and results container that only appears when we instruct it to -->
                  <div class="interactiveResults"></div>
                   <!-- START DIV that contains the Private Message form -->
                  <div class="interactContainers" id="private_message" style="background-color: #EAF4FF;">
        <form id="pmForm">
        <font size="+1">Sending Private Message to <strong><em>';
        $this->Profile->getInfo($friendId, "username");#
        echo '</em></strong></font><br /><br />
        Subject:
        <input name="pmSubject" id="pmSubject" type="text" maxlength="64" style="width:98%;" />
        Message:
        <textarea name="pmTextArea" id="pmTextArea" rows="4" style="width:98%;"></textarea>
          <input name="pm_sender_id" id="pm_sender_id" type="hidden" value="' . $friendId . '" />
          <input name="pm_sender_name" id="pm_sender_name" type="hidden" value="';$this->Profile->getInfo($friendId, "username"); echo '" />
          <input name="pm_rec_id" id="pm_rec_id" type="hidden" value="' . $userId . '" />
          <input name="pm_rec_name" id="pm_rec_name" type="hidden" value="' . $this->Profile->getInfo($userId, "username") . '" />
          <input name="pmWipit" id="pmWipit" type="hidden" value="' .$thisRandNum . '" />
          <span id="PMStatus" style="color:#F00;"></span>
          <br /><input name="pmSubmit" type="submit" value="Submit" onclick="WIProfile.privateMessage(event);" /> or <a href="#" onclick="WIProfile.toggleInteractContainers(`private_message`);">Close</a>
        <span id="pmFormProcessGif" style="display:none;"><img class="priv_mes" src="../WIAdmin/WIMedia/Img/loading.gif" width="28" height="10" alt="Loading" /></span></form>
                  </div>
                  <!-- END DIV that contains the Private Message form -->
                  <div class="interactContainers" id="friend_requests" style="background-color:#FFF; height:240px; overflow:auto;">
                    <div align="right"><a href="#" onclick="return false" onmousedown="WIProfile.toggleInteractContainers(`friend_requests`);">close window</a> &nbsp; &nbsp; </div>
                    <h3>The following people are requesting you as a friend</h3>
                    </div>';

                     $this->Profile->friendRequests($userId);

                  
                  echo '</div>';
        }

        public function Wall()
        {
          echo '<div>Wall</div>';
        }

        public function Saved()
        {
          echo '<div>Saved</div>';
        }

        public function Media($friendId)
        {
          if($friendId > 0){

          }else{
            $this->image->avatarDisplayPics();
          }
          
        }

        public function Message()
        {
          echo 'Messages';

        // include_once 'WIInc/messages_tabbed.php'; 
              $this->mod->getMod("profile_msg");

          echo '</div>';

        }

}