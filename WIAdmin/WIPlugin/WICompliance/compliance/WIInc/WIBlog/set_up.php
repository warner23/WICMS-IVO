
 <form  class="form-horizontal database-form" id="setup">
                      <fieldset>
                        <div id="legend">
                          <legend class="">Blog Set Up</legend>
                        </div>

                        <div class="col-lg-12">
                        
                        <form>
                            <fieldset>
                      <div id="legend">
                        <legend class="center">Blog Settings</legend>
                      </div>  

                      <div class="form-group">
                        <!-- website name -->
                        <div class="col-lg-4 col-xs-4 col-sm-s col-md-4">
                        <label class="control-label"  for="Blog_name">Blog Name:</label>
                      </div>
                        <div class="controls col-lg-8">
                          <input type="text" id="Blog_name"  maxlength="88" name="Blog_name" placeholder="Blog Name" class="input-xlarge form-control" value=""> <br />
                        </div>
                      </div>

                          </fieldset>
                        </form>
                        
                        </div>
                       
                         

                              <div class="form-group">
                        <!-- Button -->
                        <div class="controls col-lg-offset-4 col-lg-8">
                           <button id="add_lang_btn" onclick="WILang.AddLangModal()" class="btn btn-success" >Add</button> 
                        </div>
                      </div>
                      <div class="results" id="results"></div>
                    </fieldset>
                        <br /><br />
                  </form>

            