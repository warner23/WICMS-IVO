<?php
#[\AllowDynamicProperties]
/**
 * cafe class.
 */
class WITransaction 
{

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->User = new WIUser(WISession::get('user_id') );
    }


    public function viewTrans()
    {
        echo '<div class="col-8 col-sm-8 col-xs-8 col-md-8">';
        $user_id = $this->User->id();

        $trans = $this->WIdb->select('SELECT * FROM `wi_course_orders` WHERE `user_id`=:id', array("id" => $user_id));

        echo '<div class="transa" style="border: 2px solid yellow;"><ul>';

        if(count($trans) > 0){
            foreach ($trans as $tran) {
            $order_id = $tran['id'];
             $order = $this->WIdb->select('SELECT * FROM `wi_booking_order_list` WHERE `course_order_id`=:id', array("id" => $order_id));

             if(count($order) > 0){
                foreach ($order as $ord ) {
                 echo '<li style="border: 2px solid #b1aaaa;">
                 <div class="tran">
                <div class="item" style="float:left;">
                <img src="../WIAdmin/WIMedia/Img/courses/course/thumb/'. $ord['image'] .'">
                </div>

                <div class="boxer">
                <div class="item_title">'. $ord['name'] .'</div>
                <div class="description">'. $ord['description'] .'</div>
                <div class="price">'. $ord['price'] .'</div>
                </div>
                </div></li>';
                }
             }else{
                
             }
         }


        }else{
            echo "Sorry you have no orders to view.";
        }


         echo '</ul></div></div>';
    }


}



?>