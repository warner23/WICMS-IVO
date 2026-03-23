<?php
#[\AllowDynamicProperties]

class WICalendar 
{  
  private $WIdb;

  function __construct()
  {
    $this->WIdb =  WIdb::getInstance();
    $this->login = new  WILogin();
    $this->Info = new WIUserInfo();
    $this->user   = new WIUser(WISession::get('user_id'));
    $this->Maint = new WIMaintenace();
    $this->mailer = new WIEmail();
    //$WIdb = WIdb::getInstance();
  }


  public function getCalendar($year = '', $month = '')
  {
  $dateYear   = ($year != '')?$year:date("Y");
  $dateMonth  = ($month != '')?$month:date("m");
  $date       = $dateYear.'-'.$dateMonth.'-01';
  
  $currentMonthFirstDay = date("N",strtotime($date));
  $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear);
  $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7)?($totalDaysOfMonth):($totalDaysOfMonth + $currentMonthFirstDay);
  $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42;

  echo '<style>
  select {
    width: 32% !important;
    border: 1px solid #cccccc;
    background-color: #ffffff;
}
.app{
font-size: 5px;
    width: 100%;
    height: 24px;
}
  </style>';
  echo '<div id="calender_section">
  <div class="">
    <span class="day btn" id="day" onclick="WIAppointment.getDayCalendar()">Day</span>
    <span class="week btn" id="week" onclick="WIAppointment.getWeekCalendar()">Week</span>
    <span class="month btn active" id="month" onclick="WIAppointment.getCalendar(`calendar`,`'.date("Y",strtotime($date)).'`,`'.date("m",strtotime($date)).'`)">Month</span>
  </div>
  <h2>
          <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' - 1 Month')).'`,`'.date("m",strtotime($date.' - 1 Month')).'`);">&lt;&lt;</a>
            <select name="month_dropdown" class="month_dropdown dropdown">'.WICalendar::getAllMonths($dateMonth).'
            </select>
      <select name="year_dropdown" class="year_dropdown dropdown">'.WICalendar::getYearList($dateYear).'</select>
            <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' + 1 Month')).'`,`'. date("m",strtotime($date.' + 1 Month')).'`);">&gt;&gt;</a>
        </h2>';



echo '<div class="calendar_wrap" style="height: 446px; border: solid;">
    <div id="calender_section_top">
      <ul style="width: 100%;margin: 0 0 10px 14px;">
        <li>Sunday</li>
        <li>Monday</li>
        <li>Tuesday</li>
        <li>Wednesday</li>
        <li>Thursday</li>
        <li>Friday</li>
        <li>Saturday</li>
      </ul>
    </div>
    <!-- // payment api code for PayPal
                // DO NOT  ALTER !!!-->

    <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Ensures optimal rendering on mobile devices. -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
  <!-- Optimal Internet Explorer compatibility -->

  <script
    src="https://www.paypal.com/sdk/js?client-id=' .PAYPAL_CLIENT_ID . '&currency=' . CURRENCY . '">
  </script>

    <div id="calender_section_bot">
      <ul>';
        $dayCount = "1";
        $today = date("D M j G:i:s T Y"); 
        //echo $dayCount;
        for($cb=1;$cb<=$boxDisplay;$cb++){
          if(($cb >= $currentMonthFirstDay+1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)){
            //Current date
            $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount;
            $day = date('l', strtotime($currentDate ));
            $eventNum = 0;
            //Get number of events based on the current date
            
        
           $status = "0";
            $result = $this->WIdb->select(
                    "SELECT * FROM `wi_class_schedule` WHERE day =:d AND status = :s",
                     array(
                       "d" => $day,
                       "s" => $status
                     )
                  );
            //var_dump($result);
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($currentDate) == strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'"  day="'.$day.'" class="appointment_create grey date_cell">';

              foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$currentDate.'\');">'. $res['name'].'</a></div>';
              }
              
            }elseif($eventNum > 0){
              echo '<li date="'.$currentDate.'" day="'.$day.'"class="appointment_create light_sky date_cell">';


              foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$currentDate.'\');">'. $res['name'].'</a></div>';
              }

            }elseif(strtotime($currentDate) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" day="'.$day.'"class="appointment_create light_sky date_cell">';

              foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$currentDate.'\');">'. $res['name'].'</a></div>';
              }
            }else{
              echo '<li date="'.$currentDate.'" day="'.$day.'"class="appointment_create date_cell">';
            }

          
            //Date cell
            echo '<span>';
            echo $dayCount;
            echo '</span>';
          
            echo '</li>';
            $dayCount++;
      }else{
        echo '<li class="appointment_create"><span>&nbsp;</span></li>';
       } 
     } // end of for loop 


      echo '</ul>
    </div></div>
  </div>';


}



  public function getWeekCalendar($year = '', $month = '')
  {
  $dateYear = ($year != '')?$year:date("Y");
  $dateMonth = ($month != '')?$month:date("m");
  $date = $dateYear.'-'.$dateMonth.'-01';
  $currentMonthFirstDay = date("N",strtotime($date));
  $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear);
  $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7)?($totalDaysOfMonth):($totalDaysOfMonth + $currentMonthFirstDay);
  $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42;
  $hourlyWeeklyTotal = 98;

  echo '<style>
  select {
    width: 32% !important;
    border: 1px solid #cccccc;
    background-color: #ffffff;
}
.app{
font-size: 5px;
    width: 100%;
    height: 24px;
}
  </style>';

  echo '<div id="calender_section">

  <div class="">
    <span class="day btn" id="day" onclick="WIAppointment.getDayCalendar()">Day</span>
    <span class="week btn" id="week" onclick="WIAppointment.getWeekCalendar()">Week</span>
    <span class="month btn active" id="month" onclick="WIAppointment.getCalendar(`calendar`,`'.date("Y",strtotime($date)).'`,`'.date("m",strtotime($date)).'`)">Month</span>
  </div>

  <h2>
          <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' - 1 Month')).'`,`'.date("m",strtotime($date.' - 1 Month')).'`);">&lt;&lt;</a>
            <select name="month_dropdown" class="month_dropdown dropdown">'.WICalendar::getAllMonths($dateMonth).'
            </select>
      <select name="year_dropdown" class="year_dropdown dropdown">'.WICalendar::getYearList($dateYear).'</select>
            <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' + 1 Month')).'`,`'. date("m",strtotime($date.' + 1 Month')).'`);">&gt;&gt;</a>
        </h2>
    <div id="event_list" class=""></div>
    <div class="calendar_wrap">
    <div id="calender_weekly_section_top">
      <ul style="width: 100%;">';
      $date = new DateTime();
      echo '<li class="ulweekly">' .$date->modify( '+0 days' )->format( 'l jS' ) . '</li>';
      
  for( $days = 6; $days--; ) {
  echo '<li class="ulweekly">' .$date->modify( '+1 days' )->format( 'l jS' ) . '</li>';
}
      echo '</ul>
    </div>
    <div id="calendar_weekly_section">
    <ul class="cal_week_ul">';
    $weekDisplay = "19";
