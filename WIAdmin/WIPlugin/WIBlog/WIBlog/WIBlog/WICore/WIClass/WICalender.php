<?php


class WICalendar 
{  
  private $WIdb;

  function __construct()
  {
    $this->WIdb =  WIdb::getInstance();
    $this->login = new  WILogin();
    $this->Info = new WIUserInfo();
    $this->user   = new WIUser(WISession::get('user_id'));
    //$WIdb = WIdb::getInstance();
  }


  public function getCalendar($year = '', $month = '')
  {
      $dateYear = ($year != '')?$year:date("Y");
  $dateMonth = ($month != '')?$month:date("m");
  $date = $dateYear.'-'.$dateMonth.'-01';
  $currentMonthFirstDay = date("N",strtotime($date));
  $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear);
  $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7)?($totalDaysOfMonth):($totalDaysOfMonth + $currentMonthFirstDay);
  $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42;
  echo '<div id="calender_section"><h2>
  ' . date("Y",strtotime($date.' - 1 Month')) .',' .date("m",strtotime($date.' - 1 Month')).'
          <span class="day btn" id="day" onclick="WIAppointment.getDayCalendar()">Day</span>
          <span class="week btn" id="week" onclick="WIAppointment.getWeekCalendar()">Week</span>
          <span class="month btn active" id="month" onclick="WIAppointment.getCalendar()">Month</span>
          <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' - 1 Month')).'`,`'.date("m",strtotime($date.' - 1 Month')).'`);">&lt;&lt;</a>
            <select name="month_dropdown" class="month_dropdown dropdown">'.WICalendar::getAllMonths($dateMonth).'
            </select>
      <select name="year_dropdown" class="year_dropdown dropdown">'.WICalendar::getYearList($dateYear).'</select>
            <a href="javascript:void(0);" onclick="WICalendar.getCalendar(`calendar_div`,`'.date("Y",strtotime($date.' + 1 Month')).'`,`'. date("m",strtotime($date.' + 1 Month')).'`);">&gt;&gt;</a>
        </h2>
    <div id="event_list" class="none"></div>
    <div id="event_add" class="none"><h1>Add Event</h1>';
    if($this->login->isLoggedIn()){
      $username =  $this->Info->getUserInfo('username');
      echo '<div class="col-md-12"><p><b>Please enter your full name ' . $username . '</b><input type="text" id="fullname" value="" placeholder="' . $username . '"/></p></div>';
    }else{
      
      echo '<div class="col-md-12"><p><p><b>Full name: </b><input type="text" id="fullname" value="" placeholder="John Doe"/></p></div>';
    }
    echo '<div class="col-md-12"><p>Add Event on <span id="eventDateView"></span></p>
    <p><b>Event Title: </b><input type="text" id="eventTitle" value="" placeholder="Training"/></p></div>

    <div class="col-md-12 input-group bootstrap-timepicker timepicker">
    <b>Pick your Time : </b>
  <input id="timepicker" type="text" class="form-control input-small">
  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
</div>

    <div class="col-md-12"><p><b>Your contact Number: </b><input type="number" id="mobile"/></p></div>
    <input type="hidden" id="eventDate" value=""/>
    <input type="button" id="addEventBtn" onclick="WIAppointment.addEventBtn()" value="Add"/>
