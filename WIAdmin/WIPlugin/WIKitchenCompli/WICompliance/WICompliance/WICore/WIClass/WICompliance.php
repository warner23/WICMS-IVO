<?php
#[\AllowDynamicProperties]
/**
* Compliance Class
* Created by Warner Infinity
* Author Jules Warner
*/

/**
* 
*/
class WICompliance 
{

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
        $this->Encrypt = new WIEncryption();
	}


    public function StaffSite($id)
    {
        $res = $this->WIdb->select('SELECT * FROM `wi_user_details` WHERE `user_id`=:id',array(
            "id" => $id
        ));
        if($res > 0){
            $rid = $res[0]['staff_site'];
          return $rid;
            
        }
    }

    public function StaffSiteName($id)
    {
        //echo "id ".$id;
        $res = $this->WIdb->select('SELECT * FROM `wi_user_details` WHERE `user_id`=:id',array(
            "id" => $id
        ));
        if($res > 0){
            //var_dump($res);
            $rid = $res[0]['staff_site'];
            $results = $this->WIdb->select('SELECT * FROM `wi_sites` WHERE `id`=:rid',array(
            "rid" => $rid
        ));
            if($results > 0){
                return $results[0]['name'];
            }
        }
    }

    public function Compliance()
    {
        echo '<h3 class="text-center">'. WEBSITE_NAME .' - '.$this->StaffSiteName(WISession::get('user_id')).'</h3>';
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_compliance`');
        if($results > 0){
            echo ' <div class="col-lg-12 col-xs-12">';
            foreach($results as $res){
                 echo ' <div class="col-lg-3 col-xs-12">
                            <!-- small box -->
                            <div class="small-box styleswitcher" style="padding:7%;">
                                <div class="inner">
                                    <h3 id="' .$res['name'] . '">
                                        <a href="checklists.php">' .$res['name'] . '</a>

                                    </h3>
                                    <p>
                                       
                                    </p>
                                </div>
                                
                            </div>
                        </div><!-- ./col -->';
            }
            echo '</div>';
        }
    }

    public function checklists()
    {
        $check = $this->WIdb->bindfree('SELECT * FROM `wi_compliance_checklist`');
        if($check > 0){
            return $check[0]['id'];
        }
    }

    public function userChecklists()
    {
        $date = date("Y-m-d");
        $time = date('H:i:s');
        $site = $this->StaffSite(WISession::get('user_id'));
        $MCstartTime = "07:00:00";
        $ECstartTime = "17:00:00";
        $checklists = $this->WIdb->bindfree('SELECT * FROM `wi_compliance_checklist` ORDER BY `name` DESC');

        //var_dump($results);
        echo '<style>
        @media screen and (max-width:315px){
        .checks {
    border: 2px solid;
    background-color: silver;
    width: 149%;
    margin-left: -23%;
          }
    }

    @media screen and (max-width:415px){
        .checks {
    border: 2px solid;
    background-color: silver;
    width: 130%;
    margin-left: -16%;
                }
    }
        .sidin{
            width: 45%;
            float: right;
        }
        .checks{
            border:2px solid;
            background-color: #ededed;
        }

        .c{
            padding: 2%;
            border: 2px solid green;
            height: 45px;
            background-color: #a2deb5;
            margin: 1%;
        }

        .d{
            padding: 2%;
            border: 2px solid red;
            height: 45px;
            background-color: red;
            margin: 2%;
        }
        </style>
        <a href="compdash.php">Back to Dashboard</a>
        <h3 class="text-center">CHECKLISTS</h3>
        <ul id="checks" class="checks">';
        //echo "time ".$time;
                       // echo "timestrt ".$ECstartTime;
        if($checklists > 0){
        foreach($checklists as $res){

            if($res['date'] === ""){      
                    if($res['time'] === "" && $time > "12:00:00"){
                    //echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                }elseif($res['time'] === "" && $time < "12:00:00"){
                    if($res['name'] === "Morning Checks"){
                        if($time >= $MCstartTime){
                        echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                    }else{
                        echo '<li class="c">' .$res['name'] . ' Opens at 07AM <div class="sidin">' .$res['side'] . '</div></li>';
                    }
                    }elseif($res['name'] === "Evening Checks"){
                        
                        if($time >= $ECstartTime){
                        echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                    }else{
                        echo '<li class="c">' .$res['name'] . ' Opens at 5pm <div class="sidin">' .$res['side'] . '</div></li>';
                    }
                    
                    
                }else{
                        echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                    }
                
                }

                  
            }elseif($res['date'] != $date){
                 if($res['time'] === "" && $time > "12:00:00"){
                    echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                }elseif($res['time'] === "" && $time < "12:00:00"){
                    if($res['name'] === "Morning Checks"){
                        if($time >= $MCstartTime){
                        echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                    }else{
                        echo '<li class="c">' .$res['name'] . ' Opens at 07AM <div class="sidin">' .$res['side'] . '</div></li>';
                    }
                    }elseif($res['name'] === "Evening Checks"){
                        if($time >= $ECstartTime){
                        echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                    }else{
                        echo '<li class="c">' .$res['name'] . ' Opens at 5pm <div class="sidin">' .$res['side'] . '</div></li>';
                    }
                    
                    
                }else{
                        echo '<li class="c"><a href="' .$res['href'] . '">' .$res['name'] . '</a><div class="sidin">' .$res['side'] . '</div></li>';
                    }
                
                }
            }


          }// end for
       }

        echo '</ul>';
    }

    public function MorningChecks()
    {
        $name = "Morning checks";
        //echo "user". WISession::get('user_id');
        $site = $this->StaffSite(WISession::get('user_id'));
        //echo $site.'site';
        $check = $this->checkId($name);

                $this->WIdb->update(
                    "wi_compliance_overview",
                     array(
                         "status" => 'opened' 
                     ),
                     "`checklist_id` = :check AND `site_id`=:site",
                    array( "check" => $check,"site" => $site )
                );

        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_sections` WHERE `name`=:name', array(
            "name" => $name
        ));
        echo '<style>
    @media screen and (max-width:400px){
        .modal-body{
            margin-left: -15%;
            width: 130%;
        }
    }
        .fridges{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .freezers{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .ui-accordion-content-active{
            min-height:60px;
        }

        .ctitle{
           text-align: center; 
           border: 2px solid; 
           background-color:white;
        }

@media screen and (max-width:555px){
     .quest{
        font-size: 75%;
    }
}

@media screen and (min-width:555px){
    .quest{
        font-size: 100%;
    }
    }



        .fridgeTemps{
           text-align: center;
           float: left;
        }

        .freezerTemps{
           text-align: center;
           float: left;
        }

        .btn-fridges{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .btn-freezers{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .fridgeTemps{

        }

        .addFridges{

        }

        .save{
    width: 100%;
    border: 2px solid green;
    padding: 1%;
    background-color: green;
    color: white; 
        }

        .dropdown-menu {
    top: 41%;
    left: 108px;

}


@media screen and (max-width:555px){
     .btn-switch {
  font-size: .4em !important;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }


.btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}


.btn-switch__radio {
    display: none;
}
.btn-switch__label {
    display: inline-block;  
    padding: .75em .5em .75em .75em;
    vertical-align: top;
    font-size: 1em;
    font-weight: 700;
    line-height: 1.5;
    color: #666;
  cursor: pointer;
    transition: color .2s ease-in-out;
}
.btn-switch__label + .btn-switch__label {
  padding-right: .75em;
    padding-left: 0;
}
.btn-switch__txt {
    position: relative;
    z-index: 2;
  display: inline-block;
   min-width: 1.5em;
    opacity: 1;
    pointer-events: none;
    transition: opacity .2s ease-in-out;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes .btn-switch__txt,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no .btn-switch__txt {
    opacity: 0;
}
.btn-switch__label:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #f0f0f0;
    border-radius: 1.5em;
    box-shadow: inset 0 .0715em .3572em rgba(43,43,43,.05);
    transition: background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label:before {
    background: #6ad500;
}
.btn-switch__label_no:after {
    content: "";
    position: absolute;
    z-index: 2;
    top: .5em;
    bottom: .5em;
    left: .5em;
    width: 2em;
    background: #fff;
    border-radius: 1em; 
    pointer-events: none;
    box-shadow: 0 .1429em .2143em rgba(43,43,43,.2), 0 .3572em .3572em rgba(43,43,43,.1);
    transition: left .2s ease-in-out, background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:after {
    left: calc(100% - 2.5em);
    background: #fff;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes:before,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:before {
    z-index: 1;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_yes {
    color: #fff;
}
        </style>
        <div id="accordion">';
        if($results > 0){
            foreach($results as $res){
                echo '<h4 class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ctitle" id="' .$res['function'] . '">' .$res['section'] . '</h3>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $function = $res['function'];
                self::$function();
                echo '</div>';
            }
        }else{
            echo "No results found.";
        }

        echo '<div class="">
        <a href="javascript:void(0);" class="save" onclick="morningChecks.save();">Save</a>
        </div>
        <div id="mcmessageBox"></div>
        </div>';
    }


    public function EveningChecks()
    {
        $name = "Evening Checks";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_sections` WHERE `name`=:name', array(
            "name" => $name
        ));
        echo '<style>
    @media screen and (max-width:400px){
        .modal-body{
            margin-left: -15%;
            width: 130%;
        }
    }
        .fridges{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .freezers{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .ui-accordion-content-active{
            min-height:60px;
        }

        .ctitle{
           text-align: center; 
           border: 2px solid; 
           background-color:white;
        }

@media screen and (max-width:555px){
     .quest{
        font-size: 75%;
    }
}

@media screen and (min-width:555px){
    .quest{
        font-size: 100%;
    }
    }

        .fridgeTemps{
           text-align: center;
           float: left;
        }

        .freezerTemps{
           text-align: center;
           float: left;
        }

        .btn-fridges{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .btn-freezers{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .fridgeTemps{

        }

        .addFridges{

        }

        .save{
    width: 100%;
    border: 2px solid green;
    padding: 1%;
    background-color: green;
    color: white; 
        }

        .dropdown-menu {
    top: 41%;
    left: 108px;

}

.cooked{
    width: 100%;
    height: 177px;
}

@media screen and (max-width:555px){
     .btn-switch {
  font-size: .4em !important;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }

@media screen and (min-width:555px){
     .btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
}

.btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.btn-switch__radio {
    display: none;
}
.btn-switch__label {
    display: inline-block;  
    padding: .75em .5em .75em .75em;
    vertical-align: top;
    font-size: 1em;
    font-weight: 700;
    line-height: 1.5;
    color: #666;
  cursor: pointer;
    transition: color .2s ease-in-out;
}
.btn-switch__label + .btn-switch__label {
  padding-right: .75em;
    padding-left: 0;
}
.btn-switch__txt {
    position: relative;
    z-index: 2;
  display: inline-block;
   min-width: 1.5em;
    opacity: 1;
    pointer-events: none;
    transition: opacity .2s ease-in-out;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes .btn-switch__txt,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no .btn-switch__txt {
    opacity: 0;
}
.btn-switch__label:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #f0f0f0;
    border-radius: 1.5em;
    box-shadow: inset 0 .0715em .3572em rgba(43,43,43,.05);
    transition: background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label:before {
    background: #6ad500;
}
.btn-switch__label_no:after {
    content: "";
    position: absolute;
    z-index: 2;
    top: .5em;
    bottom: .5em;
    left: .5em;
    width: 2em;
    background: #fff;
    border-radius: 1em; 
    pointer-events: none;
    box-shadow: 0 .1429em .2143em rgba(43,43,43,.2), 0 .3572em .3572em rgba(43,43,43,.1);
    transition: left .2s ease-in-out, background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:after {
    left: calc(100% - 2.5em);
    background: #fff;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes:before,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:before {
    z-index: 1;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_yes {
    color: #fff;
}
        </style>
        <div id="accordion">';
        if($results > 0){
            foreach($results as $res){
                echo '<h4 class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ctitle" id="' .$res['function'] . '">' .$res['section'] . '</h3>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $function = $res['function'];
                self::$function();
                echo '</div>';
            }
        }else{
            echo "No results found.";
        }

        echo '<div class="">
        <a href="javascript:void(0);" class="save" onclick="eveningChecks.save();">Save</a>
        </div>
        <div id="ecmessageBox"></div>
        </div>';
    }

    public function DeliveryChecks()
    {
        $name = "Delivery Checks";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_sections` WHERE `name`=:name', array(
            "name" => $name
        ));
        echo '<style>

    @media screen and (max-width:400px){
        .modal-body{
            margin-left: -15%;
            width: 130%;
        }
    }
        .fridges{
    
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .freezers{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .ui-accordion-content-active{
            min-height:60px;
        }

        .ctitle{
           text-align: center; 
           border: 2px solid; 
           background-color:white;
        }

@media screen and (max-width:555px){
     .quest{
        font-size: 75%;
    }
}

@media screen and (min-width:555px){
    .quest{
        font-size: 100%;
    }
    }

        .fridgeTemps{
           text-align: center;
           float: left;
        }

        .freezerTemps{
           text-align: center;
           float: left;
        }

        .btn-fridges{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .btn-freezers{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .fridgeTemps{

        }

        .addFridges{

        }
        .ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all{
            background-color:white;
        }

        .ui-datepicker-calendar{
         background-color:white;
        }

        .ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all{
            background-color:white;
        }

        .labelling{
            float:left;
        }

        .deliveryInput{
                float: left;
             margin-left: 2%;
        }

        .save{
    width: 100%;
    border: 2px solid green;
    padding: 1%;
    background-color: green;
    color: white; 
        }

        .dropdown-menu {
    top: 41%;
    left: 108px;

}

@media screen and (max-width:555px){
     .btn-switch {
  font-size: .4em !important;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }

@media screen and (min-width:555px){
     .btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
}

.btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.btn-switch__radio {
    display: none;
}
.btn-switch__label {
    display: inline-block;  
    padding: .75em .5em .75em .75em;
    vertical-align: top;
    font-size: 1em;
    font-weight: 700;
    line-height: 1.5;
    color: #666;
  cursor: pointer;
    transition: color .2s ease-in-out;
}
.btn-switch__label + .btn-switch__label {
  padding-right: .75em;
    padding-left: 0;
}
.btn-switch__txt {
    position: relative;
    z-index: 2;
  display: inline-block;
   min-width: 1.5em;
    opacity: 1;
    pointer-events: none;
    transition: opacity .2s ease-in-out;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes .btn-switch__txt,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no .btn-switch__txt {
    opacity: 0;
}
.btn-switch__label:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #f0f0f0;
    border-radius: 1.5em;
    box-shadow: inset 0 .0715em .3572em rgba(43,43,43,.05);
    transition: background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label:before {
    background: #6ad500;
}
.btn-switch__label_no:after {
    content: "";
    position: absolute;
    z-index: 2;
    top: .5em;
    bottom: .5em;
    left: .5em;
    width: 2em;
    background: #fff;
    border-radius: 1em; 
    pointer-events: none;
    box-shadow: 0 .1429em .2143em rgba(43,43,43,.2), 0 .3572em .3572em rgba(43,43,43,.1);
    transition: left .2s ease-in-out, background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:after {
    left: calc(100% - 2.5em);
    background: #fff;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes:before,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:before {
    z-index: 1;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_yes {
    color: #fff;
}

        </style>
        <script>
  $( function() {
    $( "#date" ).datepicker();
  } );
  </script>
        <div id="accordion">';
        if($results > 0){
            foreach($results as $res){
                echo '<h4 class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ctitle" id="' .$res['function'] . '">' .$res['section'] . '</h4>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $function = $res['function'];
                self::$function();
                echo '</div>';
            }
        }else{
            echo "No results found.";
        }

        echo '<div class="">
        <a href="javascript:void(0);" class="save" onclick="deliveryChecks.save();">Save</a>
        </div>
        <div id="dcmessageBox"></div>
        </div>';
    }

    public function DailyCleaning()
    {
        $name = "Daily Cleaning";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_sections` WHERE `name`=:name', array(
            "name" => $name
        ));
        echo '<style>

    @media screen and (max-width:400px){
        .modal-body{
            margin-left: -15%;
            width: 130%;
        }
.dailyClean{
            margin-left: -15%;
            width: 130%;
        }

    }
        .fridges{
    
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .freezers{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .ui-accordion-content-active{
            min-height:60px;
        }

        .ctitle{
           text-align: center; 
           border: 2px solid; 
           background-color:white;
        }

@media screen and (max-width:555px){
     .quest{
        font-size: 75%;
    }
}

@media screen and (min-width:555px){
    .quest{
        font-size: 100%;
    }
    }

        .fridgeTemps{
           text-align: center;
           float: left;
        }

        .freezerTemps{
           text-align: center;
           float: left;
        }

        .btn-fridges{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .btn-freezers{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .fridgeTemps{

        }

        .addFridges{

        }
        .ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all{
            background-color:white;
        }

        .ui-datepicker-calendar{
         background-color:white;
        }

        .ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all{
            background-color:white;
        }

        .labelling{
            float:left;
        }

        .deliveryInput{
                float: left;
    margin-left: 2%;
        }
    .app{
        width:30%;
    }

    .save{
    width: 100%;
    border: 2px solid green;
    padding: 1%;
    background-color: green;
    color: white; 
        }

        .dropdown-menu {
    top: 41%;
    left: 108px;
    
}


@media screen and (max-width:555px){
     .btn-switch {
  font-size: .4em !important;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }

@media screen and (min-width:555px){
     .btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }

.btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.btn-switch__radio {
    display: none;
}
.btn-switch__label {
    display: inline-block;  
    padding: .75em .5em .75em .75em;
    vertical-align: top;
    font-size: 1em;
    font-weight: 700;
    line-height: 1.5;
    color: #666;
  cursor: pointer;
    transition: color .2s ease-in-out;
}
.btn-switch__label + .btn-switch__label {
  padding-right: .75em;
    padding-left: 0;
}
.btn-switch__txt {
    position: relative;
    z-index: 2;
  display: inline-block;
   min-width: 1.5em;
    opacity: 1;
    pointer-events: none;
    transition: opacity .2s ease-in-out;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes .btn-switch__txt,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no .btn-switch__txt {
    opacity: 0;
}
.btn-switch__label:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #f0f0f0;
    border-radius: 1.5em;
    box-shadow: inset 0 .0715em .3572em rgba(43,43,43,.05);
    transition: background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label:before {
    background: #6ad500;
}
.btn-switch__label_no:after {
    content: "";
    position: absolute;
    z-index: 2;
    top: .5em;
    bottom: .5em;
    left: .5em;
    width: 2em;
    background: #fff;
    border-radius: 1em; 
    pointer-events: none;
    box-shadow: 0 .1429em .2143em rgba(43,43,43,.2), 0 .3572em .3572em rgba(43,43,43,.1);
    transition: left .2s ease-in-out, background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:after {
    left: calc(100% - 2.5em);
    background: #fff;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes:before,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:before {
    z-index: 1;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_yes {
    color: #fff;
}

        </style>
        
        <div id="accordion">';
        if($results > 0){
            foreach($results as $res){
                echo '<h4 class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ctitle" id="' .$res['function'] . '">' .$res['section'] . '</h4>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $function = $res['function'];
                self::$function();
                echo '</div>';
            }
        }else{
            echo "No results found.";
        }

        echo '<div class="">
        <a href="javascript:void(0);" class="save" onclick="dailyCleaning.save();">Save</a>
        </div>
        <div id="cleaningmessageBox"></div>
        </div>';
    }


    public function DeepCleaning()
    {
        $name = "Deep Cleaning";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_sections` WHERE `name`=:name', array(
            "name" => $name
        ));
        echo '<style>

    @media screen and (max-width:400px){
        .modal-body{
            margin-left: -15%;
            width: 130%;
        }
    }
        .fridges{
    
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .freezers{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .ui-accordion-content-active{
            min-height:60px;
        }

        .ctitle{
           text-align: center; 
           border: 2px solid; 
           background-color:white;
        }

@media screen and (max-width:555px){
     .quest{
        font-size: 75%;
    }
}

@media screen and (min-width:555px){
    .quest{
        font-size: 100%;
    }
    }

        .fridgeTemps{
           text-align: center;
           float: left;
        }

        .freezerTemps{
           text-align: center;
           float: left;
        }

        .btn-fridges{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .btn-freezers{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .fridgeTemps{

        }

        .addFridges{

        }
        .ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all{
            background-color:white;
        }

        .ui-datepicker-calendar{
         background-color:white;
        }

        .ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all{
            background-color:white;
        }

        .labelling{
            float:left;
        }

        .deliveryInput{
                float: left;
    margin-left: 2%;
        }
    .app{
        width:30%;
    }

    .save{
    width: 100%;
    border: 2px solid green;
    padding: 1%;
    background-color: green;
    color: white; 
        }

        .dropdown-menu {
    top: 41%;
    left: 108px;
   
}

@media screen and (max-width:555px){
     .btn-switch {
  font-size: .4em !important;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }

@media screen and (min-width:555px){
     .btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }

.btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.btn-switch__radio {
    display: none;
}
.btn-switch__label {
    display: inline-block;  
    padding: .75em .5em .75em .75em;
    vertical-align: top;
    font-size: 1em;
    font-weight: 700;
    line-height: 1.5;
    color: #666;
  cursor: pointer;
    transition: color .2s ease-in-out;
}
.btn-switch__label + .btn-switch__label {
  padding-right: .75em;
    padding-left: 0;
}
.btn-switch__txt {
    position: relative;
    z-index: 2;
  display: inline-block;
   min-width: 1.5em;
    opacity: 1;
    pointer-events: none;
    transition: opacity .2s ease-in-out;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes .btn-switch__txt,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no .btn-switch__txt {
    opacity: 0;
}
.btn-switch__label:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #f0f0f0;
    border-radius: 1.5em;
    box-shadow: inset 0 .0715em .3572em rgba(43,43,43,.05);
    transition: background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label:before {
    background: #6ad500;
}
.btn-switch__label_no:after {
    content: "";
    position: absolute;
    z-index: 2;
    top: .5em;
    bottom: .5em;
    left: .5em;
    width: 2em;
    background: #fff;
    border-radius: 1em; 
    pointer-events: none;
    box-shadow: 0 .1429em .2143em rgba(43,43,43,.2), 0 .3572em .3572em rgba(43,43,43,.1);
    transition: left .2s ease-in-out, background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:after {
    left: calc(100% - 2.5em);
    background: #fff;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes:before,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:before {
    z-index: 1;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_yes {
    color: #fff;
}
        </style>
        
        <div id="accordion">';
        if($results > 0){
            foreach($results as $res){
                echo '<h4 class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ctitle" id="' .$res['function'] . '">' .$res['section'] . '</h4>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $function = $res['function'];
                self::$function();
                echo '</div>';
            }
        }else{
            echo "No results found.";
        }

        echo '<div class="">
        <a href="javascript:void(0);" class="save" onclick="deepCleaning.save();">Save</a>
        </div>
        <div id="cleaningmessageBox"></div>
        </div>';
    }


    public function EditMorningChecks($id)
    {
        $name = "edit Morning Checks";

        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_sections` WHERE `name`=:name', array(
            "name" => $name
        ));
        echo '<style>
    @media screen and (max-width:400px){
        .modal-body{
            margin-left: -15%;
            width: 130%;
        }
    }
        .fridges{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .freezers{
    height: 31px;
    padding: 3%;
    margin-bottom: 2%;
    width: 39%;
    float: left;
        }

        .ui-accordion-content-active{
            min-height:60px;
        }

        .ctitle{
           text-align: center; 
           border: 2px solid; 
           background-color:white;
        }

@media screen and (max-width:555px){
     .quest{
        font-size: 75%;
    }
}

@media screen and (min-width:555px){
    .quest{
        font-size: 100%;
    }
    }



        .fridgeTemps{
           text-align: center;
           float: left;
        }

        .freezerTemps{
           text-align: center;
           float: left;
        }

        .btn-fridges{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .btn-freezers{
            color: #fff;
            background-color: #999999;
            border-color: transparent;
            padding: 6px 6px !important;
        }

        .fridgeTemps{

        }

        .addFridges{

        }

        .save{
    width: 100%;
    border: 2px solid green;
    padding: 1%;
    background-color: green;
    color: white; 
        }

        .dropdown-menu {
    top: 41%;
    left: 108px;

}


@media screen and (max-width:555px){
     .btn-switch {
  font-size: .4em !important;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
    }


.btn-switch {
  font-size: 1em;
    position: relative;
    display: inline-block;      
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}


.btn-switch__radio {
    display: none;
}
.btn-switch__label {
    display: inline-block;  
    padding: .75em .5em .75em .75em;
    vertical-align: top;
    font-size: 1em;
    font-weight: 700;
    line-height: 1.5;
    color: #666;
  cursor: pointer;
    transition: color .2s ease-in-out;
}
.btn-switch__label + .btn-switch__label {
  padding-right: .75em;
    padding-left: 0;
}
.btn-switch__txt {
    position: relative;
    z-index: 2;
  display: inline-block;
   min-width: 1.5em;
    opacity: 1;
    pointer-events: none;
    transition: opacity .2s ease-in-out;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes .btn-switch__txt,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no .btn-switch__txt {
    opacity: 0;
}
.btn-switch__label:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #f0f0f0;
    border-radius: 1.5em;
    box-shadow: inset 0 .0715em .3572em rgba(43,43,43,.05);
    transition: background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label:before {
    background: #6ad500;
}
.btn-switch__label_no:after {
    content: "";
    position: absolute;
    z-index: 2;
    top: .5em;
    bottom: .5em;
    left: .5em;
    width: 2em;
    background: #fff;
    border-radius: 1em; 
    pointer-events: none;
    box-shadow: 0 .1429em .2143em rgba(43,43,43,.2), 0 .3572em .3572em rgba(43,43,43,.1);
    transition: left .2s ease-in-out, background .2s ease-in-out;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:after {
    left: calc(100% - 2.5em);
    background: #fff;
}
.btn-switch__radio_no:checked ~ .btn-switch__label_yes:before,
.btn-switch__radio_yes:checked ~ .btn-switch__label_no:before {
    z-index: 1;
}
.btn-switch__radio_yes:checked ~ .btn-switch__label_yes {
    color: #fff;
}
        </style>
        
  <script>
  $( function() {
    $( "#morningchecks" ).accordion({
      heightStyle: "content"
    });
  } );
  </script>
        <div id="morningchecks">';
        if($results > 0){
            foreach($results as $res){
                echo '<h4 class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ctitle" id="' .$res['function'] . '">' .$res['section'] . '</h3>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $function = $res['function'];
                self::$function($id);
                echo '</div>';
            }
        }else{
            echo "No results found.";
        }

        echo '<div class="">
        <a href="javascript:void(0);" class="save" onclick="morningChecks.save();">Save</a>
        </div>
        <div id="mcmessageBox"></div>
        </div>';
    }


    //****************************************************************************//
    //***                    checklist functions                                              ******//
    //**************************************************************************//

	public function FridgeTemps()
    {
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_fridges`');

        if($results > 0){
            echo '<form>
            <div style="color:red">Fridges should be set at 5c or ideally below so they can maintain food below the legal temperature of 8c.</div><ul id="tempfridges">';
            foreach($results as $res){
                echo '<li id="' . $res['no']. '" class=""><div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 fridgeTemps">
                    <label>' . $res['no']. '</label>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <input type="number" class="col-lg-8 col-xs-12 fridges" id="fridge-' . $res['no']. '" data-id="' . $res['no']. '" placeholder="3">C


                     <a class="btn btn-fridges dropdown-toggle" data-toggle="dropdown" href="#">Options
                    <span class=""></span>
                                  </a>
                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.faultFridge(`' . $res['no']. '`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              Fridge Faultly
                                          </a>
                                      </li>
                                  </ul>
                    </div>


                    <script>
                        $("#fridge-' . $res['no']. '").change(function(){
                            
                            if($("#fridge-' . $res['no']. '").val() > 7){
                            $("#fridge-' . $res['no']. '").css("background-color","red");
                            $("#fridge-' . $res['no']. '").css("color","white");
                            }else{
                            $("#fridge-' . $res['no']. '").css("background-color","green");
                            $("#fridge-' . $res['no']. '").css("color","white");
                            }
                            
                       });
                        
                        
                    </script>
                    </li>';
            }

            echo '<script> $("#fridge-4").change(function(){
                            
                            if($("#fridge-4").val() === ""){
                            $("#FridgeTemps").css("background-color","red");
                            $("#FridgeTemps").css("color","white");
                        }else{
                            $("#FridgeTemps").css("background-color","green");
                            $("#FridgeTemps").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <a href="javascript:void(0);" class="addFridges dropdown-toggle btn" data-toggle="dropdown">Add More Fridge Temps
                    </a>
                    <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`1`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 1
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`2`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 2
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`4`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 4
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`6`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 6
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`8`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 8
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`10`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 10
                                          </a>
                                      </li>
                                  </ul>';
        }else{
            echo 'No Results to show.';
        }
        $id = "fridgeTemps";
        echo  self::signedBy($id) . '</ul></form>';
    }

    public function editFridgeTemps($id)
    {

        $results = $this->WIdb->select('SELECT * FROM `wi_fridge_temps` WHERE `group_id`=:id', array(
            "id"  => $id));

        if($results > 0){
            echo '<form>
            <div style="color:red">Fridges should be set at 5c or ideally below so they can maintain food below the legal temperature of 8c.</div><ul id="tempfridges">';
            foreach($results as $res){
                echo '<li id="' . $res['fridge']. '" class=""><div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 fridgeTemps">
                    <label>' . $res['fridge']. '</label>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <input type="number" class="col-lg-8 col-xs-12 fridges" id="' . $res['fridge']. '" data-id="' . $res['fridge']. '" placeholder="3" value="' . $res['temp']. '">C


                     <a class="btn btn-fridges dropdown-toggle" data-toggle="dropdown" href="#">Options
                    <span class=""></span>
                                  </a>
                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.faultFridge(`' . $res['fridge']. '`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              Fridge Faultly
                                          </a>
                                      </li>
                                  </ul>
                    </div>


                    <script>
                        $("#' . $res['fridge']. '").change(function(){
                            
                            if($("#' . $res['fridge']. '").val() > 7){
                            $("#' . $res['fridge']. '").css("background-color","red");
                            $("#' . $res['fridge']. '").css("color","white");
                            }else{
                            $("#' . $res['fridge']. '").css("background-color","green");
                            $("#' . $res['fridge']. '").css("color","white");
                            }
                            
                       });
                        
                        
                    </script>
                    </li>';
            }

            echo '<script> $("#fridge-4").change(function(){
                            
                            if($("#fridge-4").val() === ""){
                            $("#FridgeTemps").css("background-color","red");
                            $("#FridgeTemps").css("color","white");
                        }else{
                            $("#FridgeTemps").css("background-color","green");
                            $("#FridgeTemps").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <a href="javascript:void(0);" class="addFridges dropdown-toggle btn" data-toggle="dropdown">Add More Fridge Temps
                    </a>
                    <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`1`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 1
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`2`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 2
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`4`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 4
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`6`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 6
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`8`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 8
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFridgeTemps(`10`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 10
                                          </a>
                                      </li>
                                  </ul>';
        }else{
            echo 'No Results to show.';
        }
        $fid = "fridgeTemps";
        echo  self::signedBy($fid) . '</ul></form>';
    }

    public function FreezerTemps()
    {
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_freezers`');

        if($results > 0){
             echo '<form>
             <div style="color:red">Freezers MUST operate at -18c or colder.</div>
             <ul id="tempfreezers">';
            foreach($results as $res){
                echo '<li id="freezer-' . $res['no']. '" data-id="' . $res['no']. '">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 freezerTemps">
                    <label>' . $res['no']. '</label>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <input type="number" class="col-lg-8 col-xs-12 freezers" id="' . $res['no']. '" placeholder="-22">C
                     <a class="btn btn-freezers dropdown-toggle" data-toggle="dropdown" href="#">Options
                    <span class=""></span>
                                  </a>
                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.faultFreezer(`' . $res['no']. '`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              Freezer Faulty
                                          </a>
                                      </li>
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.defrostingFreezer(`' . $res['no']. '`);">
                                              <i class="icon-pencil glyphicon glyphicon-pencil"></i>
                                              Freezer switched off defrosting
                                          </a>
                                      </li>
                                  </ul>
                    </div>
                    <script>
                        $("#' . $res['no']. '").change(function(){
                            
                            if($("#' . $res['no']. '").val() < -19){
                            $("#' . $res['no']. '").css("background-color","red");
                            $("#' . $res['no']. '").css("color","white");
                        }else{
                            $("#' . $res['no']. '").css("background-color","green");
                            $("#' . $res['no']. '").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
                    </li>';
            }


            echo '
            <script> $("#D").change(function(){
                            
                            if($("#fridge-D").val() === ""){
                            $("#FreezerTemps").css("background-color","red");
                            $("#FreezerTemps").css("color","white");
                        }else{
                            $("#FreezerTemps").css("background-color","green");
                            $("#FreezerTemps").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <a href="javascript:void(0);" class="addFreezers dropdown-toggle btn" data-toggle="dropdown">Add More Freezer Temps
                    </a>
                    <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`1`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 1
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`2`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 2
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`4`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 4
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`6`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 6
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`8`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 8
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`10`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 10
                                          </a>
                                      </li>
                                  </ul>';

        }else{
            echo 'No Results to show.';
        }
        $id = "freezerTemps";
        echo self::signedBy($id) . '</ul></form>';
    }

    public function editFreezerTemps($id)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_freezer_temps` WHERE `group_id`=:id', array(
            "id"  => $id));

        if($results > 0){
             echo '<form>
             <div style="color:red">Freezers MUST operate at -18c or colder.</div>
             <ul id="tempfreezers">';
            foreach($results as $res){
                echo '<li id="freezer-' . $res['freezer']. '" data-id="' . $res['freezer']. '">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 freezerTemps">
                    <label>' . $res['freezer']. '</label>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <input type="number" class="col-lg-8 col-xs-12 freezers" id="' . $res['freezer']. '" placeholder="-22" value="' . $res['temp']. '">C
                     <a class="btn btn-freezers dropdown-toggle" data-toggle="dropdown" href="#">Options
                    <span class=""></span>
                                  </a>
                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.faultFreezer(`' . $res['freezer']. '`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              Freezer Faulty
                                          </a>
                                      </li>
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.defrostingFreezer(`' . $res['freezer']. '`);">
                                              <i class="icon-pencil glyphicon glyphicon-pencil"></i>
                                              Freezer switched off defrosting
                                          </a>
                                      </li>
                                  </ul>
                    </div>
                    <script>
                        $("#' . $res['freezer']. '").change(function(){
                            
                            if($("#' . $res['freezer']. '").val() < -19){
                            $("#' . $res['freezer']. '").css("background-color","red");
                            $("#' . $res['freezer']. '").css("color","white");
                        }else{
                            $("#' . $res['freezer']. '").css("background-color","green");
                            $("#' . $res['freezer']. '").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
                    </li>';
            }


            echo '
            <script> $("#D").change(function(){
                            
                            if($("#fridge-D").val() === ""){
                            $("#FreezerTemps").css("background-color","red");
                            $("#FreezerTemps").css("color","white");
                        }else{
                            $("#FreezerTemps").css("background-color","green");
                            $("#FreezerTemps").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                    <a href="javascript:void(0);" class="addFreezers dropdown-toggle btn" data-toggle="dropdown">Add More Freezer Temps
                    </a>
                    <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`1`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 1
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`2`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 2
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`4`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 4
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`6`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 6
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`8`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 8
                                          </a>
                                      </li>

                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WICompliance.addFreezerTemps(`10`);">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              add 10
                                          </a>
                                      </li>
                                  </ul>';

        }else{
            echo 'No Results to show.';
        }
        $fid = "freezerTemps";
        echo self::signedBy($fid) . '</ul></form>';
    }

    public function editChefOnDuty($id)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_morning_checks` WHERE `group_id`=:id', array(
            "id"  => $id));
        if($results > 0){
            echo '<form>';

            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
        <div style="color:red">Person responsible for supervising overall Food safety Today</div>
        <label >Chef\'s Full Name</label>
        </div>
        <div class="col-lg-9">
        <input type="text" id="dutyChef" class="col-lg-8 col-xs-12" placeholder="Ben ordish" value="' . $res['chefs_name']. '">
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
        <label >Position</label>
        </div>
        <div class="col-lg-9">
        <input type="text" id="dutyChefPosition" class="col-lg-8 col-xs-12" placeholder="AKM" value="' . $res['chefs_position']. '">
        </div>';
            }
            echo '<script>
                $("#dutyChefPosition").change(function(){
                    
                    if($("#dutyChefPosition").val() === ""){
                    $("#ChefOnDuty").css("background-color","amber");
                    $("#ChefOnDuty").css("color","white");
                }else{
                    $("#ChefOnDuty").css("background-color","green");
                    $("#ChefOnDuty").css("color","white");
                    }
                    
                    });
                    </script>
        </form>';
        }
        
    }

    public function ChefOnDuty()
    {
        echo '<form>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
        <div style="color:red">Person responsible for supervising overall Food safety Today</div>
        <label >Chef\'s Full Name</label>
        </div>
        <div class="col-lg-9">
        <input type="text" id="dutyChef" class="col-lg-8 col-xs-12" placeholder="Ben ordish">
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
        <label >Position</label>
        </div>
        <div class="col-lg-9">
        <input type="text" id="dutyChefPosition" class="col-lg-8 col-xs-12" placeholder="AKM">
        </div>
        <script>
                        $("#dutyChefPosition").change(function(){
                            
                            if($("#dutyChefPosition").val() === ""){
                            $("#ChefOnDuty").css("background-color","yellow");
                            $("#ChefOnDuty").css("color","white");
                        }else{
                            $("#ChefOnDuty").css("background-color","green");
                            $("#ChefOnDuty").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
        </form>';
    }

    public function OpeningChecks()
    {
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_opening_questions`');

        if($results > 0){
            echo '<form class="form-horizontal">
            <div style="color:red">Complete at the start of the day before any food is serve.</div>';
            foreach($results as $res){
               echo '<fieldset>
               <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="text-align: center;">
        <div class="quest">' . $res['question']. '</div>
        </div>
        <div class="col-lg-4 col-xs-4 opening" id="' . $res['id']. '">

        <div class="btn-switch">                  
  <input type="radio"  name="switch-' . $res['id']. '" class="btn-switch__radio btn-switch__radio_yes questions q' . $res['id']. '" value="yes" id="yes-' . $res['id']. '" data-id="' . $res['id']. '" />
  <input type="radio" checked  name="switch-' . $res['id']. '" class="btn-switch__radio btn-switch__radio_no questions q' . $res['id']. '" value="no" id="no-' . $res['id']. '" data-id="' . $res['id']. '" />       
  <label for="yes-' . $res['id']. '" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="no-' . $res['id']. '" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>
        </div>
        <div class="col-lg-12 col-xs-12 hide actions-' . $res['id']. '" style="border:1px solid;">
        <label>Corrective Action Log:T9</label>
        <div class="col-lg-12 col-xs-12">
        <label>Details of problem/Incident</label>
        <textarea id="problem-' . $res['id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Details of action taken or to be taken</label>
        <textarea id="takeAction-' . $res['id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed By</label>
        <input type="text" id="actionSigned-' . $res['id']. '">
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed Date</label>
        <input type="text" id="completeDate-' . $res['id']. '">
        </div>
        </div>
        <script type="text/javascript">


            $(`.q' . $res['id']. '`).click(function(){
                
                $("#OpeningChecks").css("background-color","yellow");
                            $("#OpeningChecks").css("color","white");

            if($(`#' . $res['id']. 'B`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['id']. '").addClass("show check").removeClass("hide"); 
                }

            if($(`#' . $res['id']. 'A`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['id']. '").addClass("hide").removeClass("show check"); 
                }

            })

            $(`.q11`).click(function(){
                $("#OpeningChecks").css("background-color","green");
                            $("#OpeningChecks").css("color","white");
            })

                    </script>

        </fieldset>'; 
            }
        }
        $id = "OpeningChecks";
        echo self::signedBy($id) . '</form>';
        
    }

    public function findOpeningQuestions($id)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_opening_questions` WHERE `id`=:id', array(
            "id" => $id
        ));

        if($results > 0){
            foreach ($results as $res) {
                $question = $res['question'];
                return $question;
            }
        }
    }

    public function editOpeningChecks($id)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_opening_checks` WHERE `group_id`=:id' ,array(
            "id" => $id
        ));

        if($results > 0){
            echo '<form class="form-horizontal">
            <div style="color:red">Complete at the start of the day before any food is serve.</div>';
            foreach($results as $res){
               echo '<fieldset>
               <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="text-align: center;">
        <div class="quest">' . $this->findOpeningQuestions($res['question_id']). '</div>
        </div>
        <div class="col-lg-4 col-xs-4 opening" id="' . $res['question_id']. '">

        <div class="btn-switch">  
        <input type="hidden" id="oqswitch" value="' . $res['answer']. '">                
  <input type="radio"  name="switch-' . $res['question_id']. '" class="btn-switch__radio btn-switch__radio_yes questions q' . $res['question_id']. '" value="yes" id="yes-' . $res['question_id']. '" data-id="' . $res['question_id']. '" />
  <input type="radio" checked  name="switch-' . $res['question_id']. '" class="btn-switch__radio btn-switch__radio_no questions q' . $res['question_id']. '" value="no" id="no-' . $res['question_id']. '" data-id="' . $res['question_id']. '" />       
  <label for="yes-' . $res['question_id']. '" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="no-' . $res['question_id']. '" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>
        </div>
        <div class="col-lg-12 col-xs-12 hide actions-' . $res['question_id']. '" style="border:1px solid;">
        <label>Corrective Action Log:T9</label>
        <div class="col-lg-12 col-xs-12">
        <label>Details of problem/Incident</label>
        <textarea id="problem-' . $res['question_id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Details of action taken or to be taken</label>
        <textarea id="takeAction-' . $res['question_id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed By</label>
        <input type="text" id="actionSigned-' . $res['question_id']. '">
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed Date</label>
        <input type="text" id="completeDate-' . $res['question_id']. '">
        </div>
        </div>
        <script type="text/javascript">

            var quest = $("#oqswitch").val();
            if(quest === "yes"){
                $("#yes-' . $res['question_id']. '").attr("checked", "checked");
                }else{
                    $("#no-' . $res['question_id']. '").attr("checked", "checked");
                }
            $(`.q' . $res['question_id']. '`).click(function(){
                $("#OpeningChecks").css("background-color","green");
                            $("#OpeningChecks").css("color","white");
            if($(`#' . $res['question_id']. 'B`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['question_id']. '").addClass("show check").removeClass("hide"); 
                }

            if($(`#' . $res['question_id']. 'A`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['question_id']. '").addClass("hide").removeClass("show check"); 
                }

            })

                    </script>

        </fieldset>'; 
            }
        }
        $id = "OpeningChecks";
        echo self::signedBy($id) . '</form>';
        
    }

    public function ClosingChecks()
    {
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_closing_questions`');

        if($results > 0){
            echo '<form class="form-horizontal">
            <div style="color:red">Complete at the end of the day.</div>';
            foreach($results as $res){
               echo '<fieldset>
               <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="text-align: center;">
        <div class="quest">' . $res['question']. '</div>
        </div>
        <div class="col-lg-4 col-xs-4 opening" id="' . $res['id']. '">

        <div class="btn-switch">                  
  <input type="radio"  name="switch-' . $res['id']. '" class="btn-switch__radio btn-switch__radio_yes questions q' . $res['id']. '" value="yes" id="yes-' . $res['id']. '" data-id="' . $res['id']. '" />
  <input type="radio" checked  name="switch-' . $res['id']. '" class="btn-switch__radio btn-switch__radio_no questions q' . $res['id']. '" value="no" id="no-' . $res['id']. '" data-id="' . $res['id']. '" />       
  <label for="yes-' . $res['id']. '" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="no-' . $res['id']. '" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>
        
        </div>
        <div class="col-lg-12 col-xs-12 hide actions-' . $res['id']. '" style="border:1px solid;">
        <label>Corrective Action Log:T9</label>
        <div class="col-lg-12 col-xs-12">
        <label>Details of problem/Incident</label>
        <textarea id="problem-' . $res['id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Details of action taken or to be taken</label>
        <textarea id="takeAction-' . $res['id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed By</label>
        <input type="text" id="actionSigned-' . $res['id']. '">
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed Date</label>
        <input type="text" id="completeDate-' . $res['id']. '">
        </div>
        </div>
        <script type="text/javascript">
            $(`.q' . $res['id']. '`).click(function(){

                $("#ClosingChecks").css("background-color","green");
                            $("#ClosingChecks").css("color","white");

            if($(`#' . $res['id']. 'B`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['id']. '").addClass("show check").removeClass("hide"); 
                }

            if($(`#' . $res['id']. 'A`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['id']. '").addClass("hide").removeClass("show check"); 
                }

            })

                    </script>
        </fieldset>'; 
            }
        }
        $id = "ClosingChecks";
        echo self::signedBy($id) . '</form>';
        
    }

    public function findClosingQuestions($id)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_closing_questions` WHERE `id`=:id', array(
            "id" => $id
        ));

        if($results > 0){
            foreach ($results as $res) {
                $question = $res['question'];
                return $question;
            }
        }
    }

    public function editClosingChecks()
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_closing_checks` WHERE `group_id`=:id' ,array(
            "id" => $id
        ));

        if($results > 0){
            echo '<form class="form-horizontal">
            <div style="color:red">Complete at the end of the day.</div>';
            foreach($results as $res){
               echo '<fieldset>
               <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="text-align: center;">
        <div class="quest">' . $this->findClosingQuestions($res['question_id']). '</div>
        </div>
        <div class="col-lg-4 col-xs-4 opening" id="' . $res['question_id']. '">

        <div class="btn-switch">
        <input type="hidden" value="'. $res['answer'] .'">                  
  <input type="radio"  name="switch-' . $res['question_id']. '" class="btn-switch__radio btn-switch__radio_yes questions q' . $res['question_id']. '" value="yes" id="yes-' . $res['question_id']. '" data-id="' . $res['question_id']. '" />
  <input type="radio" checked  name="switch-' . $res['question_id']. '" class="btn-switch__radio btn-switch__radio_no questions q' . $res['question_id']. '" value="no" id="no-' . $res['question_id']. '" data-id="' . $res['question_id']. '" />       
  <label for="yes-' . $res['question_id']. '" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="no-' . $res['question_id']. '" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>
        
        </div>
        <div class="col-lg-12 col-xs-12 hide actions-' . $res['question_id']. '" style="border:1px solid;">
        <label>Corrective Action Log:T9</label>
        <div class="col-lg-12 col-xs-12">
        <label>Details of problem/Incident</label>
        <textarea id="problem-' . $res['question_id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Details of action taken or to be taken</label>
        <textarea id="takeAction-' . $res['question_id']. '"></textarea>
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed By</label>
        <input type="text" id="actionSigned-' . $res['question_id']. '">
        </div>
        <div class="col-lg-12 col-xs-12">
        <label>Completed Date</label>
        <input type="text" id="completeDate-' . $res['question_id']. '">
        </div>
        </div>
        <script type="text/javascript">

        var quest = $("#oqswitch").val();
            if(quest === "yes"){
                $("#yes-' . $res['question_id']. '").attr("checked", "checked");
                }else{
                    $("#no-' . $res['question_id']. '").attr("checked", "checked");
                }
            $(`.q' . $res['question_id']. '`).click(function(){

                $("#ClosingChecks").css("background-color","green");
                            $("#ClosingChecks").css("color","white");

            if($(`#' . $res['question_id']. 'B`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['question_id']. '").addClass("show check").removeClass("hide"); 
                }

            if($(`#' . $res['question_id']. 'A`).is(":checked") ){
                console.log("checked");
                $(".actions-' . $res['question_id']. '").addClass("hide").removeClass("show check"); 
                }

            })

                    </script>
        </fieldset>'; 
            }
        }
        $cid = "ClosingChecks";
        echo self::signedBy($cid) . '</form>';
        
    }

    public function editFridgeChecks($id)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_morning_checks` WHERE `group_id`=:id', array(
            "id" => $id
        ));

        $fid = "fridgeChecks";
        if($results > 0){
            foreach($results as $res){
                echo '<form><div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 fridgeChecks">
        <label>Any out of date food found</label>
        </div>
        <div class="col-lg-8 col-xs-12">

        <div class="btn-switch">
        <input type="hidden" value="' . $res['oodf_found'] .'">                  
  <input type="radio"  name="switch" class="btn-switch__radio btn-switch__radio_yes fcheck" value="yes" id="yfc"  />
  <input type="radio" checked  name="switch" class="btn-switch__radio btn-switch__radio_no fcheck" value="no" id="nfc"  />       
  <label for="yfc" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="nfc" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

        </div>
        <div class="hide fridgec">
            <div>
            <label>Action Taken</label>
            <textarea id="fridgeCAction"></textarea>
            </div>
        </div>';
            }
        }
        
        echo '
        <script type="text/javascript">

        var quest = $("#oqswitch").val();
            if(quest === "yes"){
                $("#yfc").attr("checked", "checked");
                }else{
                    $("#nfc").attr("checked", "checked");
                }
            $(`.fcheck`).click(function(){

            if($(`#yfc`).is(":checked") ){

                $(".fridgec").addClass("show check").removeClass("hide");
                 $("#FridgeChecks").css("background-color","green");
                 $("#FridgeChecks").css("color","white");
                }

            if($(`#nfc`).is(":checked") ){
                $(".fridgec").addClass("hide").removeClass("show check"); 
                $("#FridgeChecks").css("background-color","green");
                $("#FridgeChecks").css("color","white");
                }
            })

            </script>'. self::signedBy($fid).'</form>';
    }

    public function FridgeChecks()
    {
        $id = "fridgeChecks";
        echo '<form><div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 fridgeChecks">
        <label>Any out of date food found</label>
        </div>
        <div class="col-lg-8 col-xs-12">

        <div class="btn-switch">                  
  <input type="radio"  name="switch" class="btn-switch__radio btn-switch__radio_yes fcheck" value="yes" id="yfc"  />
  <input type="radio" checked  name="switch" class="btn-switch__radio btn-switch__radio_no fcheck" value="no" id="nfc"  />       
  <label for="yfc" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="nfc" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

        </div>
        <div class="hide fridgec">
            <div>
            <label>Action Taken</label>
            <textarea id="fridgeCAction"></textarea>
            </div>
        </div>
        <script type="text/javascript">
            $(`.fcheck`).click(function(){

            if($(`#yfc`).is(":checked") ){

                $(".fridgec").addClass("show check").removeClass("hide");
                 $("#FridgeChecks").css("background-color","green");
                 $("#FridgeChecks").css("color","white");
                }

            if($(`#nfc`).is(":checked") ){
                $(".fridgec").addClass("hide").removeClass("show check"); 
                $("#FridgeChecks").css("background-color","green");
                $("#FridgeChecks").css("color","white");
                }
            })

            </script>'. self::signedBy($id).'</form>';
    }

    public function BatchCooking()
    {
        echo '<form>
        <fieldset>
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
         <div style="color:red">To be used for recording cooking and cooling of batch cooked food that is to be reheated for service later. core temp must be over 75c ( 82c in scotland ), and cooled to a suitable temperature for refridgeration within 2 hours.</div>
                <div>
            <ul id="batch">
             <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 batch" id="batch-1"  data-id="1">
                <label>Product</label>
                <input type="text" class="batchCook" id="product-1" name="product" placeholder="Chicken">
                </div>
                <div>
                <label>End of Cooking Time</label>
                <input type="text" class="batchCook" id="eofct-1" placeholder="10:40am" name="end_cook_time">
                </div>
                <div>
                <label>End of cooking Temp</label>
                <input type="text" class="batchCook" id="eoct-1" placeholder="82 C" name="end_cook_temp">
                </div>
                <div>
                <label>Cooking method</label>
                <input type="text" class="batchCook" id="method-1" placeholder="Oven" name="method">
                </div>
                <div>
                <label>End of cooling Time</label>
                <input type="text" class="batchCook" id="eoctime-1" placeholder="11:40 am" name="end_cool_time">
                </div>
                <div>
                <label>End of cooling Temp</label>
                <input type="text" class="batchCook" id="eoctemp-1" placeholder="2 C" name="end_cool_temp">
                </div>
                <div>
                <label>Total Cooling time</label>
                <input type="text" class="batchCook" id="totalCool-1" placeholder="60 mins" name="total_cool_temp">
                </div>
                <div>
                <label>Time in fridge</label>
                <input type="text" class="batchCook" id="time_in_fridge-1" placeholder="11:45 am" name="time_in_fridge">
                </div>
                <div>
                <label>initials</label>
                <input type="text" class="batchCook" id="initials-1" placeholder="BO" name="initials">
                </div>
                    
             </li>
             </ul>
             <a href="#" onclick="WICompliance.addBatch()">Add Batch</a>
             </div>
             <script>
             $("#product-1").change(function(){
                            
                            if($("#product-1").val() === ""){
                            $("#BatchCooking").css("background-color","red");
                            $("#BatchCooking").css("color","white");
                        }else{
                            $("#BatchCooking").css("background-color","green");
                            $("#BatchCooking").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
             </fieldset>
             </form>';
    }

    public function editBatchCooking()
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_batch_cooking` WHERE `group_id`=:id', array(
            "id" => $id
        ));

        if($results > 0){
            echo '<form>
        <fieldset><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
         <div style="color:red">To be used for recording cooking and cooling of batch cooked food that is to be reheated for service later. core temp must be over 75c ( 82c in scotland ), and cooled to a suitable temperature for refridgeration within 2 hours.</div>
                <div>';
            foreach($results as $res){
                echo '
            <ul id="batch">
             <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 batch" id="batch-1"  data-id="1">
                <label>Product</label>
                <input type="text" class="batchCook" id="product-1" name="product" placeholder="Chicken" value="' . $res['product'] .'">
                </div>
                <div>
                <label>End of Cooking Time</label>
                <input type="text" class="batchCook" id="eofct-1" placeholder="10:40am" name="end_cook_time" value="' . $res['end_cook_time'] .'">
                </div>
                <div>
                <label>End of cooking Temp</label>
                <input type="text" class="batchCook" id="eoct-1" placeholder="82 C" name="end_cook_temp" value="' . $res['end_cook_temp'] .'">
                </div>
                <div>
                <label>Cooking method</label>
                <input type="text" class="batchCook" id="method-1" placeholder="Oven" name="method" value="' . $res['method'] .'" >
                </div>
                <div>
                <label>End of cooling Time</label>
                <input type="text" class="batchCook" id="eoctime-1" placeholder="11:40 am" name="end_cool_time" value="' . $res['end_cool_time'] .'>
                </div>
                <div>
                <label>End of cooling Temp</label>
                <input type="text" class="batchCook" id="eoctemp-1" placeholder="2 C" name="end_cool_temp" value="' . $res['end_cool_temp'] .'>
                </div>
                <div>
                <label>Total Cooling time</label>
                <input type="text" class="batchCook" id="totalCool-1" placeholder="60 mins" name="total_cool_temp" value="' . $res['total_cool_temp'] .'>
                </div>
                <div>
                <label>Time in fridge</label>
                <input type="text" class="batchCook" id="time_in_fridge-1" placeholder="11:45 am" name="time_in_fridge" value="' . $res['time_in_fridge'] .'>
                </div>
                <div>
                <label>initials</label>
                <input type="text" class="batchCook" id="initials-1" placeholder="BO" name="initials" value="' . $res['initials'] .'>
                </div>
                    
             </li>
             </ul>
             <a href="#" onclick="WICompliance.addBatch()">Add Batch</a>
             </div>';
            }
        }
        echo '<script>
             $("#product-1").change(function(){
                            
                            if($("#product-1").val() === ""){
                            $("#BatchCooking").css("background-color","red");
                            $("#BatchCooking").css("color","white");
                        }else{
                            $("#BatchCooking").css("background-color","green");
                            $("#BatchCooking").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
             </fieldset>
             </form>';
    }

    public function DishwasherChecks()
    {
        $id = "dishwasherChecks";
        echo '<form></fieldset>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div style="color:red">Record from the dial on the machine or a specific dishwasher thermostat, allow it to warm up for 30 minutes before taking the reading, take the temperature of the final rinse cycle NOT the washing cycle.</div>
                <div>
                <label>Temperature</label>
                <input type="number" id="dishTemp" placeholder="75">C
                </div>
                <div>
                <label>Unit Clean</label>
                <div class="col-lg-8 col-xs-12">

                <div class="btn-switch">                  
  <input type="radio"  name="switch" class="btn-switch__radio btn-switch__radio_yes unitClean" value="yes" id="yuc"  />
  <input type="radio" checked  name="switch" class="btn-switch__radio btn-switch__radio_no unitClean" value="no" id="nuc"  />       
  <label for="yuc" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="nuc" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

                </div>
                </div>
                <div>
                <label>Time Taken</label>
                <input type="text" id="dishTime">
                </div>';
                echo self::signedBy($id);
           echo  '</div>
           <script>
           $("#dishTemp").change(function(){
                            
                            if($("#dishTemp").val() === ""){
                            $("#DishwasherChecks").css("background-color","red");
                            $("#DishwasherChecks").css("color","white");
                        }else{
                            $("#DishwasherChecks").css("background-color","green");
                            $("#DishwasherChecks").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
         </fieldset></form>';
    }

    public function editDishwasherChecks($id)
    {
        $fid = "dishwasherChecks";
        echo '<form></fieldset>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div style="color:red">Record from the dial on the machine or a specific dishwasher thermostat, allow it to warm up for 30 minutes before taking the reading, take the temperature of the final rinse cycle NOT the washing cycle.</div>
                <div>
                <label>Temperature</label>
                <input type="number" id="dishTemp" placeholder="75">C
                </div>
                <div>
                <label>Unit Clean</label>
                <div class="col-lg-8 col-xs-12">

                <div class="btn-switch">                  
  <input type="radio"  name="switch" class="btn-switch__radio btn-switch__radio_yes unitClean" value="yes" id="yuc"  />
  <input type="radio" checked  name="switch" class="btn-switch__radio btn-switch__radio_no unitClean" value="no" id="nuc"  />       
  <label for="yuc" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="nuc" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

                </div>
                </div>
                <div>
                <label>Time Taken</label>
                <input type="text" id="dishTime">
                </div>';
                echo self::signedBy($fid);
           echo  '</div>
           <script>
           $("#dishTemp").change(function(){
                            
                            if($("#dishTemp").val() === ""){
                            $("#DishwasherChecks").css("background-color","red");
                            $("#DishwasherChecks").css("color","white");
                        }else{
                            $("#DishwasherChecks").css("background-color","green");
                            $("#DishwasherChecks").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>
         </fieldset></form>';
    }

    public function signedBy($id)
    {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                    <label>Checks Completed by</label>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <input type="text" class="col-lg-8 col-xs-12" id="' . $id . '" placeholder="Ben ordish">
                    </div>';
    }

    public function CookedFood()
    {
        echo '<form><fieldset>
        <ul id="cooked">
        <li id="1" class="cooked">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Food Item</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" class="col-lg-8 col-xs-12" placeholder="Burgers" id="cooked-1">
                </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>cooked or reheated</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="radio">cooked</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="radio" class="col-lg-8 col-xs-12 cr-1" value="cooked" name="radio">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="radio">reheated</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="radio" class="col-lg-8 col-xs-12 cr-1" value="reheated" name="radio">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="radio">Hot Held</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="radio" class="col-lg-8 col-xs-12 cr-1" value="hot held" name="radio">
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" class="col-lg-8 col-xs-12 cr" placeholder="78c" id="CTemp-1">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>initials</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" class="col-lg-8 col-xs-12 cr" placeholder="BO" id="cinitials-1">
                </div>
                </div>
                
            </div>
            </li>
            </ul>
            <a href="javascript:void(0)" onclick="WICompliance.addCooked(`1`)">Add Cooked</a>
            </fieldset></form>';
    }

    public function editCookedFood()
    {
        echo '<form><fieldset>
        <ul id="cooked">
        <li id="1" class="cooked">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Food Item</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" class="col-lg-8 col-xs-12" placeholder="Burgers" id="cooked-1">
                </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>cooked or reheated</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="radio">cooked</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="radio" class="col-lg-8 col-xs-12 cr-1" value="cooked" name="radio">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="radio">reheated</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="radio" class="col-lg-8 col-xs-12 cr-1" value="reheated" name="radio">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="radio">Hot Held</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="radio" class="col-lg-8 col-xs-12 cr-1" value="hot held" name="radio">
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" class="col-lg-8 col-xs-12 cr" placeholder="78c" id="CTemp-1">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>initials</label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" class="col-lg-8 col-xs-12 cr" placeholder="BO" id="cinitials-1">
                </div>
                </div>
                
            </div>
            </li>
            </ul>
            <a href="javascript:void(0)" onclick="WICompliance.addCooked(`1`)">Add Cooked</a>
            </fieldset></form>';
    }


    public function HotHeld()
    {
        echo '<form><fieldset>
        <ul><li>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Food Item</label>
                <input type="text" class="col-lg-8 col-xs-12" placeholder="Burgers">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Time placed in unit</label>
                <input type="number" class="col-lg-8 col-xs-12" placeholder="12:00">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>+ 2 Hours</label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Time</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="14:00">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="78">C
                </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>+ 2 Hours</label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Time</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="16:00">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="70">C
                </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>+ 2 Hours</label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Time</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="18:00">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="60">C
                </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>discard Time</label>
                <input type="number" class="col-lg-8 col-xs-12" placeholder="18:00">
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>initials</label>
                <input type="text" class="col-lg-8 col-xs-12" placeholder="BO">
                </div>
            </div>
            </li>
            </ul>
            </fieldset></form>
            ';
    }

    public function editHotHeld($id)
    {
        echo '<form><fieldset>
        <ul><li>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Food Item</label>
                <input type="text" class="col-lg-8 col-xs-12" placeholder="Burgers">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Time placed in unit</label>
                <input type="number" class="col-lg-8 col-xs-12" placeholder="12:00">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>+ 2 Hours</label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Time</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="14:00">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="78">C
                </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>+ 2 Hours</label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Time</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="16:00">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="70">C
                </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>+ 2 Hours</label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Time</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="18:00">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label>Temp</label>
                <input type="numbers" class="col-lg-8 col-xs-12" placeholder="60">C
                </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>discard Time</label>
                <input type="number" class="col-lg-8 col-xs-12" placeholder="18:00">
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>initials</label>
                <input type="text" class="col-lg-8 col-xs-12" placeholder="BO">
                </div>
            </div>
            </li>
            </ul>
            </fieldset></form>
            ';
    }

    public function DryFoods()
    {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <input type="hidden" value="dryfoods" class="dfdelivery" name="type" id="dryfoods">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Supplier\'s Name</label>
            <input type="text" id="dfsupplier" class="deliveryInput dfdelivery" name="supplier">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery date</label>
            <input type="text" class="dfdelivery" id="dfdate" name="date" placeholder="' .date("l jS M Y") . '">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery Time</label>
            <input type="text" id="dftime" class="dfdelivery" name="time">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Packaging ok</label>
            <div class="btn-switch">                  
  <input type="radio"  name="switch-dry-pack" class="btn-switch__radio btn-switch__radio_yes dfpackaging dfcinput" value="yes" id="yespack"  />
  <input type="radio" checked  name="switch-dry-pack" class="btn-switch__radio btn-switch__radio_no dfpackaging dfcinput" value="no" id="nopack"  />       
  <label for="yespack" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="nopack" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Produce ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-dry-prod" class="btn-switch__radio btn-switch__radio_yes dfproduce dfcinput" value="yes" id="dfproduceyes"  />
  <input type="radio" checked  name="switch-dry-prod" class="btn-switch__radio btn-switch__radio_no dfproduce dfcinput" value="no" id="dfproduceno"  />       
  <label for="dfproduceyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="dfproduceno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>


            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Food Temp C</label>
            <input type="text" id="dftemp" class="dfdelivery" name="temp">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery rejected</label>

             <div class="btn-switch">                  
  <input type="radio"  name="switch-dry-re" class="btn-switch__radio btn-switch__radio_yes dfrejected dfcinput" value="yes" id="rejectedyes"  />
  <input type="radio" checked  name="switch-dry-re" class="btn-switch__radio btn-switch__radio_no dfrejected dfcinput" value="no" id="rejectedno"  />       
  <label for="rejectedyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="rejectedno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Corrective Actions or Comments</label>
            <textarea id="dfcorrective" class="dfdelivery" name="corrective"></textarea>
            </div>
        </div>
        <script>
            $(".dfproduce").click(function(){

                $(".dfproduce").addClass("checked");
                })

        $("#dftime").change(function(){
                            
                            if($("#dftime").val() === ""){
                            $("#DryFoods").css("background-color","red");
                            $("#DryFoods").css("color","white");
                        }else{
                            $("#DryFoods").css("background-color","green");
                            $("#DryFoods").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>';
    }

    public function editDryFoods($id)
    {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <input type="hidden" value="dryfoods" class="dfdelivery" name="type" id="dryfoods">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Supplier\'s Name</label>
            <input type="text" id="dfsupplier" class="deliveryInput dfdelivery" name="supplier">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery date</label>
            <input type="text" class="dfdelivery" id="dfdate" name="date" placeholder="' .date("l jS M Y") . '">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery Time</label>
            <input type="text" id="dftime" class="dfdelivery" name="time">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Packaging ok</label>
            <div class="btn-switch">                  
  <input type="radio"  name="switch-dry-pack" class="btn-switch__radio btn-switch__radio_yes dfpackaging dfcinput" value="yes" id="yespack"  />
  <input type="radio" checked  name="switch-dry-pack" class="btn-switch__radio btn-switch__radio_no dfpackaging dfcinput" value="no" id="nopack"  />       
  <label for="yespack" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="nopack" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Produce ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-dry-prod" class="btn-switch__radio btn-switch__radio_yes dfproduce dfcinput" value="yes" id="dfproduceyes"  />
  <input type="radio" checked  name="switch-dry-prod" class="btn-switch__radio btn-switch__radio_no dfproduce dfcinput" value="no" id="dfproduceno"  />       
  <label for="dfproduceyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="dfproduceno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>


            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Food Temp C</label>
            <input type="text" id="dftemp" class="dfdelivery" name="temp">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery rejected</label>

             <div class="btn-switch">                  
  <input type="radio"  name="switch-dry-re" class="btn-switch__radio btn-switch__radio_yes dfrejected dfcinput" value="yes" id="rejectedyes"  />
  <input type="radio" checked  name="switch-dry-re" class="btn-switch__radio btn-switch__radio_no dfrejected dfcinput" value="no" id="rejectedno"  />       
  <label for="rejectedyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="rejectedno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Corrective Actions or Comments</label>
            <textarea id="dfcorrective" class="dfdelivery" name="corrective"></textarea>
            </div>
        </div>
        <script>
            $(".dfproduce").click(function(){

                $(".dfproduce").addClass("checked");
                })

        $("#dftime").change(function(){
                            
                            if($("#dftime").val() === ""){
                            $("#DryFoods").css("background-color","red");
                            $("#DryFoods").css("color","white");
                        }else{
                            $("#DryFoods").css("background-color","green");
                            $("#DryFoods").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>';
    }

    public function FrozenFoods()
    {
         echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <input type="hidden" value="frozenfoods" class="ffdelivery" name="type" id="frozenfoods">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Supplier\'s Name</label>
            <input type="text" id="ffsupplier" class="deliveryInput ffdelivery" name="supplier">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery date</label>
            <input type="text" id="ffdate" name="date" class="ffdelivery" placeholder="' .date("l jS M Y") . '">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery Time</label>
            <input type="text" id="fftime" name="time" class="ffdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Packaging ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-frozen-pack" class="btn-switch__radio btn-switch__radio_yes ffpackaging ffcinput" value="yes" id="ffpackyes"  />
  <input type="radio" checked  name="switch-frozen-pack" class="btn-switch__radio btn-switch__radio_no ffpackaging ffcinput" value="no" id="ffpackno"  />       
  <label for="ffpackyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="ffpackno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>
</div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Produce ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-frozen-prod" class="btn-switch__radio btn-switch__radio_yes ffproduce ffcinput" value="yes" id="ffpprodyes"  />
  <input type="radio" checked  name="switch-frozen-prod" class="btn-switch__radio btn-switch__radio_no ffproduce ffcinput" value="no" id="ffprodno"  />       
  <label for="ffpprodyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="ffprodno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Food Temp C</label>
            <input type="text" id="fftemp" name="temp" class="ffdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery rejected</label>
            <div class="btn-switch">                  
  <input type="radio"  name="switch-frozen-re" class="btn-switch__radio btn-switch__radio_yes ffrejected ffcinput" value="yes" id="ffrejectyes"  />
  <input type="radio" checked  name="switch-frozen-re" class="btn-switch__radio btn-switch__radio_no ffrejected ffcinput" value="no" id="ffrejectno"  />       
  <label for="ffrejectyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="ffrejectno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Corrective Actions or Comments</label>
            <textarea id="ffcorrective" name="corrective" class="ffdelivery"></textarea>
            </div>

        </div>
        <script>
            $(".ffproduce").click(function(){
                
                $(".ffproduce").addClass("checked");
                })

                $("#fftime").change(function(){
                            
                            if($("#fftime").val() === ""){
                            $("#FrozenFoods").css("background-color","red");
                            $("#FrozenFoods").css("color","white");
                        }else{
                            $("#FrozenFoods").css("background-color","green");
                            $("#FrozenFoods").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>';
    }


    public function editFrozenFoods($id)
    {
         echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <input type="hidden" value="frozenfoods" class="ffdelivery" name="type" id="frozenfoods">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Supplier\'s Name</label>
            <input type="text" id="ffsupplier" class="deliveryInput ffdelivery" name="supplier">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery date</label>
            <input type="text" id="ffdate" name="date" class="ffdelivery" placeholder="' .date("l jS M Y") . '">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery Time</label>
            <input type="text" id="fftime" name="time" class="ffdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Packaging ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-frozen-pack" class="btn-switch__radio btn-switch__radio_yes ffpackaging ffcinput" value="yes" id="ffpackyes"  />
  <input type="radio" checked  name="switch-frozen-pack" class="btn-switch__radio btn-switch__radio_no ffpackaging ffcinput" value="no" id="ffpackno"  />       
  <label for="ffpackyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="ffpackno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>
</div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Produce ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-frozen-prod" class="btn-switch__radio btn-switch__radio_yes ffproduce ffcinput" value="yes" id="ffpprodyes"  />
  <input type="radio" checked  name="switch-frozen-prod" class="btn-switch__radio btn-switch__radio_no ffproduce ffcinput" value="no" id="ffprodno"  />       
  <label for="ffpprodyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="ffprodno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Food Temp C</label>
            <input type="text" id="fftemp" name="temp" class="ffdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery rejected</label>
            <div class="btn-switch">                  
  <input type="radio"  name="switch-frozen-re" class="btn-switch__radio btn-switch__radio_yes ffrejected ffcinput" value="yes" id="ffrejectyes"  />
  <input type="radio" checked  name="switch-frozen-re" class="btn-switch__radio btn-switch__radio_no ffrejected ffcinput" value="no" id="ffrejectno"  />       
  <label for="ffrejectyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="ffrejectno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Corrective Actions or Comments</label>
            <textarea id="ffcorrective" name="corrective" class="ffdelivery"></textarea>
            </div>

        </div>
        <script>
            $(".ffproduce").click(function(){
                
                $(".ffproduce").addClass("checked");
                })

                $("#fftime").change(function(){
                            
                            if($("#fftime").val() === ""){
                            $("#FrozenFoods").css("background-color","red");
                            $("#FrozenFoods").css("color","white");
                        }else{
                            $("#FrozenFoods").css("background-color","green");
                            $("#FrozenFoods").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>';
    }


    public function FreshFoods()
    {
         echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <input type="hidden" value="freshfoods" class="fdelivery" name="type" id="freshfoods">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Supplier\'s Name</label>
            <input type="text" id="fsupplier" class="deliveryInput fdelivery" name="supplier">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery date</label>
            <input type="text" id="fdate" name="date" class="fdelivery" placeholder="' .date("l jS M Y") . '">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery Time</label>
            <input type="text" id="ftime" name="time" class="fdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Packaging ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-f-pack" class="btn-switch__radio btn-switch__radio_yes packaging fcinput" value="yes" id="fpackyes"  />
  <input type="radio" checked  name="switch-f-pack" class="btn-switch__radio btn-switch__radio_no packaging fcinput" value="no" id="fpackno"  />       
  <label for="fpackyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="fpackno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Produce ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-f-prod" class="btn-switch__radio btn-switch__radio_yes fproduce fcinput" value="yes" id="fprodyes"  />
  <input type="radio" checked  name="switch-f-prod" class="btn-switch__radio btn-switch__radio_no fproduce fcinput" value="no" id="fprodno"  />       
  <label for="fprodyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="fprodno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Food Temp C</label>
            <input type="text" id="ftemp" name="temp" class="fdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery rejected</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-f-re" class="btn-switch__radio btn-switch__radio_yes frejected fcinput" value="yes" id="frejectyes"  />
  <input type="radio" checked  name="switch-f-re" class="btn-switch__radio btn-switch__radio_no frejected fcinput" value="no" id="frejectno"  />       
  <label for="frejectyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="frejectno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Corrective Actions or Comments</label>
            <textarea id="fcorrective" name="corrective" class="fdelivery"></textarea>
            </div>

        </div>
        <script>
            $(".fproduce").click(function(){
                
                $(".fproduce").addClass("checked");
                })
                $("#fdate").change(function(){
                            
                            if($("#fdate").val() === ""){
                            $("#FreshFoods").css("background-color","red");
                            $("#FreshFoods").css("color","white");
                        }else{
                            $("#FreshFoods").css("background-color","green");
                            $("#FreshFoods").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>';
    }

    public function editFreshFoods()
    {
         echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <input type="hidden" value="freshfoods" class="fdelivery" name="type" id="freshfoods">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Supplier\'s Name</label>
            <input type="text" id="fsupplier" class="deliveryInput fdelivery" name="supplier">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery date</label>
            <input type="text" id="fdate" name="date" class="fdelivery" placeholder="' .date("l jS M Y") . '">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery Time</label>
            <input type="text" id="ftime" name="time" class="fdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Packaging ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-f-pack" class="btn-switch__radio btn-switch__radio_yes packaging fcinput" value="yes" id="fpackyes"  />
  <input type="radio" checked  name="switch-f-pack" class="btn-switch__radio btn-switch__radio_no packaging fcinput" value="no" id="fpackno"  />       
  <label for="fpackyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="fpackno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Produce ok</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-f-prod" class="btn-switch__radio btn-switch__radio_yes fproduce fcinput" value="yes" id="fprodyes"  />
  <input type="radio" checked  name="switch-f-prod" class="btn-switch__radio btn-switch__radio_no fproduce fcinput" value="no" id="fprodno"  />       
  <label for="fprodyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="fprodno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Food Temp C</label>
            <input type="text" id="ftemp" name="temp" class="fdelivery">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">delivery rejected</label>

            <div class="btn-switch">                  
  <input type="radio"  name="switch-f-re" class="btn-switch__radio btn-switch__radio_yes frejected fcinput" value="yes" id="frejectyes"  />
  <input type="radio" checked  name="switch-f-re" class="btn-switch__radio btn-switch__radio_no frejected fcinput" value="no" id="frejectno"  />       
  <label for="frejectyes" class="btn-switch__label btn-switch__label_yes">
  <span class="btn-switch__txt">Yes</span></label>                               
      <label for="frejectno" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
</div>

            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <label class="labelling">Corrective Actions or Comments</label>
            <textarea id="fcorrective" name="corrective" class="fdelivery"></textarea>
            </div>

        </div>
        <script>
            $(".fproduce").click(function(){
                
                $(".fproduce").addClass("checked");
                })
                $("#fdate").change(function(){
                            
                            if($("#fdate").val() === ""){
                            $("#FreshFoods").css("background-color","red");
                            $("#FreshFoods").css("color","white");
                        }else{
                            $("#FreshFoods").css("background-color","green");
                            $("#FreshFoods").css("color","white");
                            }
                            
                            })
                        
                        
                    </script>';
    }

    public function Appliances()
    {
        $section = "appliance";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_appliances` WHERE `section`=:section', array(
            "section" => $section
        ));
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="text-align: center;">';
        if($results > 0){
            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deepClean " style="text-align: center;" id="appliance" data-id="' . $res['id'] . '">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:2%">
                <label class="labelling">' . $res['name'] . '</label>
                 <input type="hidden" value="' . $res['name'] . '" class="app" id="product-' . $res['id'] . '">
                <input type="text" id="cleaningdate-' . $res['id'] . '" placeholder="date"  class="app" name="date">
                <input type="text" id="cleaninginitials-' . $res['id'] . '" placeholder="initials"  class="app" name="initials">
                </div></div>
                <script>
  $( function() {
    $( "#cleaningdate-' . $res['id'] . '" ).datepicker();
    $("#Appliances").css("background-color","green");
  } );
  </script>';
            }
        }
        echo '</div>';

        
    }

        public function editAppliances($id)
    {
        $section = "appliance";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_appliances` WHERE `section`=:section', array(
            "section" => $section
        ));
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="text-align: center;">';
        if($results > 0){
            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deepClean " style="text-align: center;" id="appliance" data-id="' . $res['id'] . '">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:2%">
                <label class="labelling">' . $res['name'] . '</label>
                 <input type="hidden" value="' . $res['name'] . '" class="app" id="product-' . $res['id'] . '">
                <input type="text" id="cleaningdate-' . $res['id'] . '" placeholder="date"  class="app" name="date">
                <input type="text" id="cleaninginitials-' . $res['id'] . '" placeholder="initials"  class="app" name="initials">
                </div></div>
                <script>
  $( function() {
    $( "#cleaningdate-' . $res['id'] . '" ).datepicker();
    $("#Appliances").css("background-color","green");
  } );
  </script>';
            }
        }
        echo '</div>';
        
        
    }


    public function Section()
    {
        $section = "section";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_appliances` WHERE `section`=:section', array(
            "section" => $section
        ));
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">';
        if($results > 0){
            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deepClean" style="text-align: center;" id="section" data-id="' . $res['id'] . '">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>' . $res['name'] . '</label>
                 <input type="hidden" value="' . $res['name'] . '" class="app" id="product-' . $res['id'] . '">
                <input type="text" id="cleaningdate-' . $res['id'] . '" class="app" placeholder="date">
                <input type="text" id="cleaninginitials-' . $res['id'] . '" class="app" placeholder="initials">
                </div></div>
                <script>
  $( function() {
    $( "#cleaningdate-' . $res['id'] . '" ).datepicker();
    $("#FreezerTemps").css("background-color","green");
  } );
  </script>';
            }
        }
        echo '</div>';
    }

    public function editSection()
    {
        $section = "section";
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_appliances` WHERE `section`=:section', array(
            "section" => $section
        ));
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">';
        if($results > 0){
            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deepClean" style="text-align: center;" id="section" data-id="' . $res['id'] . '">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>' . $res['name'] . '</label>
                 <input type="hidden" value="' . $res['name'] . '" class="app" id="product-' . $res['id'] . '">
                <input type="text" id="cleaningdate-' . $res['id'] . '" class="app" placeholder="date">
                <input type="text" id="cleaninginitials-' . $res['id'] . '" class="app" placeholder="initials">
                </div></div>
                <script>
  $( function() {
    $( "#cleaningdate-' . $res['id'] . '" ).datepicker();
    $("#FreezerTemps").css("background-color","green");
  } );
  </script>';
            }
        }
        echo '</div>';
    }

    public function DailyClean()
    {
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_daily_cleaning`');
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">';
        if($results > 0){
            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dailyClean" style="text-align: center;" data-id="' . $res['id'] . '">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:2%">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <label class="labelling">' . $res['name'] . '</label>
                </div>
               c
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <input type="text" id="cleaningdate-' . $res['id'] . '" placeholder="date"  class="app" name="date" />
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <input type="text" id="cleaninginitials-' . $res['id'] . '" placeholder="initials"  class="app" name="initials" />
                </div>
                </div></div>
                <script>
  $( function() {
    $( "#cleaningdate-' . $res['id'] . '" ).datepicker();
    $("#DailyClean").css("background-color","green");
  } );
  </script>';
            }
        }
        echo '</div>';
    }

    public function editDailyClean()
    {
        $results = $this->WIdb->bindfree('SELECT * FROM `wi_daily_cleaning`');
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">';
        if($results > 0){
            foreach($results as $res){
                echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dailyClean" style="text-align: center;" data-id="' . $res['id'] . '">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:2%">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <label class="labelling">' . $res['name'] . '</label>
                </div>
               c
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <input type="text" id="cleaningdate-' . $res['id'] . '" placeholder="date"  class="app" name="date" />
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <input type="text" id="cleaninginitials-' . $res['id'] . '" placeholder="initials"  class="app" name="initials" />
                </div>
                </div></div>
                <script>
  $( function() {
    $( "#cleaningdate-' . $res['id'] . '" ).datepicker();
    $("#DailyClean").css("background-color","green");
  } );
  </script>';
            }
        }
        echo '</div>';
    }

    public function deliveryChecksSave($data)
    {
        //var_dump($data);
        $rand = rand();
        $site = $this->StaffSite(WISession::get('staff_id'));
        foreach($data as $res){
             $dry = $data[0]['dry'];
             $frozen = $data[0]['frozen'];
             $fresh = $data[0]['fresh'];
        
        if($dry > 0){
            //var_dump($dry);
            //var_dump($dry[0]['dry']['UserData']);
            $dry = $dry[0]['dry']['UserData'];
             $this->WIdb->insert('wi_compliance_deliveries', array(
            "supplier"     => $dry['dfsupplier'],
            "date"         => $dry['dfdate'],
            "time"         => $dry['dftime'],
            "temp"         => $dry['dftemp'],
            "packing_ok" => $dry['dfpackaging'],
            "produce_ok"   => $dry['dfproduce'],
            "rejected"     => $dry['dfrejected'],
            "type"         => $dry['type'],
            "corrective"   => $dry['dfcorrective'],
            "site"         => $site,
            "group_id"     => $rand    
        ));
        }

        if($frozen > 0){
            $frozen = $frozen[0]['frozen']['UserData'];
             $this->WIdb->insert('wi_compliance_deliveries', array(
            "supplier"     => $frozen['ffsupplier'],
            "date"         => $frozen['ffdate'],
            "time"         => $frozen['fftime'],
            "temp"         => $frozen['fftemp'],
            "packing_ok" => $frozen['ffpackaging'],
            "produce_ok"   => $frozen['ffproduce'],
            "rejected"     => $frozen['ffrejected'],
            "type"         => $frozen['type'],
            "corrective"   => $frozen['ffcorrective'],
            "site"         => $site,
            "group_id"     => $rand    
        ));
           

        }

        if($fresh > 0){
            $fresh = $fresh[0]['fresh']['UserData'];
             $this->WIdb->insert('wi_compliance_deliveries', array(
            "supplier"     => $fresh['fsupplier'],
            "date"         => $fresh['fdate'],
            "time"         => $fresh['ftime'],
            "temp"         => $fresh['ftemp'],
            "packing_ok" => $fresh['fpackaging'],
            "produce_ok"   => $fresh['fproduce'],
            "rejected"     => $fresh['frejected'],
            "type"         => $fresh['type'],
            "corrective"   => $fresh['fcorrective'],
            "site"         => $site,
            "group_id"     => $rand    
        ));
        
           

        }
    }

    $name = "Delivery Checks";
        $this->WIdb->insert('wi_compliance_dashboard', array(
            "name" => $name,
            "date" => date("Y-m-d"),
            "time" =>  date('h:i:s'),
            "site" => $site,
            "group_id" => $rand
        ));

        $this->WIdb->update(
                    'wi_compliance_checklist',
                     array(
                         "date" => date("Y-m-d")
                     ),
                     "`name` = :name AND `site`=:site",
                    array( "name" => $name,"site" => $site )
                );

        echo "Saved, thank you.";

        
    }

    public function deepCleaningSave($data)
    {
        $rand = rand();
        $site = $this->StaffSite(WISession::get('staff_id'));
        foreach($data as $res){
        //var_dump($res['clean']['UserData']);
        $this->WIdb->insert('wi_compliance_deep_clean', array(
            "name" => $res['clean']['UserData']['product'],
            "date" => $res['clean']['UserData']['date'],
            "initials" =>  $res['clean']['UserData']['initials'],
            "group_id" => $rand
        ));
        }



        $name = "Deep Cleaning";
        $this->WIdb->insert('wi_compliance_dashboard', array(
            "name" => $name,
            "date" => date("Y-m-d"),
            "time" =>  date('h:i:s'),
            "site" => $site,
            "group_id" => $rand
        ));

        $this->WIdb->insert('wi_compliance_deep_clean_checklist', array(
            "name" => $name,
            "group_id" => $rand,
            "site" => $site
        ));

        $this->WIdb->update(
                    'wi_compliance_checklist',
                     array(
                         "date" => date("Y-m-d")
                     ),
                     "`name` = :name AND `site`=:site",
                    array( "name" => $name,"site" => $site )
                );

        echo "Saved, thank you.";


        
    }

    public function dailyCleaningSave($data)
    {
        $rand = rand();
        $site = $this->StaffSite(WISession::get('staff_id'));
       
        foreach($data as $res){
        //var_dump($res['clean']['UserData']);
        $this->WIdb->insert('wi_compliance_daily_clean', array(
            "name" => $res['clean']['UserData']['product'],
            "date" => $res['clean']['UserData']['date'],
            "initials" =>  $res['clean']['UserData']['initials'],
            "group_id" => $rand
        ));
        }



        $name = "Daily Cleaning";
        $this->WIdb->insert('wi_compliance_dashboard', array(
            "name" => $name,
            "date" => date("Y-m-d"),
            "time" =>  date('h:i:s'),
            "site" => $site,
            "group_id" => $rand
        ));

        $this->WIdb->insert('wi_compliance_daily_clean_checklist', array(
            "name" => $name,
            "group_id" => $rand,
            "site" => $site
        ));

       $this->WIdb->update(
                    'wi_compliance_checklist',
                     array(
                         "date" => date("Y-m-d")
                     ),
                     "`name` = :name AND `site`=:site",
                    array( "name" => $name,"site" => $site )
                );

        echo "Saved, thank you.";


        
    }

    public function checkId($name)
    {
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_checklist` WHERE `name`=:name', array("name" => $name));
        if($results > 0){
            return $results[0]['id'];
        }
    }

    public function morningChecksSave($data)
    {
        //var_dump($data);
        $rand = rand();
        $name = "Morning Checks";
        $site = $this->StaffSite(WISession::get('user_id'));
        $check = $this->checkId($name);


        foreach($data as $res){
            //var_dump($res);

            $dutyChef = $res['dutyChef'];
            $position = $res['position'];

            $fridges   = $res['fridgedata'];
            $freezers  = $res['freezerdata'];
            $questions = $res['questiondata'];

             foreach($fridges as $fridge){
                $this->WIdb->insert('wi_fridge_temps', array(
            "fridge"    => $fridge['id'],
            "temp"      => $fridge['fridge'],
            "group_id"  => $rand    
        ));
            }

            foreach($freezers as $freezer){
                $this->WIdb->insert('wi_freezer_temps', array(
            "freezer"   => $freezer['id'],
            "temp"      => $freezer['freezer'],
            "group_id"  => $rand    
        ));
            }

            foreach($questions as $quest){
                if($quest['answer'] === "no"){
                    $this->WIdb->insert('wi_opening_checks', array(
            "question_id" => $quest['question'],
            "answer"      => $quest['answer'],
            "problem"     => $quest['problem'],
            "action"      => $quest['action'],
            "signed"      => $quest['signed'],
            "date"        => $quest['date'],
            "group_id"    => $rand    
        ));
                }else{
                   $this->WIdb->insert('wi_opening_checks', array(
            "question_id" => $quest['question'],
            "answer"      => $quest['answer'],
            "group_id"    => $rand    
        )); 
                }

            }

            $this->WIdb->insert('wi_dishwasher_checks', array(
            "temp"          => $quest['dishTemp'],
            "unit_clean"    => $quest['unitClean'],
            "completed_by"  => $quest['signedDishes'],
            "group_id"      => $rand    
        ));

             $this->WIdb->insert('wi_morning_checks', array(
            "chefs_name"        => $res['dutyChef'],
            "chefs_position"    => $res['position'],
            "opening_checks_id" => $rand,
            "oodf_found"        => $res['fridgeCheck'],
            "completed_by"      => $res['dutyChef'],
            "fridge_checks_id"  => $rand,
            "freezer_checks_id" => $rand,
            "batch_cooking_id"  => $rand,
            "dishwasher_checks_id"  => $rand,
            "date"              => date("Y-m-d"),
            "time"              =>  date('h:i:s'),
            "completed"         => "yes",
            "group_id"          => $rand,
            "signedFridges"     => $res['signedFridges'],
            "signedFreezera"    => $res['signedFreezera'],
            "signedFChecks"     => $res['signedFChecks'],
            "signedOpening"     => $res['signedOpening'],
            "signedDishes"      => $res['signedDishes'],
            "site"              => $site
           ));


         if($res['batch'] > 0){
                //var_dump($res['batch']);
                $count = count($res['batch']);
                //echo $count;
                $user = $res['batch'];
                //var_dump($user);

                foreach($user as $res){
               //var_dump($u['batch']['UserData']['product']);

               $this->WIdb->insert('wi_batch_cooking', array(
                  "product"          => $res['batch']['UserData']['product'],
                  "end_cook_timee"           => $res['batch']['UserData']['eofct'],
                  "end_cook_temp"            => $res['batch']['UserData']['eoct'],
                    "cook_method"          => $res['batch']['UserData']['method'],
                    "end_cool_time"         => $res['batch']['UserData']['eoctime'],
                    "end_cool_temp"         => $res['batch']['UserData']['eoctemp'],
                    "total_cooling_time"      => $res['batch']['UserData']['totalCool'],
                   "time_in_fridge"  => $res['batch']['UserData']['time_in_fridge'],
                    "initials"        => $res['batch']['UserData']['initials'],
                    "group_id"        => $rand   
        )); 

                }
                
            }

        }

        $this->WIdb->insert('wi_compliance_dashboard', array(
            "name" => $name,
            "date" => date("Y-m-d"),
            "time" =>  date('h:i:s'),
            "site" => $site,
            "group_id" => $rand
        ));

$this->WIdb->update(
                    "wi_compliance_overview",
                     array(
                         "status" => 'completed' 
                     ),
                     "`checklist_id` = :check AND `site_id`=:site",
                    array( "check" => $check,"site" => $site )
                );
            
        $results = $this->WIdb->select("SELECT * FROM `wi_morning_checks` WHERE `group_id`=:group", array(
            "group" => $rand
        ));
        if($results > 0){
            echo "Saved, thank you.";
        }else{
            echo "Unable to save, please try again later, sorry.";
        }
        
    }


    public function eveningChecksSave($data)
    {
        //var_dump($data);
        $rand = rand();
        $site = $this->StaffSite(WISession::get('staff_id'));
        foreach($data as $res){
            //var_dump($res);

            $fridges   = $res['fridgedata'];
            $freezers  = $res['freezerdata'];
            $questions = $res['questiondata'];
            $cooked    = $res['cooked'];
             //var_dump($cooked);
             foreach($fridges as $fridge){
                $this->WIdb->insert('wi_fridge_temps', array(
                "fridge"    => $fridge['id'],
                "temp"      => $fridge['fridge'],
                "group_id"  => $rand    
                    ));
            }

            foreach($freezers as $freezer){
                $this->WIdb->insert('wi_freezer_temps', array(
                "freezer"   => $freezer['id'],
                "temp"      => $freezer['freezer'],
                "group_id"  => $rand    
                 ));
            }

            foreach($questions as $quest){
                if($quest['answer'] === "no"){
                    $this->WIdb->insert('wi_closing_checks', array(
                    "question_id" => $quest['question'],
                    "answer"      => $quest['answer'],
                    "problem"     => $quest['problem'],
                    "action"      => $quest['action'],
                    "signed"      => $quest['signed'],
                    "date"        => $quest['date'],
                    "group_id"    => $rand    
                      ));
                }else{
                   $this->WIdb->insert('wi_closing_checks', array(
                    "question_id" => $quest['question'],
                    "answer"      => $quest['answer'],
                    "group_id"    => $rand    
                      )); 
                }

            }

            $this->WIdb->insert('wi_dishwasher_checks', array(
            "temp"          => $quest['dishTemp'],
            "unit_clean"    => $quest['unitClean'],
            "completed_by"  => $quest['signedDishes'],
            "group_id"      => $rand    
             ));

             $this->WIdb->insert('wi_evening_checks', array(
            "closing_checks_id"     => $rand,
            "oodf_found"            => $res['fridgeCheck'],
            "fridge_checks_id"      => $rand,
            "freezer_checks_id"     => $rand,
            "batch_cooking_id"      => $rand,
            "dishwasher_checks_id"  => $rand,
            "cooked_foods_id"       => $rand,
            "hot_held_id"           => $rand,
            "date"                  => date("Y-m-d"),
            "time"                  =>  date('h:i:s'),
            "completed"             => "yes",
            "group_id"              => $rand,
            "signedFridges"         => $res['signedFridges'],
            "signedFreezera"        => $res['signedFreezera'],
            "signedFChecks"         => $res['signedFChecks'],
            "signedClosing"         => $res['signedClosing'],
            "signedDishes"          => $res['signedDishes'],
            "site"                  => $site
             ));


         if($res['batch'] > 0){
                //var_dump($res['batch']);
                $count = count($res['batch']);
                //echo $count;
                $user = $res['batch'];
                //var_dump($user);

                foreach($user as $res){
               //var_dump($u['batch']['UserData']['product']);

               $this->WIdb->insert('wi_batch_cooking', array(
                "product"             => $res['batch']['UserData']['product'],
                "end_cook_timee"      => $res['batch']['UserData']['eofct'],
                "end_cook_temp"       => $res['batch']['UserData']['eoct'],
                "cook_method"         => $res['batch']['UserData']['method'],
                "end_cool_time"       => $res['batch']['UserData']['eoctime'],
                "end_cool_temp"       => $res['batch']['UserData']['eoctemp'],
                "total_cooling_time"  => $res['batch']['UserData']['totalCool'],
                "time_in_fridge"      => $res['batch']['UserData']['time_in_fridge'],
                "initials"            => $res['batch']['UserData']['initials'],
                "group_id"            => $rand   
                 )); 

                }
                
            }
            //var_dump($cooked);
        if($cooked > 0){
                //var_dump($cooked);
                $count = count($cooked);
                //echo $count;
                $user = $cooked;
                //var_dump($user);

                foreach($user as $res){
              // var_dump($res['cooked']['UserData']['product']);

               $this->WIdb->insert('wi_cooked_foods', array(
                  "product"   => $res['cooked']['UserData']['product'],
                  "temp"      => $res['cooked']['UserData']['temp'],
                  "method"    => $res['cooked']['UserData']['method'],
                  "initials"  => $res['cooked']['UserData']['initials'],
                  "group_id"  => $rand   
                  )); 

                }
                
            }

        }

        $name = "Evening Checks";
        $this->WIdb->insert('wi_compliance_dashboard', array(
            "name" => $name,
            "date" => date("Y-m-d"),
            "time" =>  date('h:i:s'),
            "site" => $site,
            "group_id" => $rand
        ));

        $this->WIdb->update(
                    'wi_compliance_checklist',
                     array(
                         "date" => date("Y-m-d")
                     ),
                     "`name` = :name AND `site`=:site",
                    array( "name" => $name,"site" => $site )
                );
            
        $results = $this->WIdb->select("SELECT * FROM `wi_evening_checks` WHERE `group_id`=:group", array(
            "group" => $rand
        ));
        if($results > 0){
            echo "Saved, thank you.";
        }else{
            echo "Unable to save, please try again later, sorry.";
        }
        
    }


}