<?php
#[\AllowDynamicProperties]
class WISupport
{

	  private $userId;

	  private $WIdb;

	  function __construct()
	  {
	    $this->WIdb = WIdb::getInstance();
	    $this->Reg  = new WIRegister();
	    $this->site = new WISite();
	  }

	  public function support()
	  {
	  	echo '<style>
	  	.sup{
	  	    width: 80%;
    margin-left: 14%;
    background-color: white;
    height: 570px;
	  	}
	  .help{
	  	    width: 28%;
    height: 149px;
    float: left;
    padding: 5%;
    border: 2px solid yellow;
	  }

	  	</style>
	  	<ul class="sup">
	  	     <a href="javascript:void(0);" onclick="WISupport.forum()"><li class="help">Forum</li></a>
	  	     <a href="javascript:void(0);" onclick="WISupport.ticket()"><li class="help">Ticket</li></a>
	  	</ul>';
	  }

}



