  <form class="form-horizontal" id="POS_settings">
     <fieldset>
                      <div id="legend">
                        <legend class="">Add / Edit POS Settings</legend>
                      </div>  


                       <div class="col-sm-12 col-lg-12 col-md-12 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-xs-12">
                        </div>


                        <div class="form-group">
          <!-- website name -->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
         <label class="control-label"  for="shop_name">Business Name:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="text" id="business_name"  maxlength="88" name="business_name" placeholder="Business Name" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('business_name')?>"> <br />
          </div>
        </div>

        <div class="form-group">
          <!-- website name -->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
         <label class="control-label"  for="shop_name">Business Type:</label>
        </div>
          <div class="controls col-lg-8">
            <select name="business_type" id="btype">
                        <option value="take away">Take Away</option>
                        <option value="eat in">Eat in</option>
                    </select>
            <br />
          </div>
        </div>

        <div class="form-group">
          <!-- website domain-->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
          <label class="control-label" for="business_email">Business Email:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="email" id="business_email" maxlength="100" name="business_email" placeholder="Business Email" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('business_email')?>">
          </div>
        </div>

        <div class="form-group">
          <!-- website url-->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
          <label class="control-label" for="paypal_id">Paypal ID:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="text" id="paypal_id" maxlength="100" name="paypal_id" placeholder="Paypal ID" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('paypal_id')?>">
          </div>
        </div>

                <div class="form-group">
          <!-- website url-->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
          <label class="control-label" for="paypal_secret">Paypal Secret:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="text" id="paypal_secret" maxlength="100" name="paypal_secret" placeholder="Paypal Secret" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('paypal_secret')?>">
          </div>
        </div>

                <div class="form-group">
          <!-- website url-->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
          <label class="control-label" for="paypal_callback">Paypal Callback Url:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="text" id="paypal_callback" maxlength="100" name="paypal_callback" placeholder="Paypal Callback" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('paypal_callback')?>">
          </div>
        </div>

                <div class="form-group">
          <!-- website url-->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
          <label class="control-label" for="cancel_url">Paypal Cancel Url:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="text" id="cancel_url" maxlength="100" name="cancel_url" placeholder="Paypal Cancel Url" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('cancel_url')?>">
          </div>
        </div>

                <div class="form-group">
          <!-- website url-->
          <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
          <label class="control-label" for="notify_url">Paypal Notify Url:</label>
        </div>
          <div class="controls col-lg-8">
            <input type="text" id="notify_url" maxlength="100" name="notify_url" placeholder="Paypal Notify Url" class="input-xlarge form-control" value="<?php echo $pos->POS_Info('notify_url')?>">
          </div>
        </div>


        <br />
         <div class="form-group">
          <!-- Button -->
          <div class="controls col-lg-offset-10 col-lg-2">
             <button id="POS_settings" class="btn btn-success">Save</button> 
          </div>
        </div>
                        <div class="results" id="sresults"></div>

      </fieldset>

  </form>
  
                       
                     

                    

                        