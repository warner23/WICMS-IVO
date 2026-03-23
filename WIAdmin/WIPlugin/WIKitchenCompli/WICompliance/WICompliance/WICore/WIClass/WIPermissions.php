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

        public function permissionTabs()
    {
        $result = $this->WIdb->select("SELECT * FROM `wi_user_roles`");

        echo ' <script>
                $( function() {

                  var index = "key";
              //  Define friendly data store name
              var dataStore = window.sessionStorage;
              //  Start magic!
              try {
                  // getter: Fetch previous value
                  var oldIndex = dataStore.getItem(index);
              } catch(e) {
                  // getter: Always default to first tab in error state
                  var oldIndex = 0;
              }

                  $( "#tabs" ).tabs({
        // The zero-based index of the panel that is active (open)
        active : oldIndex,
        // Triggered after a tab has been activated
        activate : function( event, ui ){
            //  Get future value
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            //  Set future value
            dataStore.setItem( index, newIndex ) 
        }
    }); 

    
    });
                </script>

                <div id="tabs">
              <ul>';
         foreach ($result as $tab) {
          echo  '<li><a href="#tabs-' . $tab['role_id'] . '">' . $tab['role'] . '</a></li>';
        }
        echo '</ul>';

        foreach ($result as $tab) {
          echo  '<div id="tabs-' . $tab['role_id'] . '">';
                  //$group_id = self::GroupUserTabs();
                  self::PermissionContents($tab['role_id'], $tab['role']);
                  echo '</div>'; 
        }
        echo '</div>';
    }

    public function GroupUserTabs($id)
    {
      //echo $id. 'sent';
            $results = $this->WIdb->select("SELECT * FROM `wi_user_group` WHERE `id`=:id",array("id" =>$id));
            //var_dump($results);
      if(count($results) > 0){
        return $results[0]['group'];
      }

    }


        public function PermissionContents($id, $role)
    {
        $result = $this->WIdb->select(
          "SELECT * FROM `wi_user_permissions` WHERE `role_id`=:id",
                     array("id" => $id));
        
        echo '<button id="cp" onclick="WIPermissions.modalcreatePerm(`' . $id . '`)">Create Permission</button>
        <div id="legend">
                        <legend class="user_role_id" id="' . $id .'">' . $role . ' Permissions 

                        <div class="sectionwrapper">
                        <div class="sect">View</div>
                        <div class="sect">Delete</div>
                        <div class="sect">Create</div>
                        <div class="sect">edit</div>
                        <div class="sect">Actions</div>
                        </div></legend>
                        </div>';
            echo '<form class="form-horizontal UPermission-form" id="UPermission-"'.$role.'>
            <fieldset>';
        foreach ($result as $res ) {
          //var_dump($res);
          echo ' <div>
                      
                        <div class="col-xs-12 col-md-12 col-lg-12">
                        <div class="col-xs-3 col-md-3 col-lg-3">
                           <label>' . self::GroupUserTabs($res['group_id']) . '</label>
                        </div>

                    <div class="col-xs-9 col-md-9 col-lg-9" id="editing" data-toggle="buttons-radio">

                    <input type="hidden" class="site" id="' . $res['id'] . '">
                    <div class="col-xs-12 col-md-12 col-lg-12">

                    <div class="sect">
                     <label class="switch">
                      <input type="hidden" name="view" id="view-' . $res['id'] . '" class="btn-group-value" value="' . $res['view'] . '"/>
                        <input type="checkbox" id-"view_site_' . $res['id'] . '" checked class="view_site">
                        <span class="slider round vi" id="vi-' . $res['id'] . '"></span>
                      </label>
                      </div>

                      <div class="sect">
                      <label class="switch">
                      <input type="hidden" name="del" id="del-' . $res['id'] . '" class="btn-group-value" value="' . $res['delete'] . '"/>
                        <input type="checkbox" id="delete_site_' . $res['id'] . '" checked>
                        <span class="slider round de" id="de-' . $res['id'] . '"></span>
                      </label>
                      </div>

                      <div class="sect">
                       <label class="switch">
                      <input type="hidden" name="create" id="create-' . $res['id'] . '" class="btn-group-value" value="' . $res['create'] . '"/>
                        <input type="checkbox" id="create_site_' . $res['id'] . '" checked>
                        <span class="slider round cr" id="cr-' . $res['id'] . '"></span>
                      </label>
                      </div>

                      <div class="sect">
                      <label class="switch">
                       <input type="hidden" name="edit" id="edit-' . $res['id'] . '" class="btn-group-value" value="' . $res['edit'] . '"/>
                        <input type="checkbox" id="edit_site_' . $res['id'] . '" checked>
                        <span class="slider round ed" id="ed-' . $res['id'] . '"></span>
                      </label>
                      </div>


                      <div class="btn-group" style="width:15%;float:right;">
                                  <a  class="btn btn-danger btn-user"
                                      href="javascript:void(0);"
                                      onclick="">

                                      <i class="icon-user icon-white glyphicon glyphicon-user"></i>
                                      <span class="user-role"><?php echo ucfirst($userRole); ?></span>
                                  </a>
                                  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                      <span class="caret"></span>
                                  </a>
                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);"
                                             onclick="WIPermissions.editPermission(' . $res['id'] . ');">
                                              <i class="icon-edit glyphicon glyphicon-edit"></i>
                                              '.WILang::get('edit').'
                                          </a>
                                      </li>

                                      <li class="divider"></li>

                                      <li>
                                            
                                          <a href="javascript:void(0);"
                                             onclick="WIPermissions.deletePermission(' . $res['id'] . ');">
                                              <i class="icon-trash glyphicon glyphicon-trash"></i>
                                              '.WILang::get('delete').'
                                          </a>
                                      </li>

                                      


                                  </ul>
                              </div>


                      </div>
                    </div>
                </div>  
                    
                        <br /><br />
                   <script type="text/javascript">
                       var edit = $("#edit-' . $res['id'] . '").attr(`value`);
                       if (edit === "0"){
                        $("#edit_site_' . $res['id'] . '").prop("checked", false);
                        $("#ed-' . $res['id'] . '").text(`OFF`);
                        $("#ed-' . $res['id'] . '").css(`padding-left`, `50%`);
                       }else if (edit === "1"){
                        $("#edit_site_' . $res['id'] . '").prop("checked", true);
                        $("#ed-' . $res['id'] . '").text(`ON`);
                       }

                       var create = $("#create-' . $res['id'] . '").attr(`value`);
                       console.log(create);
                       if (create === "0"){
                        $("#create_site_' . $res['id'] . '").prop("checked", false);
                        $("#cr-' . $res['id'] . '").text(`OFF`);
                        $("#cr-' . $res['id'] . '").css(`padding-left`, `50%`);
                       }else if (create === "1"){
                        $("#create_site_' . $res['id'] . '").prop("checked", true);
                        $("#cr-' . $res['id'] . '").text(`ON`);
                       }

                       var del = $("#del-' . $res['id'] . '").attr(`value`);
                       if (del === "0"){
                        $("#delete_site_' . $res['id'] . '").prop("checked", false);
                        $("#de-' . $res['id'] . '").text(`OFF`);
                        $("#de-' . $res['id'] . '").css(`padding-left`, `50%`);
                       }else if (del === "1"){
                        $("#delete_site_' . $res['id'] . '").prop("checked", true);
                        $("#de-' . $res['id'] . '").text(`ON`);
                       }

                       var view = $("#view-' . $res['id'] . '").attr(`value`);
                       if (view === "0"){
                        $("#view_site_' . $res['id'] . '").prop("checked", false);
                        $("#vi-' . $res['id'] . '").text(`OFF`);
                        $("#vi-' . $res['id'] . '").css(`padding-left`, `50%`);
                       }else if (view === "1"){
                        $("#view_site_' . $res['id'] . '").prop("checked", true);
                        $("#vi-' . $res['id'] . '").text(`ON`);
                       }
                   </script>
                  ';
        }
        echo '</fieldset>
        </form>';

      
    }



        public function site_perm($ed, $id, $edit)
    {
      //echo $edit;
      $perm = array($ed => $edit);

      $this->WIdb->update("wi_user_permissions", $perm,"`id` = :id",
                    array( "id" => $id) 
                  );
      $result = array(
        "status"  => "completed"
                );
      echo json_encode($result);
               
    }

    public function createPerm($data)
    {
      $data = $data['UserData'];

      $results = $this->WIdb->insert("wi_user_permissions", array(
        "role_id"   => $data['role'],
        "group"     => $data['group'],
        "perm_name" => $data['perm_name']
      ));

      $last = $this->WIdb->lastInsertId();

      if (!$last == ""){
              $msg = WILang::get('successfully_created_new_perm');

      $st1  = WISession::get('user_id');
      $st2  = "Created new permission";
      $this->maint->Notifications($st1, $st2);
      $result = array(
              "status" => "successful",
              "msg" => $msg
          );
            
            //output result
      echo json_encode ($result); 
    }else{
      $msg = WILang::get('there was a problem');

      $st1  = WISession::get('user_id');
      $st2  = "Created new permission";
      $this->maint->Notifications($st1, $st2);
      $result = array(
              "status" => "error",
              "msg" => $msg
          );
            
            //output result
      echo json_encode ($result); 
    }
 

    }
   
}