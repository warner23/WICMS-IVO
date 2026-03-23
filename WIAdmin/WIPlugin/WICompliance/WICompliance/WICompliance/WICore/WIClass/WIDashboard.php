<?php
#[\AllowDynamicProperties]
class WIDashboard 
{

	public function __construct()
	{
		$this->WIdb = WIdb::getInstance();
    $this->Comp = new WICompliance();
    $this->Modal = new WIModal();
	}
  
	public function dashboard()
	{
		echo '<div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="pb-8 pt-5 pt-md-8">
      <div class="container">';
      $this->Comp->Compliance();
      echo '</div>
      </div>';
 
	}

	public function WIProducts()
    {
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wipos_products` ');
        return $results;
    }

    public function WIPayments()
    {
       $results = $this->WIdb->select('SELECT * FROM   wipos_payments   ORDER BY `wipos_payments`.`created_at` DESC LIMIT 7');
        echo '<tbody>';
        if($results > 0){
            foreach($results as $res){
                echo '<tr>
                      <th class="text-success" scope="row">
                      '. $res['pay_code'].'
                      </th>
                      <td>
                        £'. $res['pay_amt'].'
                      </td>
                      <td class="text-success">
                        '. $res['order_code'].'
                      </td>
                    </tr>';
            }
            echo '</tbody>';
    }
   }

	//analysitics

   public function WICountSites()
    {
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wi_sites`');

        if($results > 0){
          //var_dump($results);
            return $results[0]['COUNT(*)'];
        }
    }

	public function WICountFaultyFridges()
    {
      $temp = "faulty";
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wi_fridge_temps` WHERE `temp`=:temp', array(
          "temp" => $temp
        ));

        if($results > 0){
        	//var_dump($results);
            return $results[0]['COUNT(*)'];
        }
    }

      public function WICountFaultyFreezers()
    {
      $temp = "faulty";
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wi_freezer_temps` WHERE `temp`=:temp', array(
          "temp" => $temp
        ));

        if($results > 0){
          //var_dump($results);
            return $results[0]['COUNT(*)'];
        }
    }

    public function WICountChecklists()
    {
      $site = $this->StaffSite(WISession::get('staff_id'));
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wi_compliance_dashboard` WHERE `site`=:site',array(
          "site"  => $site
        ));

        if($results > 0){
            return $results[0]['COUNT(*)'];
        }
    }

    public function WICountOrders()
    {
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wipos_orders`');

        if($results > 0){
            return $results[0]['COUNT(*)'];
        }
    }

    public function WICountProducts()
    {
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wipos_products`');

        if($results > 0){
            return $results[0]['COUNT(*)'];
        }
    }

        public function WICountSales()
    {
        $results = $this->WIdb->select('SELECT SUM(pay_amt) FROM `wipos_payments`');

        if($results > 0){
            return $results[0]['SUM(pay_amt)'];
        }
    }

    public function StaffSite($id)
    {
        //echo "id ".$id;
        $res = $this->WIdb->select('SELECT * FROM `wi_compliance_staff` WHERE `staff_id`=:id',array(
            "id" => $id
        ));
        if($res > 0){
            //var_dump($res);
            $rid = $res[0]['staff_site'];
          return $rid;
            
        }
    }

    public function SiteName($id)
    {
        //echo "id ".$id;
        
        $results = $this->WIdb->select('SELECT * FROM `wi_sites` WHERE `id`=:rid',array(
            "rid" => $id
        ));
            if($results > 0){
                return $results[0]['name'];
            }
    }

    public function WIOrder()
    {
      $site = $this->StaffSite(WISession::get('staff_id'));
        $results = $this->WIdb->select('SELECT * FROM `wi_compliance_dashboard` WHERE `site`=:site  ORDER BY `wi_compliance_dashboard`.`date` DESC LIMIT 7', array(
          "site"  => $site 
        ));
        echo '<tbody>';
        if($results > 0){
            foreach($results as $res){
                echo '  <tr>
                      <td>'.$res['name'].'</td>
                      <td class="text-success">'.$this->SiteName($res['site']).'</td>
                      <td>'.$res['time'].'</td>
                      <td class="text-success">'. date('d/M/Y g:i', strtotime($res['date'])).'</td>
                      <td>


                        <a href="javascript:void(0);" onclick="WIDashboard.Compedit(`'.$res['group_id'] . '`,`'.$res['name'] . '`);">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-user-edit"></i>
                            Update
                          </button>
                        </a>
                      </td>';
            }
            echo '</tr>
                </tbody>';
        }
    }

}