<form  class="form-horizontal trans-form" id="add-trans">
                      <fieldset>
                        <div id="legend">
                          <legend class="">Add Translations</legend>
                        </div>

                        <div class="col-lg-12 col-xs-12 col-md-12">
                        <div class="form-group">
                        <!-- Button -->
                        <div class="col-lg-3 col-sm-3 col-md-3">
                           <button id="add_trans_btn" onclick="WILang.AddTransModal('trans-add')" class="btn btn-success" >Add</button> 
                        </div>
                      </div>
                      <div class="results" id="transresults"></div>

       
                        
                        </div>
                       <div id="trans"><!-- content will be loaded here --></div>  
                     <!-- <?php //$web->viewTrans(); ?> -->
                 <div class="loading-div closed"><img src="WIMedia/Img/ajax-loader.gif" ></div>
                  

                    </fieldset>
                        <br /><br />
                  </form>

<?php
$modal->moduleModal('trans-edit', 'Edit Trans', 'WILang', 'EditTrans','Save',''); 
$modal->moduleModal('trans-add', 'Add Trans', 'WILang', 'Addtrans','Save',''); 
$modal->moduleModal('trans-delete', 'Delete Trans', 'WILang', 'transitemdelete','Save','');
?>
          