<?php
#[\AllowDynamicProperties]
/**
* 
*/
class WIModal 
{

		function __construct()
	{
		$this->WIdb = WIdb::getInstance();
        $this->Comp = new WICompliance();
	}

	public function new_modal($ele_id, $title, $action, $function, $button)
	{
		echo '<!-- Modal -->
<div class="modal hide" id="modal-'.$ele_id.'-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
        <button type="button" class="close" onclick="'.$action.'.close('.$ele_id.')" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <divv id="details-body"></div>';
        //self::$function();
      echo '</div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>';

	}

	public function moduleModal($ele_id, $title, $action, $function, $button)
	{
		echo '<!-- Modal -->
<div class="modal hide" id="modal-'.$ele_id.'-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">';
        self::header($action, $ele_id, $title);
        echo '</button>
      </div>
      <div class="modal-body">
      <div id="details-body">';
        self::$function($title);
      echo '</div></div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>';
	}

	public function delete()
	{
		echo 'Are you sure you want to delete ';
	}

	public function header($action, $ele_id, $title)
	{
		echo '<h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
        <button type="button" class="close" onclick="'.$action.'.closed('.$ele_id.')" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>';
	}

	public function footer($button)
	{
		echo ' <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">'.$button.'.</button>';
	}

    public function AddStaff()
    {
        echo '<div class="container-fluid mt--8">
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Please Fill All Fields</h3>
            </div>
            <div class="card-body">
              <form class="staff-form">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Staff Role</label>';
                    $this->Comp->staffRole();
                    echo '</div>
                  <div class="col-md-6">
                    <label>Staff Site</label>';
                    $this->Comp->staffSite();
                    echo '</div>
                  <div class="col-md-6">
                    <label>Staff Name</label>
                    <input type="text" name="staff_name" class="form-control" value="" id="staff_name">
                  </div>
                </div>

                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Staff Email</label>
                    <input type="email" name="staff_email" class="form-control" value="" id="staff_email">
                  </div>
                  <div class="col-md-6">
                    <label>Staff Password</label>
                    <input type="password" name="staff_password" class="form-control" value="" id="staff_password">
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <button name="addStaff" class="btn btn-success" id="AddNewStaff">Add Staff</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>';
    }

    public function editComp()
  {
    echo '<div id="editComp"></div>';
  }

}

?>