<?php
/**
 * WIABS Anti Bully System class.
 */
class WIABS
{


    public function __construct()
    {
       $this->WIdb   = WIdb::getInstance();
       $this->users  = new WIUser(WISession::get('user_id'));
    }

    public function ABSC($phase)
    {

        $result = $this->WIdb->select('SELECT * FROM `wi_abs`');

        if(count($result) > 0){

            foreach($result as $res){
                $sentence = $res['phase'];

                if($phase == $sentence){

                    return true;
                }else{
                    return false;
                }
            }

        }else{

        }
    }

}