for( $hours = 6; $hours<=$weekDisplay; $hours++ ) {
         echo '<li class="ulliweek">' .$hours.'</li>';
      }
    echo '</ul>
    </div>

    <div id="calender_weekly_section_bot">
      <ul>';
        $dayCount = 1; 
        $hourCount = 24;

        
        for($cb=1;$cb<=$hourlyWeeklyTotal;$cb++){
          if(($cb >= $currentMonthFirstDay+1 || $currentMonthFirstDay == 7) && $cb <= ($hourlyWeeklyTotal)){
            //Current date
            $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount;
            $day = date('l', strtotime($currentDate ));
            $eventNum = 0;
            //Get number of events based on the current date
            
        
           $status = "0";
            $result = $this->WIdb->select(
                    "SELECT * FROM `wi_class_schedule` WHERE day =:d AND status = :s",
                     array(
                       "d" => $day,
                       "s" => $status
                     )
                  );
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($currentDate) == strtotime(date("Y-m-d"))){
              echo '<li date="" class="grey date_cell">';
            }elseif($eventNum > 0){
              echo '<li date="'.$currentDate.'" class="light_sky date_cell">';
              foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$currentDate.'\');">'. $res['name'].'</a></div>';
              }

            }elseif(strtotime($currentDate) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell">';

               foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$currentDate.'\');">'. $res['name'].'</a></div>';
              }
            }else{
              echo '<li date="" class="date_cell">';
            }
            //Date cell
            echo '<span>';
            echo '';
            echo '</span>';
            
            //Hover event popup
            echo '<div id="date_popup_'.$currentDate.'" class="date_popup_wrap none">';
            echo '<div class="date_window">';
            echo '<div class="popup_event">Events ('.$eventNum.')</div>';
            echo ($eventNum > 0)?'<a href="javascript:;" onclick="WICalendar.getEvents(\''.$currentDate.'\');">view events</a>':'';
            echo '</div></div>';
            

            echo '</li>';
            $dayCount++;
      }else{
        echo '<li><span>&nbsp;</span></li>';
       } 
     } 
      echo '</ul>
    </div></div>
  </div>';


}

  public function getDayCalendar($year = '', $month = '')
  {
      $dateYear = ($year != '')?$year:date("Y");
  $dateMonth = ($month != '')?$month:date("m");
  $date = $dateYear.'-'.$dateMonth.'-01';
  $currentMonthFirstDay = date("N",strtotime($date));
  $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear);
  $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7)?($totalDaysOfMonth):($totalDaysOfMonth + $currentMonthFirstDay);
  $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42;
  $hourlyWeeklyTotal = 19;

  echo '<style>
  select {
    width: 32% !important;
    border: 1px solid #cccccc;
    background-color: #ffffff;
}
.app{
font-size: 5px;
    width: 100%;
    height: 24px;
}
  </style>';
  echo '<div id="calender_section">

  <div class="">
    <span class="day btn" id="day" onclick="WIAppointment.getDayCalendar()">Day</span>
    <span class="week btn" id="week" onclick="WIAppointment.getWeekCalendar()">Week</span>
    <span class="month btn active" id="month" onclick="WIAppointment.getCalendar(`calendar`,`'.date("Y",strtotime($date)).'`,`'.date("m",strtotime($date)).'`)">Month</span>
  </div>

  <h2>
          <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' - 1 Month')).'`,`'.date("m",strtotime($date.' - 1 Month')).'`);">&lt;&lt;</a>
            <select name="month_dropdown" class="month_dropdown dropdown">'.WICalendar::getAllMonths($dateMonth).'
            </select>
      <select name="year_dropdown" class="year_dropdown dropdown">'.WICalendar::getYearList($dateYear).'</select>
            <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' + 1 Month')).'`,`'. date("m",strtotime($date.' + 1 Month')).'`);">&gt;&gt;</a>
        </h2>
    <div id="event_list" class=""></div>
    <div class="calendar_wrap">
    <div id="calender_daily_section_top">
      <ul style="width: 100%;">';
      $date = new DateTime();
