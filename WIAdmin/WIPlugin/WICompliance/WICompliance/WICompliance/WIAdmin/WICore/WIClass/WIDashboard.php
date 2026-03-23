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
      echo '<div class="header-body">
          <!-- Card stats -->
          <div class="row">
          <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Missed Compliance</h5>
                      <span class="h2 font-weight-bold mb-0">'.$this->MissedComplianceCount().'</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                        <i class="fas fa-users"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Faulty Fridges</h5>
                      <span class="h2 font-weight-bold mb-0">'.$this->WICountFaultyFridges().'</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                        <i class="fas fa-utensils"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Faulty Freezers</h5>
                      <span class="h2 font-weight-bold mb-0">'.$this->WICountFaultyFreezers().'</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                        <i class="fas fa-utensils"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Completed Checklists</h5>
                      <span class="h2 font-weight-bold mb-0">'.$this->WICountChecklists().'</span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                        <i class="fas fa-shopping-cart"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>';

      echo '<div class="row">
        <div class="col-xl-12 mb-5 mb-xl-0">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0">Completed Compliance</h3>
                </div>
                <div class="col text-right">
                  <a href="completed_compliance.php" class="btn btn-sm btn-primary">See all</a>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <!-- Projects table -->
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <th scope="col"><b>Name</b></th>
                    <th class="text-success" scope="col"><b>Site</b></th>
                    <th scope="col"><b>Time</b></th>
                    <th scope="col"><b>Date</b></th>
                    <th scope="col"><b>Action</b></th>
                  </tr>
                </thead>';
                $this->WIOrder();
              echo '</table>
            </div>
          </div>
        </div>
      </div>
      <script src="WICore/WIJ/WIDashboard.js"></script>';
      $this->Modal->moduleModal('editComp', 'Edit Compliance', 'WIDashboard', 'editComp','editComp'); 
  }

  public function WIProducts()
    {
        $results = $this->WIdb->select('SELECT COUNT(*) FROM `wipos_products` ');
        if($results > 0){
          return $results;
        }else{
          return "0";
        }
        
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
      $site = $this->StaffSite(WISession::get('user_id'));
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
        $res = $this->WIdb->select('SELECT * FROM `wi_user_details` WHERE `user_id`=:id',array(
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
        //echo $id.' user_id';
        $results = $this->WIdb->select('SELECT * FROM `wi_sites` WHERE `id`=:rid',array(
            "rid" => $id
        ));
            if($results > 0){
                return $results[0]['name'];
            }
    }

    public function WIOrder()
    {
      $site = $this->StaffSite(WISession::get('user_id'));
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

    public function MissedComplianceCount()
    {
      //$date = date("Y-m-d");
      //$time = date('h:i:s');
      //$end = date('Y-m-d', strtotime('now - 28day'));
      $yesterday = new DateTime('yesterday');
       //echo $yesterday->format("Y-m-d");

       $begin = new DateTime('2022-11-00');

       $end = new DateTime('2022-11-11');

       $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
       $count = "0";
       $compliance = "5";
       WISession::set('count', $count);

foreach($daterange as $date){

    $results = $this->WIdb->select('SELECT * FROM `wi_compliance_dashboard` WHERE `date`=:dat',array(
        "dat" => $date->format("Y-m-d") 
      ));

       if($results > 0){
          $due = count($results);
          if(count($results) < 5){
            $missed = $compliance - $due;
            $oldCount = WISession::get('count');
            $newCount = $missed + $oldCount;
           WISession::set('count', $newCount);
          }
       }

    //echo $date->format("Y-m-d") . "\n";
      }
      $complianceCount = WISession::get('count');
      if($complianceCount > 0){
        return $complianceCount;
      }else{
        return "0";
      }

    }

    public function MissedCompliance()
    {
      $date = date("Y-m-d");
      $time = date('h:i:s');
       
      $yesterday = new DateTime('yesterday');
       echo $yesterday->format("Y-m-d");

       $begin = new DateTime('2017-07-18');
$end = new DateTime('2017-08-08');

$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);

foreach($daterange as $date){
    echo $date->format("Y-m-d") . "\n";
}

       $count = "0";
       $compliance = "5";
       $results = $this->WIdb->select('SELECT * FROM `wi_compliance_dashboard` WHERE `date`=:dat',array(
        "dat" => $yesterday->format("Y-m-d")));

       if($results > 0){
          $due = count($results);
          if(count($results) < 5){
            $count = $compliance - $due;
            return $count;
          }
       }else echo "0";
    }

}