</div>
<div class="calendar_wrap">
    <div id="calender_section_top">
      <ul style="width: 100%;">
        <li>Sunday</li>
        <li>Monday</li>
        <li>Tuesday</li>
        <li>Wednesday</li>
        <li>Thursday</li>
        <li>Friday</li>
        <li>Saturday</li>
      </ul>
    </div>

    <div id="calender_section_bot">
      <ul>';
        $dayCount = 1; 
        for($cb=1;$cb<=$boxDisplay;$cb++){
          if(($cb >= $currentMonthFirstDay+1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)){
            //Current date
            $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount;
            $eventNum = 0;
            //Get number of events based on the current date
            
        
           $status = "active";
            $result = $this->WIdb->select(
                    "SELECT `title` FROM `wi_events` WHERE dating =:d AND status = :s",
                     array(
                       "d" => $currentDate,
                       "s" => $status
                     )
                  );
            //var_dump($result);
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($currentDate) == strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create grey date_cell"><a href="javascript:void(0);" onclick="WIAppointment.addEvent(\''.$currentDate.'\');">add event</a>';
            }elseif($eventNum > 0){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell"><a href="javascript:void(0);" onclick="WIAppointment.addEvent(\''.$currentDate.'\');">add event</a>';
            }elseif(strtotime($currentDate) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell"><a href="javascript:void(0);" onclick="WIAppointment.addEvent(\''.$currentDate.'\');">add event</a>';
            }else{
              echo '<li date="'.$currentDate.'" class="appointment_create date_cell">';
            }
            //Date cell
            echo '<span>';
            echo $dayCount;
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
        echo '<li class="appointment_create"><span>&nbsp;</span></li>';
       } } 
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

  echo '<div id="calender_section"><h2>
 <span class="day btn" id="day" onclick="WIAppointment.getDayCalendar()">Day</span>
          <span class="week btn active" id="week" onclick="WIAppointment.getWeekCalendar()">Week</span>
          <span class="month btn" id="month" onclick="WIAppointment.getCalendar()">Month</span>

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
for( $days = 7; $days--; ) {
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
            $eventNum = 0;
            //Get number of events based on the current date
            
        
           $status = "active";
            $result = $this->WIdb->select(
                    "SELECT `title` FROM `wi_events` WHERE dating =:d AND status = :s",
                     array(
                       "d" => $currentDate,
                       "s" => $status
                     )
                  );
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($currentDate) == strtotime(date("Y-m-d"))){
              echo '<li date="" class="grey date_cell">';
            }elseif($eventNum > 0){
              echo '<li date="'.$currentDate.'" class="light_sky date_cell">';
            }elseif(strtotime($currentDate) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell"><a href="javascript:void(0);" onclick="WIAppointment.addEvent(\''.$currentDate.'\');">add event</a>';
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

  echo '<div id="calender_section"><h2>
<span class="day btn active" id="day" onclick="WIAppointment.getDayCalendar()">Day</span>
          <span class="week btn" id="week" onclick="WIAppointment.getWeekCalendar()">Week</span>
          <span class="month btn" id="month" onclick="WIAppointment.getCalendar()">Month</span>
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
for( $hours = 6; $hours<=$weekDisplay; $hours++ ) {
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
            //Get number of events based on the current date
            
        
           $status = "active";
            $result = $this->WIdb->select(
                    "SELECT `title` FROM `wi_events` WHERE dating =:d AND status = :s",
                     array(
                       "d" => $currentDate,
                       "s" => $status
                     )
                  );
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($currentDate) == strtotime(date("Y-m-d"))){
              echo '<li date="" class="grey date_cell">';
            }elseif($eventNum > 0){
              echo '<li date="'.$currentDate.'" class="light_sky date_cell">';
            }elseif(strtotime($currentDate) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell"><a href="javascript:void(0);" onclick="WIAppointment.addEvent(\''.$currentDate.'\');">add event</a>';
            }else{
              echo '<li date="" class="date_cell">';
            }
            //Date cell
            echo '<span>';
            echo $dayCount;
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
                          "SELECT `title` FROM `wi_events` WHERE `dating` =:d AND `status` = :s ",
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
        $result = $this->WIdb->select("SELECT `title` FROM `wi_appointment_type`");

        if(count($result) > 0){
        // var_dump($result);

        echo '<h2>Appointment Types</h2>';
        echo '<ul>';
      // echo "title" . $result[0]["title"];

       foreach ($result as $res) {
         // echo "title2" . $res['title'];
                  echo '<li id="eventDrag" class="drag ui-draggable-handle ui-draggable">'. $res["title"] .'</li>';
       }

          echo '</ul>';
        }
    }

    public function addEventBtn($appointment)
    {
      $user = $appointment['UserData'];
      $status = "active";
       $this->WIdb->insert('wi_events', array(
            "title"     => strip_tags($user['title']),
            "status"  => $status,
            "dating"  => $user['dating'],
            "timing"  => $user['timing'],
            "name" => $user['name'],
            "contact_no" => $user['contact_no']
        )); 
    }

 
}