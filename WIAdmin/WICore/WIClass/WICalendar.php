<?php
#[\AllowDynamicProperties]

class WICalendar 
{  
  private $WIdb;

  function __construct()
  {
    $this->WIdb =  WIdb::getInstance();
    $WIdb = WIdb::getInstance();
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
            
        
           $status = "COMPLETED";
            $result = $this->WIdb->select(
                    "SELECT `title` FROM `wi_events` WHERE `date` =:d AND `status` = :s",
                     array(
                       "d" => $currentDate,
                       "s" => $status
                     )
                  );
            //var_dump($result);
            $eventNum = count($result);

                        //Define date cell color
            if(strtotime($currentDate) == strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create grey date_cell">';
            }elseif($eventNum > 0){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell">';
            }elseif(strtotime($currentDate) > strtotime(date("Y-m-d"))){
              echo '<li date="'.$currentDate.'" class="appointment_create light_sky date_cell">';
            }else{
              echo '<li date="'.$currentDate.'" class="appointment_create date_cell">';
            }
            //Date cell
            echo '<span>';
            echo $dayCount;
            echo '</span>';
            
            //Hover event popup
            echo '<div id="date_popup_'.$currentDate.'" class="date_popup_wrap">';
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
 public function getEvents($date)
     {

        $eventListHTML = '';
       echo "date". $date;
        //Get events based on the current date
       // $result = $WIdb->query("SELECT title FROM events WHERE date = '".$date."' AND status = 1");

         $status = "COMPLETED";
                  $result = $this->WIdb->select(
                          "SELECT * FROM `wi_events` WHERE date =:d AND status = :s ",
                           array(
                             "d" => $date,
                             "s" => $status
                           )
                        );

        if(count($result) > 0){

        echo '<h2>Training '.$result[0]['name'].' on '.date("l, d M Y",strtotime($date)).'</h2>';
        echo '<ul>';
       foreach ($result as $res) {
         // echo "title2" . $res['title'];
          echo '<li><div>
              <div>'. $res["title"] .'</div>
              <div> for '. $res["duration"] .'</div>
              <div> at '. $res["place"] .'</div>
              <div> type '. $res["type"] .'</div>
              <div> contact '. $res["contact_no"] .'</div>
          </div>
          </li>';
       }

          echo '</ul>';
        }
    }

 
}