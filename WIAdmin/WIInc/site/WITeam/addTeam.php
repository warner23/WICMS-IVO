  <form class="form-horizontal" id="course">
        <fieldset>
          <div id="legend">
                <legend class="">Training</legend>
                <legend class="">This is to show the user what classes you do.</legend>
            </div>  
        <div id="cstatus"></div>


         <div class="col-sm-12 col-lg-12 col-md-12 col-xs-12">

           <div class="col-sm-12 col-lg-12 col-md-12 col-xs-12">
          <input type="hidden" name="user_id" id="user_id"value="<?php echo WISession::get('user_id'); ?>">
        </div>
            


        <div class="col-sm-6 col-lg-6 col-md-6 col-xs-6">
                          <style>

#trainingdragandrophandler{
  border: 2px dotted #0B85A1;
    color: #92AAB0;
    text-align: left;
    vertical-align: middle;
    padding: 10px 10px 10 10px;
    margin-bottom: 10px;
    font-size: 200%;
}
.progressBar {
    height: 22px;
    border: 1px solid #ddd;
    border-radius: 5px; 
    overflow: hidden;
    display:inline-block;
    margin:0px 10px 5px 5px;
    vertical-align:top;
}
 
.progressBar div {
    height: 100%;
    color: #fff;
    text-align: right;
    line-height: 22px; /* same as #progressBar height if we want text middle aligned */
    width: 0;
    background-color: #0ba1b5; border-radius: 3px; 
}
.statusbar
{
    border-top:1px solid #A9CCD1;
    min-height:25px;
    padding:10px 10px 0px 10px;
    vertical-align:top;
}
.statusbar:nth-child(odd){
    background:#EBEFF0;
}
.filename
{
display:inline-block;
vertical-align:top;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
margin-left:10px;
margin-right:5px;
}

.abort{
    background-color:#A8352F;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    border-radius:4px;display:inline-block;
    color:#fff;
    font-family:arial;font-size:13px;font-weight:normal;
    padding:4px 15px;
    cursor:pointer;
    vertical-align:top
    }

.ui-widget input {
 width: 100%!important;
}

</style>
                            <div id="content_team">
                          <img src="WIMedia/Img/training/default.png" class="img-responsive course cp">
                          </div>
                          <a href="javascript:void(0);" id="change" onclick="WIMedia.changePic('team-edit', '')"><span>Change Training Photo</span>
                          </a>
                          <br><br>
                         
                        </div>


                        <div class="col-sm-6 col-lg-6 col-md-6 col-xs-6">

                        
                      <div class="form-group">
                        <!-- Password-->
                        <label class="control-label col-lg-4" for="Description">Name:</label>
                        <div class="col-lg-8">
                          <textarea type="text" id="name" name="name" placeholder="name" class="input-xlarge form-control" value="">
                        </textarea>
                      </div>
                    </div>


                      <div class="form-group">
                        <!-- Password-->
                        <label class="control-label col-lg-4" for="Description">Job Title:</label>
                        <div class="col-lg-8">
                          <textarea type="text" id="job_title" name="job_title" placeholder="job_title" class="input-xlarge form-control" value="">
                        </textarea>
                      </div>
                    </div>

                      <div class="form-group">
                        <!-- Password-->
                        <label class="control-label col-lg-4" for="Description">Description:</label>
                        <div class="col-lg-8">
                          <textarea type="text" id="description" name="description" placeholder="description" class="input-xlarge form-control" value="">
                        </textarea>
                      </div>
                      </div>

                      <div class="form-group">
                        <!-- Password-->
                        <label class="control-label col-lg-4" for="Description">Facebook link:</label>
                        <div class="col-lg-8">
                          <textarea type="text" id="fb_link" name="fb_link" placeholder="fb_link" class="input-xlarge form-control" value="">
                        </textarea>
                      </div>
                    </div>

                    <div class="form-group">
                        <!-- Password-->
                        <label class="control-label col-lg-4" for="Description">Twitter link:</label>
                        <div class="col-lg-8">
                          <textarea type="text" id="tw_link" name="tw_link" placeholder="tw_link" class="input-xlarge form-control" value="">
                        </textarea>
                      </div>
                    </div>


                    <div class="form-group">
                        <!-- Password-->
                        <label class="control-label col-lg-4" for="Description">LinkedIn link:</label>
                        <div class="col-lg-8">
                          <textarea type="text" id="linked_link" name="linked_link" placeholder="linked_link" class="input-xlarge form-control" value="">
                        </textarea>
                      </div>
                    </div>


                 </div>

                        
                       </div>
                       

                         <a href="javascript:void(0)" class="btn" onclick="WITeam.Newteam()">Save</a>
                     </fieldset>
                      
                      </form>

<?php  
 $modal->moduleModal('team-edit', 'Change train', 'WIMedia', 'team_edit','Save',''); 
 $modal->moduleModal('team-media', 'Change Media', 'WIMedia', 'TeamPic','Save',''); 
 $modal->moduleModal('team-upload', 'Upload Media', 'WIMedia', 'UploadTeamPics','Save',''); 

?>
                    

                        