   <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

 <form  class="form-horizontal" id="floor-form">
                        <fieldset>
                        
                         <div id="legend">
                        <legend class="">Add / Edit Floor Plan</legend>
                      </div>  


                       <div class="col-sm-12 col-lg-12 col-md-12 col-xs-12">
                          <a href="javascript:void(0);" onclick="WICompliance.addFloorPlan();">
                          <div class="btn btn-sm btn-primary">
                            Add
                          </div>
                        </a>
                        <div id="getFloorPlan"> <?php $pos->FloorPlan(); ?> </div>
                       </div>
                    
                  
                      <div class="results" id="floorresults"></div>
                        </fieldset>
                      </form>


<?php
 $modal->moduleModal('floorplan-edit', 'Change favicon', 'WIMedia', 'changeFloorPlanpic','Save',''); 
 $modal->moduleModal('floorplan-media', 'Change Media', 'WIMedia', 'floorPlanPics','',''); 
 $modal->moduleModal('floorplan-upload', 'Upload Media', 'WIMedia', 'UploadFloorPlanPics','Save',''); 
?>