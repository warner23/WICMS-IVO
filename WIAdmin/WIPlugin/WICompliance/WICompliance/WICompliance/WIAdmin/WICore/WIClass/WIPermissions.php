<?php
#[\AllowDynamicProperties]
/**
* WIPermissions Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIPermissions
{
    function __construct() {
       $this->WIdb = WIdb::getInstance();
       $this->maint = new WIMaintenace();
    }

    public function hasPermission($role_id)
    {
      $results = $this->WIdb->select("SELECT * FROM `wi_user_permissions` WHERE `role_id` = :role_id", array("role_id" => $role_id));
       //var_dump($results);
       if($results>0){

         $perm = $results[0]['create'];
          return $perm;
       }

    }
   
}