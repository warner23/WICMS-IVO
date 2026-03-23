<?php
#[\AllowDynamicProperties]
class WIStaff 
{

	public function __construct()
	{
		$this->WIdb = WIdb::getInstance();
    $this->Modal = new WIModal();
	}	

	public function GetStaff()
	{
		echo '<!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="javascript:void(0);" class="btn btn-outline-success" onclick="WIStaff.OpenNewStaff();">
                <i class="fas fa-user-plus"></i>
                Add New Customer
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Full Name</th>
                    <th scope="col">Role</th>
                    <th scope="col">Site</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                <div class="container-fluid mt--8">';
		$results = $this->WIdb->select('SELECT * FROM `wi_compliance_staff` ORDER BY `wi_compliance_staff`.`created_at` DESC');

		if($results > 0){
			foreach($results as $res){
				echo '<tr>
                      <td>' . $res['staff_name'] . '</td>
                      <td>' . $this->staffRole($res['staff_role']) . '</td>
                      <td>' . $this->staffSite($res['staff_site']) . '</td>
                      <td>
                        <a href="customes.php?delete=<?php echo $cust->customer_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                          </button>
                        </a>

                        <a href="update_customer.php?update=<?php echo $cust->customer_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-user-edit"></i>
                            Update
                          </button>
                        </a>
                      </td>
                    </tr>';
			}
		}
		echo '</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>';
      $this->Modal->moduleModal('AddStaff', 'Add New Staff', 'WIStaff', 'AddStaff','AddStaff'); 
	}

  public function staffRole($id)
  {
    $results = $this->WIdb->select('SELECT * FROM `wi_compliance_roles` WHERE `id`=:id', array(
      "id" => $id
    ));

    if($results > 0){
      return $results[0]['role'];
    }
  }

  public function staffSite($id)
  {
    $results = $this->WIdb->select('SELECT * FROM `wi_sites` WHERE `id`=:id', array(
      "id" => $id
    ));

    if($results > 0){
      return $results[0]['name'];
    }
  }

}

?>