echo '<li class="ulliweekly">' .$date->modify( '+1 days' )->format( 'l jS' ) . '</li>';

      echo '</ul>
    </div>
    <div id="calendar_daily_section">
    <ul class="cal_day_ul">';
    $weekDisplay = "19";
for( $hours = 10; $hours<=$weekDisplay; $hours++ ) {
         echo '<li class="ulliweek">' .$hours.'</li>';
      }
    echo '</ul>
    </div>

    <div id="calender_daily_section_bot">
      <ul>';
        $dayCount = 1; 
        $hourCount = 24;

        
        for($cb=1;$cb<=$hourlyWeeklyTotal;$cb++){
          if(($cb >= $currentMonthFirstDay+1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)){
            //Current date
            $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount;
            $eventNum = 0;
            $day = date('l', strtotime($date));
            //Get number of events based on the current date
            
        
           $status = "0";
            $result = $this->WIdb->select(
                    "SELECT * FROM `wi_class_schedule` WHERE day =:d AND status = :s",
                     array(
                       "d" => $day,
                       "s" => $status
                     )
                  );
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($date) == strtotime(date("Y-m-d"))){
              echo '<li date="" class="grey date_cell">';
            }elseif($eventNum > 0){
              echo '<li date="'.$date.'" class="light_sky date_cell">';

              foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$date.'\', \''.$res['name'].'\');">'. $res['name'].'</a></div>';
              }
            }elseif(strtotime($day) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$date.'" class="appointment_create light_sky date_cell">';

            foreach($result as $res){
                echo '<div class="app"><a href="javascript:void(0);" onclick="WIAppointment.training(\''.$date.'\', \''.$res['name'].'\');">'. $res['name'].'</a></div>';
              }

            }else{
              echo '<li date="" class="date_cell">';
            }
            //Date cell
            echo '<span>';
            echo $dayCount;
            echo '</span>';
            
            //Hover event popup
            echo '<div id="date_popup_'.$date.'" class="date_popup_wrap none">';
            echo '<div class="date_window">';
            echo '<div class="popup_event">Events ('.$eventNum.')</div>';
            echo ($eventNum > 0)?'<a href="javascript:;" onclick="WICalendar.getEvents(\''.$date.'\');">view events</a>':'';
            echo '</div></div>';
            

            echo '</li>';
            $dayCount++;
      }else{
        echo '<li><span>&nbsp;</span></li>';
       } 
     } 
      echo '</ul>
    </div></div>
  </div>';

}





/*
 * Get months options list.
 */
function getAllMonths($selected = ''){
  $options = '';
  for($i=1;$i<=12;$i++)
  {
    $value = ($i < 10)?'0'.$i:$i;
    $selectedOpt = ($value == $selected)?'selected':'';
    $options .= '<option value="'.$value.'" '.$selectedOpt.' >'.date("F", mktime(0, 0, 0, $i+1, 0, 0)).'</option>';
  }
  return $options;
}

/*
 * Get years options list.
 */
function getYearList($selected = ''){
  $options = '';
  for($i=2015;$i<=2025;$i++)
  {
    $selectedOpt = ($i == $selected)?'selected':'';
    $options .= '<option value="'.$i.'" '.$selectedOpt.' >'.$i.'</option>';
  }
  return $options;
}

