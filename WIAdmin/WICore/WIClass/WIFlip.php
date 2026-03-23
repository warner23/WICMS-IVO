<?php
#[\AllowDynamicProperties]
/**
* flipcard Class
* Created by Warner Infinity
* Author Jules Warner
*/
class WIFlip
{
	function __construct() 
	{
       $this->WIdb = WIdb::getInstance();
    }

    public function flipcard()
    {
    	if($this->user->isAdmin()  OR $this->Perm->hasPermission(WISession::get('user_id'), "2", "People", "edit" ) ){
            //user is admin

        echo '<div class="col-xs-12 col-md-12 col-lg-12">
        <div class="flipcard-' . $actor['id'] . '">
        <span class="btn editing-' . $actor['id'] . '">Edit</span>
        <span class="btn show-delete" onclick="WIShow.delete(`' . $actor['id'] . '`);">Delete</span>
        <div class="back">
            <div class="top-bit" id="' . $actor['id'] . '">
      <div class="row">
        <div class="col-xs-4">
             <figure class="post-image" id="show_Image">            
          <div id="dragandrophandler-' . $actor['id'] . '" class="dragandrophandler">Drag & Drop Files Here</div>
        <div class="img-preview-' . $actor['id'] . '" id="preview-' . $actor['id'] . '"></div>
        <div class="upload-msg" id="status"></div>
                <input type="hidden" name="supload" id="supload" class="supload" value="shows">
      </figure>

        </div>
        <div class="col-xs-6">
          <h3 class="actorsName"><blockquote>
        <p><input type="text" class="name" name="name" id="name-' . $actor['id'] . '" value="'. $actor['name'].'" placeholder="actor Name"></p>
        
      </blockquote></h3>
        </div>
      </div>
          </div>
          <div class="middle-bit-actor">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      
          <div class="crew" id="crew"></div>
                      </div>

            </div>
                    <div class="end-bit">
                    <div class="row">
                      <div class="col-xs-12">
                        <button id="submit" type="button" onclick="WIActor.editactors(`' . $actor['id'] . '`);" class="btn btn-sm btn-smooth btn-custom" data-plan="free">Save</button>
                      </div>
                    </div>
                  </div>
                  </div>

                  
        
         <div class="front text-center">
          <div class="top-bit">
      <div class="row">
        <div class="col-xs-4">
        <span class="off-grey-actor">
    <img class="img-responsive" src="WIAdmin/WIMedia/Img/person/'. $photo.'" style="border-radius: 36px;">
    </span>
        </div>
        <div class="col-xs-6">
          <h3 class="actorsName"><blockquote>
        <p>'. $actor['name'].'</p>
        
      </blockquote></h3>
      <h3 class="Birth Place"><blockquote>
        <p>' . $actor['city_birth'] .'</p>
        
      </blockquote></h3>
      <h3 class="actors D.O.B"><blockquote>
        <p>';echo self::showDates($actor['dob']); echo'</p>
        
      </blockquote></h3>
        </div>

         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="tbio">'. $actor['biography'].'</p>
                        </div>
      </div>
          </div>
          <div class="middle-bit-actor">
                    <div class="row">
                     
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                       '; echo $this->shows->ActorRelatedShows(self::showRelatedShows($actor['id'])); echo '
         
                      </div>
                    </div>
                  </div>
                  <div class="end-bit">
                    <div class="row">
                      <div class="col-xs-12">
                      </div>
                    </div>
                  </div>
                    </div>
                </div>
              </div><style>

  .editing-' . $actor['id'] . '{
      background-color: #bf6cda;
    color: white;
    perspective: 638px;
    margin-left: 10px;
    margin-top: 10px;
}
  .flipcard-' . $actor['id'] . ' {
    position: relative;
    width: 100%;
    height: auto;
    perspective: 638px;
    margin: 0px 6px 10px 0px;
}
.flipcard-' . $actor['id'] . '.flip .front {
  transform: rotateY(180deg);
}
.flipcard-' . $actor['id'] . '.flip .back {
  transform: rotateY(0deg);
}
.flipcard-' . $actor['id'] . ' .back{
  transform: rotateY(-180deg);
}
.flipcard-' . $actor['id'] . ' .front, .flipcard-' . $actor['id'] . ' .back
{
        background-color: #eae8e8;
    border: 1px solid #222;
    border-radius: 5px;
    box-shadow: 0 5px 15px rgba(0,0,0,.5);
    position: relative;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    transition: all 1s ease 0s;
    color: #a94442;
    padding: 10px 23px;
    backface-visibility: hidden;
    top: 5px;
    z-index: -1;
}
  </style>
                              <script type="text/javascript">
                              $(document).ready(function(){

                               $(".editing-' . $actor['id'] . '").on("click", function(event) {
                                                        event.stopPropagation();
                                                        $(".flipcard-' . $actor['id'] . '").toggleClass("flip");
                                                                  });

                              var obj = $("#dragandrophandler-' . $actor['id'] . '");
                              var dir = $(".supload").attr("value");
                              var ele_id = $(".img-preview-' . $actor['id'] . '").attr("id");

                            obj.on("dragenter", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                                $(this).css("border", "2px solid #0B85A1");
                            });
                            obj.on("dragover", function (e) 
                            {
                                 e.stopPropagation();
                                 e.preventDefault();
                            });
                            obj.on("drop", function (e)
                            {
                             
                                 $(this).css("border", "2px dotted #0B85A1");
                                 e.preventDefault();
                                 var files = e.originalEvent.dataTransfer.files;
                                 //We need to send dropped files to Server
                                 showCreationhandleFileUpload(files,obj, dir, ele_id);
                            });
                            $(document).on("dragenter", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                            });
                            $(document).on("dragover", function (e)
                            {
                            e.stopPropagation();
                              e.preventDefault();
                              obj.css("border", "2px dotted #0B85A1");
                            });
                            $(document).on("drop", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                            });

                            });
                            </script>';

        }else{
            //user is not admin
            echo '<div class="flipcard">
       <div class="front text-center">
          <div class="top-bit">
      <div class="row">
        <div class="col-xs-6">
        <span class="off-grey-actor">
    <img class="img-responsive thumb" src="WIAdmin/WIMedia/Img/person/'. $photo.'" >
    </span>
        </div>
        <div class="col-xs-6">
          <h3 class="actorsName"><blockquote>
        <p>'. $actor['name'].'</p>
        
      </blockquote></h3>
      <h3 class="actors_birthplace"><blockquote>
        <p>' . $actor['city_birth'] . '</p>
        
      </blockquote></h3>
      <h3 class="actors_dob"><blockquote>
        <p>'; echo self::showDates($actor['dob']); echo '</p>
        
      </blockquote></h3>
        </div>

         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="tbio">'. $actor['biography'].'</p>
                        </div>

      </div>
          </div>
          <div class="middle-bit-actor">
                    <div class="row">
                     
                       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cast" id="cast">';
                       $actors_id = self::showRelatedShows($actor['id']);
                       //var_dump($actors_id);
                        echo $this->shows->ActorRelatedShows($actors_id); echo '</div>
          <div class="crew" id="crew"></div>
                      </div>

                      
                    </div>
                  </div>
                  <div class="end-bit">
                    <div class="row">
                      <div class="col-xs-12">
                      </div>
                    </div>
                  </div>
        </div>
    </div>
                   <style>

                 
                  .flipcard{
                    position: relative;
                    width: 100%;
                    height: auto;
                    perspective: 638px;
                    margin: 0px 6px 10px 0px;
                }
                .flipcard .flip .front {
                  transform: rotateY(180deg);
                }
                .flipcard .flip .back {
                  transform: rotateY(0deg);
                }
                .flipcard .back{
                  transform: rotateY(-180deg);
                }
                .flipcard .front, .flipcard .back
                {
                    background-color: #eae8e8;
    border: 1px solid #222;
    border-radius: 5px;
    box-shadow: 0 5px 15px rgba(0,0,0,.5);
    position: relative;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    transition: all 1s ease 0s;
    color: #a94442;
    padding: 10px 23px;
    backface-visibility: hidden;
    top: 5px;
    z-index: -1;
                }
                  </style>';

        }
    }


 

}


?>