/*
 * Get events by date
 */
 public function getEvents($date = '')
     {

        $eventListHTML = '';
        $date = $date?$date:date("Y-m-d");
       // echo "date". $date;
        //Get events based on the current date
       // $result = $WIdb->query("SELECT title FROM events WHERE date = '".$date."' AND status = 1");

         $status = 1;
                  $result = $this->WIdb->select(
                          "SELECT `title` FROM `wi_events` WHERE `date` =:d AND `status` = :s ",
                           array(
                             "d" => $date,
                             "s" => $status
                           )
                        );

        if(count($result) > 0){
        // var_dump($result);

        echo '<h2>Events on '.date("l, d M Y",strtotime($date)).'</h2>';
        echo '<ul>';
      // echo "title" . $result[0]["title"];

       foreach ($result as $res) {
         // echo "title2" . $res['title'];
                  echo '<li>'. $res["title"] .'</li>';
       }

          echo '</ul>';
        }
    }


    public function getEventTypes()
     {

        $eventListHTML = '';
        $result = $this->WIdb->select("SELECT `name` FROM `wi_appointment_types`");

        if(count($result) > 0){
        // var_dump($result);

        echo '<h2>Appointment Types</h2>';
        echo '<ul>';
      // echo "title" . $result[0]["title"];

       foreach ($result as $res) {
         // echo "title2" . $res['title'];
                  echo '<li id="eventDrag" class="drag ui-draggable-handle ui-draggable">'. $res["name"] .'</li>';
       }

          echo '</ul>';
        }
    }

    public function addEventBtn($appointment)
    {
      $user = $appointment['UserData'];
      $status = "active";

      self::paynow($user['timing'], $user['duration']);
       $this->WIdb->insert('wi_events', array(
            "title"     => strip_tags($user['title']),
            "status"  => $status,
            "date"  => $user['dating'],
            "timing"  => $user['timing'],
            "name" => $user['name'],
            "contact_no" => $user['contact_no']
        )); 
    }



  public function showPaymentExecute($res, $ord)
    {
        $status = $res['status'];
        $user_id = $this->user->id();

        if($user_id == ""){
          $user_id = rand(10, 30);
        }

        // get order for displaying
        $result = $this->WIdb->select(
                    "SELECT * FROM `wi_order`
                     WHERE `id` = :id",
                     array(
                       "id" => $ord
                     )
                  );

        //create notification
        $st1 = $user_id;
        $st2 = 'You have been book for a ' . $result[0]['item_title'] .' on ' . $result[0]['date'];
        $this->Maint->Notifications($st1, $st2);
        // create transaction receipt

        $receipt_id = $res['id'];
        $payerInfo = $res['payer'];
        $ref_id    = $res['purchase_units'][0]['reference_id'];

        $this->WIdb->insert('wi_transaction',
        array(
        "userId" => $user_id,
        "orderId" => $receipt_id,
        "code"    => $ref_id,
        "status"  => $status
        )
        );

        $trans_id = $this->WIdb->lastInsertId();

        //create log
        $st3 = $user_id;
        $st4 = "session booked and paid for " . $receipt_id;
        $this->Maint->LogFunction($st3, $st4);

        //receipt
        $receipt = '<div class="row-fluid">
    <!-- Middle Section -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div id="loadingAlert"
             class="card"
             style="display: none;">
            <div class="card-body">
                <div class="alert alert-info block"
                     role="alert">
                    Loading....
                </div>
            </div>
        </div>
        <form id="orderConfirm"
              class="form-horizontal"
              style="display: none;">
            <h3>Your payment is authorized.</h3>
            <h4>Confirm the order to execute</h4>
            <hr>
            <div class="form-group">
                
                <div class="col-sm-7">
                    <p id="confirmRecipient"></p>

                </div>
            </div>
            <div class="form-group">
               
            </div>
            <hr>
            <div class="form-group">
                <div class="col-sm-offset-5 col-sm-7">
                    <label class="btn btn-primary" id="confirmButton">Complete Payment</label>
                </div>
            </div>
        </form>
        <form id="orderView"
              class="form-horizontal"
              style="display: block;">
            <h3>Your payment is complete</h3>
            <h4>
                <span id="viewFirstName">' .$result[0]['name'] . '</span>
                Thank you for your Order
            </h4>
            <hr>
            <div class="form-group">
                <div class="form-group">
                  
                    <div class="col-sm-7">
                        <p id="viewRecipientName"></p>
                    </div>
                </div>
                <div class="form-group">
                    
                    <div class="col-sm-7">';
                    
        if($res['purchase_units'][0]['payments'] && $res['purchase_units'][0]['payments']['captures']) {

            $final_amount = $res['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $view_currentcy = $res['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
        }else {
            $final_amount = $res['purchase_units'][0]['amount']['value'];
            $view_currentcy = $res['purchase_units'][0]['amount']['currency_code'];
        }
                        $receipt .='<p>Transaction ID: <span id="viewTransactionID">' . $receipt_id . '</span></p>
                        <p>Payment Total Amount: <span id="viewFinalAmount"> 
                        ' . $final_amount . '
                        </span> </p>
                        <p>Currency Code: <span id="viewCurrency">
                        ' . $view_currentcy . '
                        </span></p>
                        <p>Payment Status: <span id="viewPaymentState">
                        result.status
                        ' . $res['status'] . '
                        </span></p>
                        <div>
                        <div>You have book for a '.$result[0]['item_title'].'</div>
                        <div>at '.$result[0]['place'].'</div>
                        <div>on '.$result[0]['date'].'</div>
                        <div> Starting at '. $result[0]['startTime'].'</div>
                        <div><p>Thank you for your business</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="emailling">
            <hr>
            <h3> Click <a href="javascript:void(0)" onclick="WIAppointment.email(`$res.receipt`)">here </a> to email a copy of your receipt or </h3>
             <h3> Click <a href="javascript:void(0)" onclick="WIAppointment.return()">here </a> to close </h3>
            </div>
        </form>
    </div>
</div>
';

          // create db event
        $dating = $result[0]['date'];
        $timing = $result[0]['startTime'];

        $this->WIdb->insert('wi_events',
        array(
        "title"       => $result[0]['item_title'],
        "status"      => $res['status'],
        "date"        => $dating,
        "startTime"   => $timing,
        "contents"    => $receipt,
        "user_id"     => $user_id,
        "name"        => $result[0]['name'],
        "contact_no"  => $result[0]['contact_no'],
        "duration"    => $result[0]['duration'],
        "place"       => $result[0]['place'],
        "trans_id"    => $receipt_id,
        "type"        => $result[0]['type'],
        "date_booked" => $result[0]['date_booked']

        )
        );

        $event_id = $this->WIdb->lastInsertId();

       // delete order
        $this->WIdb->delete("wi_order", "id = :id", array( "id" => $ord ));
    
    $results = array(
    "status"   => "COMPLETED",
    "receipt"  => $receipt,
    "trans_id" => $trans_id,
    "event_id" => $event_id
    );
    echo json_encode($results); 
       
    }


    public function showPaymentGet($res)
    {
        $user_id = WISession::get('user_id');
        $receipt_id = $res['id'];
        $cost = $res['purchase_units'][0]['amount']['value'];
        $tax = $res['purchase_units'][0]['amount']['breakdown']['tax_total']['value'];
        $total = $res['purchase_units'][0]['amount']['value'];
        $item_cost = $res['purchase_units'][0]['amount']['breakdown']['item_total']['value']; 
        $tax_cost = $res['purchase_units'][0]['amount']['breakdown']['tax_total']['value']; 

        $cc = $res['purchase_units'][0]['amount']['currency_code'];

        $status = $res['status'];

        $receipt = '<div class="row-fluid">
    <!-- Middle Section -->
    <div class="col-xs-12 col-md-12">
        <div id="loadingAlert"
             class="card"
             style="display: none;">
            <div class="card-body">
                <div class="alert alert-info block"
                     role="alert">
      <div align="center" class="ajax-loading hide"><img src="WIAdmin/WIMedia/Img/ajax_loader.gif" /></div>
                </div>
            </div>
        </div>
        <form id="orderConfirm"
              class="form-horizontal"
              style="display: block;">
            <h3>Your payment is authorized.</h3>
            <h4>Confirm the order to execute</h4>
            <hr>

            <div class="form-group">
                <div class="col-sm-7">
                </div>
            </div>

            <hr>
            <div class="form-group">
                <div class="col-sm-offset-5 col-sm-7">
                    <label class="btn btn-primary" id="confirmButton">Confirm</label>
                </div>
            </div>
        </form>
        <form id="orderView"
              class="form-horizontal"
              style="display: none;">
            <h3>Your payment is complete</h3>
            <h4>
                <span id="viewFirstName"></span>
                <span id="viewLastName"></span>,
                Thank you for your Order
            </h4>
            <hr>
            <div class="form-group">
                <div class="form-group">
                    <div class="col-sm-7">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label">Transaction Details</label>
                    <div class="col-sm-7">

                    </div>
                </div>
            </div>
            <hr>
        </form>
    </div>
</div>
';
    $msg = "You have successfully Paid for your items, thank you for your Payment.";
    $result = array(
        "status"             => $status,
        "msg"                => $msg,
        "receipt"            => $receipt,
        "item_cost"          => $item_cost,
        "tax_cost"           => $tax_cost,
        "cost"               => $cost,
        "cc"                 => $cc,
        "id"                 => $receipt_id

    );

    echo json_encode($result);
    }




  public function createOrder($item_amt,$item_qty, $item_title, $total_amt, $duration,$type, $place, $name, $selectedtime, $contact_no, $notes, $eventDate)
  {

    $this->WIdb->insert('wi_order', array(
            "item_amount"     => $item_amt,
            "duration"  => $duration,
            "date"  => $eventDate,
            "type" => $type,
            "place" => $place,
            "item_title" => $item_title,
            "name"  => $name,
            "startTime" => strip_tags($selectedtime),
            "contact_no" => $contact_no,
            "notes"    => $notes,
            "date_booked" => date('Y-m-d')

        )); 

    $orderId = $this->WIdb->lastInsertId();

    echo $orderId;
    return $orderId;
  }

  public function appointmentfinish($email, $docReceipt)
  {
    $this->mailer->confirmationAppointmentEmail($email, $docReceipt);
  }

  public function training()
  {
    echo '<div id="training">
    </div>';
  }

      public function gettrainingTypes()
     {

        $eventListHTML = '';
        $result = $this->WIdb->select("SELECT `name` FROM `wi_trainer_types`");

        if(count($result) > 0){
        // var_dump($result);

        echo '<h2>Training Types</h2>';
        echo '<select id="type">
    <option value="select one" selected >please select one...</option>';
      // echo "title" . $result[0]["title"];

       foreach ($result as $res) {
         // echo "title2" . $res['title'];
                  echo '<options value="'. $res["name"] .'">'. $res["name"] .'</option>';
       }

          echo '</select>';
        }else{
          echo "No results found.";
        }
    }

  public function trainingType($date, $className)
  {
    echo '<div class="col-md-12"><p>Book training on '.date("l, d M Y",strtotime($date)).'<span id="eventDateView"></span></p>
    <p>
    <input type="hidden" id="eventDateTraining" value="'.$date.'">
    <input type="hidden" id="eventTypeTraining" value="">
    <div>' . $className . '</div>';

    //self::gettrainingTypes();
    echo '<script type="text/javascript">

    $("select#type").on("change", function() {
      // alert( this.value );

      var type = this.value;
      console.log(type);
      $(this.value).attr("checked", "checked");
      $(this.value).attr("selected", "selected");
      $("#eventTypeTraining").attr("value", type);


      var date = $("#eventDateTraining").val();
      //var type = $("#eventTypeTraining").val();
      console.log(date);
      console.log(type);
      $("#train").attr("onclick", "WIAppointment.endTrain(\''.$date.'\');");
                
      });


      </script>';


  }

  public function availableSlots()
  {
    echo '<div id="bookings">
    </div>';
  }


  public function timeSlots($date, $type)
  {
    $results = $this->WIdb->select('SELECT * FROM `wi_events` WHERE `date`=:d',array("d" => $date) );

    $start = "08:00";
    $end =   "18:00";

    if($type == "beginner" || "advanced"){
    $interval = "60";
    }else{
      $interval = "30";
    }

    
    $start = new DateTime($start);
    $end = new DateTime($end);
    $start_time = $start->format('H:i'); // Get time Format in Hour and minutes
    $end_time = $end->format('H:i');
    $i=0;

    echo '<p>Book training on '.date("l, d M Y",strtotime($date)).'</p>';

    echo '<ul id="timeslots" class="timeslots">';
    //var_dump($results);
    if(count($results) > 0){

      //var_dump($results);
    while(strtotime($start_time) <= strtotime($end_time)){

      foreach ($results as $res) {
        ksort($res);
        //var_dump($res);
          $trainTime = $res['startTime'];
          $duration = $res['duration'];
          $type = $res['type'];
          $endTrainTime = $res['endTime'];

          $start = $start_time;
        $end = date('H:i',strtotime('+'.$interval.' minutes',strtotime($start_time)));
        $start_time = date('H:i',strtotime('+'.$interval.' minutes',strtotime($start_time)));
        $i++;
        //echo "start".$start;
        //echo "end". $end;
        $timeslotavailable = self::findTime($start, $date);
        //echo "avail "; var_dump($timeslotavailable);
        if(strtotime($start) <= strtotime($end_time) 
          && $timeslotavailable == 0
        ){
          echo '<li class="slots" id="'.$i.'">
            <input value="'.$time[$i]['start'] = $start.'" id="timing" type="hidden">
          <div id="time">
          '. $time[$i]['start'] = $start.'
          </div>
          <a href="javascript:void(0);" onclick="WIAppointment.select(`'.$time[$i]['start'] = $start.'`,`'.$i.'`)">Book Slot</a>
          </li>';
        }else{

           if(strtotime($start) <= strtotime($end_time)
            && $timeslotavailable == 1
          )
         {

          echo '<li class="taken">
          '. $time[$i]['start'] = $start.'
          <div>Slot Taken</div>
          </li>';
          }else{
        
            if(
          strtotime($start_time) <= strtotime($end_time) ){
            echo '<li class="slots" id="'.$i.'">
            <input value="'.$time[$i]['start'] = $start.'" id="timing" type="hidden">
          <div id="time">
          '. $time[$i]['start'] = $start.'
          </div>
          <a href="javascript:void();" onclick="WIAppointment.select(`'. $time[$i]['start'] = $start.'`,`'.$i.'`)">Book Slot</a>
          </li>';
             }
          }

        }

        }// end foreach    
    }// end while
    echo '</ul>';


  }else{


      while(strtotime($start_time) <= strtotime($end_time)){

        $start = $start_time;
        $end = date('H:i',strtotime('+'.$interval.' minutes',strtotime($start_time)));
        $start_time = date('H:i',strtotime('+'.$interval.' minutes',strtotime($start_time)));
        $i++;

        if(strtotime($start) <= strtotime($end_time) ){
          echo '<li class="slots" id="'.$i.'">
            <input value="'.$time[$i]['start'] = $start.'" id="timing" type="hidden">
          <div id="time">
          '. $time[$i]['start'] = $start.'
          </div>
          <a href="javascript:void(0);" onclick="WIAppointment.select(`'.$time[$i]['start'] = $start.'`,`'.$i.'`)">Book Slot</a>
          </li>';
        

          }

        

    }// end while
    echo '</ul>';
  }
  
  

}

  public function details()
  {
    echo '<form id="payerdetails">
    <div id="details">
    </div></form>';
  }

  public function completeDetails($date, $type)
  {

    if($this->login->isLoggedIn()){
      $username =  $this->Info->getUserInfo('username');
      echo '<div class="col-md-12"><p><b>Please enter your full name ' . $username . '</b><input type="text" id="fullname" value="" placeholder="' . $username . '"/></p></div>';
    }else{
      
      echo '<div class="col-md-12"><label>Full name:</label><input type="text" id="fullname" value="" placeholder="John Doe"/></p></div>';
    }

    echo '<div class="col-md-12"><p>PLease completed details for your training session on '.date("l, d M Y",strtotime($date)).'<span id="eventDateView"></span></p>
    <p>

    <div class="col-md-12"> 
    <label>Place:</label>
    <input type="hidden" id="placement">
    <select id="place">
    <option value="select one" selected >please select one...</option>
    <option value="gym">gym</option>
    <option value="webcam">webcam</option>
    </select>
    </div>

    <div class="col-md-12">
    <label>Your contact Number:</label>
    <input type="number" id="contact_no"/>
    </div>
    <div class="col-md-12">
    <label>Notes:</label>
    <textarea id="booking_notes"></textarea>
    <input type="hidden" id="eventDate" value=""/>

    <script type="text/javascript">

            $("select#place").on("change", function() {
             // alert( this.value );

              var place = this.value;
              console.log(place);
              $(this.value).attr("checked", "checked");
              $(this.value).attr("selected", "selected");
              $("#placement").attr("value", place);
            })';
  }

  public function payment()
  {
    echo '<div id="payment">
    </div>';
  }

  public function paypalPayment($date, $type, $duration, $startTime, $placement, $contact_no, $name, $notes)
  {
    foreach ($startTime as $key ) {
      echo $key;
    }

    echo '<!-- // payment api code for PayPal
                // DO NOT  ALTER !!!-->

              </div>
              <div id="paypalCheckoutContainer"></div>
              <div id="paypalpay" class="hide"></div>
              <div id="emailReceipt" class="hide">
              <label>Email address</label>
              <input type="email" name="email" id="email_Receipt" />
              <button onclick="WIAppointment.finish();">Next</button> 
              </div>';

echo "<!-- PayPal In-Context Checkout script -->
            <script type='text/javascript'>

            paypal.Buttons({

        // Set your environment
        env: '". PAYPAL_ENVIRONMENT ."',


        // Set style of buttons
        style: {
            layout: 'horizontal',   // horizontal | vertical
            size:   'responsive',   // medium | large | responsive
            shape:  'pill',         // pill | rect
            color:  'gold',         // gold | blue | silver | black,
            fundingicons: false,    // true | false,
            tagline: false          // true | false,
        },

        // Wait for the PayPal button to be clicked
        createOrder: function() {

            let currency = '".  CURRENCY ."'
            let formData = new FormData();
/*            let type = $('#type option:selected').val();
            let place = $('#place option:selected').val();
            let duration = $('#duration').val();
            let contact_no = $('#mobile').val();
            let notes = $('textarea#notes').val();
            let name = $('#fullname').val();
            let  selectedtime = $('#timepicker').val();
            let eventDate = $('#eventDate').val();*/

            var eventDate = '".$date."';
            var type = '".$type."';
            var duration = '".$duration."';
            var place = '".$placement."';
            var contact_no = '".$contact_no."';
            var name = '".$name."';
            var notes = '".$notes."';
            var selectedtime = '".$startTime."';
            

            
            console.log(selectedtime);
            selectTime = JSON.parse(selectedtime);
            console.log(selectTime);

            $.each(selectTime, function(key, value){
              console.log(key , value);

            });


            console.log(eventDate);
            if(type == 'beginner'){

              if(duration >= 4){
                    item_amt = 30
                    formData.append('item_amt', item_amt);
                    formData.append('item_title', duration +' hour '+ type +' session')
                    formData.append('item_qty', duration)
                    formData.append('total_amt', item_amt * duration);
                    formData.append('duration', duration);
                    formData.append('type', type);
                    formData.append('place', place);
                    formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
                    formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');

                    

                    WIAppointment.create(item_amt, duration, duration +' hour '+ type +' session', item_amt * duration, duration, type, place, name, selectedtime, contact_no, notes, eventDate);
                    


                  }else{
                    item_amt = 35
                    formData.append('item_amt', item_amt);
                    formData.append('item_title', duration +' hour '+ type +' session')
                    formData.append('item_qty', duration)
                    formData.append('total_amt', item_amt * duration);
                    formData.append('duration', duration);
                    formData.append('type', type);
                    formData.append('place', place);
                    formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
                    formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');

                    

                   //WIAppointment.create(item_amt, duration, duration +' hour '+ type +' session', item_amt * duration, duration, type, place, name, selectedtime, contact_no, notes, eventDate);
                  }
              

                  
              }else if(type == 'advanced'){

                  if(duration >= 4){
                    item_amt = 30
                    formData.append('item_amt', item_amt);
                    formData.append('item_title', duration +' hour '+ type +' session')
                    formData.append('item_qty', duration)
                    formData.append('total_amt', item_amt * duration);
                    formData.append('duration', duration);
                    formData.append('type', type);
                    formData.append('place', place);
                    formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
                    formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');

                    WIAppointment.create(item_amt, duration, duration +' hour '+ type +' session', item_amt * duration, duration, type, place, name, selectedtime, contact_no, notes,eventDate);

                  }else{
                    item_amt = 35
                    formData.append('item_amt', item_amt);
                    formData.append('item_title', duration +' hour '+ type +' session')
                    formData.append('item_qty', duration)
                    formData.append('total_amt', item_amt * duration);
                    formData.append('duration', duration);
                    formData.append('type', type);
                    formData.append('place', place);
                    formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
                    formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');
                    WIAppointment.create(item_amt, duration, duration +' hour '+ type +' session', item_amt * duration, duration, type, place, name, selectedtime, contact_no, notes,eventDate);

                  }

                }else if(type == 'fitness blast'){

                  if(duration >= 4){
                    item_amt = 15
                    formData.append('item_amt', item_amt);
                    formData.append('item_title', duration +' 30 min '+ type +' session')
                    formData.append('item_qty', duration)
                    formData.append('total_amt', item_amt * duration);
                    formData.append('duration', duration);
                    formData.append('type', type);
                    formData.append('place', place);
                    formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
                    formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');
                    WIAppointment.create(item_amt, duration, duration +' hour '+ type +' session', item_amt * duration, duration, type, place, name, selectedtime, contact_no, notes, eventDate);

                  }else{
                    item_amt = 20
                    formData.append('item_amt', item_amt);
                    formData.append('item_title', duration +' 30 min '+ type +' session')
                    formData.append('item_qty', duration)
                    formData.append('total_amt', item_amt * duration);
                    formData.append('duration', duration);
                    formData.append('type', type);
                    formData.append('place', place);
                    formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
                    formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');
                    WIAppointment.create(item_amt, duration, duration +' hour '+ type +' session', item_amt * duration, duration, type, place, name, selectedtime, contact_no, notes, eventDate);

                  }

                }


            return fetch(
                '".URL['services']['orderCreate']."',
                {
                    method: 'POST',
                    body: formData
                }
            ).then(function(response) {
                //console.log(response);
                return response.json();
            }).then(function(resJson) {
                //console.log(resJson);
                
            //console.log('Order ID: '+ resJson.data.id);
            return resJson.data.id;
            });
        },

        // Wait for the payment to be authorized by the customer
        onApprove: function(data, actions) {
            return fetch(
                '". URL['services']['orderGet'] ."',
                {
                    method: 'GET'
                }
            ).then(function(res) {
                return res.json();
            }).then(function(res) {
              //console.log(res);
              ord = sessionStorage.getItem('ord');
              //console.log(ord);
                WIAppointment.pushpayment(res, ord);

            });
        }

    }).render('#paypalCheckoutContainer');

</script>";
  }


  public function findTime($time, $date)
  {
    $result = $this->WIdb->select("SELECT * FROM `wi_events` WHERE `date`=:d", array("d" => $date));

    //var_dump($result['startTime']);
    $loop = count($result);
    //echo "loop" . $loop;
    $count = "0";
    foreach($result as $res){

      $bookedTime = $res['startTime'] ;
      //echo $bookedTime;
      $bookedTime = trim($bookedTime,'[{}""""{}]');
      $bookedTime = explode(",", $bookedTime);
      $count1 ="0";
      foreach ($bookedTime as $i => $key ) {
         //echo "bl " . $key;
      //var_dump($key);
      //echo "book". $key;
      //echo "count " . $count;

      if(trim($key, '"') == $time){
        return "1";
        if($count1 == $i){
          return "0";
        }
        
      }
      $count1++;
      }
     
      $count++;

    
    }
  }


 
}