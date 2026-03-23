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
    $this->Edit = new WIEditor();
    $this->Img  = new WIImage();
    $this->site = new WISite(); 
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


	public function moduleModal($ele_id, $title, $action, $function, $button, $footer_b)
	{
		echo '<!-- Modal -->
<div class="modal hide" id="modal-'.$ele_id.'-details" tabindex="-1" role="dialog" aria-labelledby="WIModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">';
        self::header($action, $ele_id, $title);
        echo '</div>
      <div class="modal-body">
      <div id="details-body">';
        self::$function();
      echo '</div>
      </div>
       <div align="center" class="ajax-loading hide"><img src="WIMedia/Img/ajax_loader.gif" /></div>
      <div class="modal-footer">';
       self::footer($button, $action, $function,$footer_b);
      echo '</div>
    </div>
  </div>
</div>';
	}

    public function multiModuleModal($ele_id, $title, $action, $function, $button, $footer_b, $id)
  {
    echo '<!-- Modal -->
<div class="modal hide" id="modal-'.$ele_id.'-details" tabindex="-1" role="dialog" aria-labelledby="WIModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">';
        self::header($action, $ele_id, $title);
        echo '</div>
      <div class="modal-body">
      <div id="details-body">';
        self::$function($id);
      echo '</div>
      </div>
       <div align="center" class="ajax-loading hide"><img src="WIMedia/Img/ajax_loader.gif" /></div>
      <div class="modal-footer">';
       self::multifooter($button, $action, $function,$footer_b, $id);
      echo '</div>
    </div>
  </div>
</div>';
  }

    public function moduleInstallerModal($ele_id, $title, $action, $function, $button, $footer_b)
  {
    echo '<!-- Modal -->
<div class="modal hide" id="modal-'.$ele_id.'-details" tabindex="-1" role="dialog" aria-labelledby="WIModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">';
        self::headerInstaller($action, $ele_id, $title);
        echo '</div>
      <div class="modal-body">
      <div id="details-body">';
        self::$function();
      echo '</div>
      </div>
       <div align="center" class="ajax-loading hide"><img src="WIMedia/Img/ajax_loader.gif" /></div>
      <div class="modal-footer">';
       self::Installerfooter($button, $action, $function,$footer_b);
      echo '</div>
    </div>
  </div>
</div>';
  }

	public function delete()
	{
		echo '<div class="delete_id" id=""><p>Are you sure you want to delete</p></div> ';
	}



	public function header($action, $ele_id, $title)
	{
		echo '<h5 class="modal-title" id="WIModalLabel">' .$title .'</h5>
        <button type="button" class="close" onclick="'.$action.'.closed(`'.$ele_id.'`)" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>';
	}

    public function headerInstaller($action, $ele_id, $title)
  {
    echo '<h5 class="modal-title" id="' .$ele_id .'">' .$title .'</h5>
        <button type="button" class="close" onclick="'.$action.'.closed(`'.$ele_id.'`)" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>';
  }

	public function footer($button, $action, $function, $footer_b)
	{

    if($button == "Next"){
      echo ' <button type="button" class="btn btn-primary close" data-dismiss="modal">Previous</button>
        <button id="'.$footer_b.'" type="button" class="btn btn-secondary" onclick="'.$action.'.'.$function.'()">'.$button.'.</button>';
    }elseif($button == ""){

	  }else{
     echo ' <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
        <button id="'.$footer_b.'" type="button" class="btn btn-primary" onclick="'.$action.'.'.$function.'()">'.$button.'.</button>';
    }



  }

    public function multifooter($button, $action, $function, $footer_b, $id)
  {

    if($button == "Next"){
      echo ' <button type="button" class="btn btn-primary close" data-dismiss="modal">Previous</button>
        <button id="'.$footer_b.'" type="button" class="btn btn-secondary" onclick="'.$action.'.'.$function.'(`'.$id.'`)">'.$button.'.</button>';
    }elseif($button == ""){

    }else{
     echo ' <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
        <button id="'.$footer_b.'" type="button" class="btn btn-primary" onclick="'.$action.'.'.$function.'()">'.$button.'.</button>';
    }



  }

      public function Installerfooter($button, $action, $function, $footer_b)
  {

    if($button == "Next"){
      echo ' <button type="button" class="btn btn-primary close" data-dismiss="modal">Previous</button>
        <button id="'.$footer_b.'" type="button" class="btn btn-secondary" onclick="'.$action.'.'.$function.'()">'.$button.'.</button>';
    }elseif($button == ""){

    }else{
     echo ' <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
        <button id="'.$footer_b.'" type="button" class="btn btn-primary" onclick="'.$action.'.'.$function.'()">'.$button.'.</button>';
    }



  }


  public function editing()
  {
    echo ' <form class="form-horizontal" id="add_trans">

                    
                      <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="lang_name"> `WILang::get("trans_lang") `</label>
                        <div class="controls col-lg-9">
                          <input id="lang_name" name="lang_name" type="text" class="input-xlarge form-control" >
                        </div>
                      </div>

                      <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="keyword">`WILang::get("lang_keyword") `</label>
                        <div class="controls col-lg-9">
                          <input id="keyword" name="keyword" type="text" class="input-xlarge form-control" >
                        </div>
                      </div>

                      <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="translation">`WILang::get("lang_trans")` </label>
                        <div class="controls col-lg-9">
                          <input id="translation" name="translation" type="text" class="input-xlarge form-control" >
                        </div>                      </div>
                  </form>';
  }

  public function addingLang()
  {
    echo '<form class="form-horizontal" id="add_trans">
                      <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="lang_name">
                        `WILang::get("trans_lang") `
                        </label>
                        <div class="controls col-lg-9">
                          <input id="lang_namep" name="lang_name" type="text" class="input-xlarge form-control" >
                        </div>
                      </div>

                      <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="keyword">
                         ` WILang::get("lang_keyword") `
                        </label>
                        <div class="controls col-lg-9">
                          <input id="keywordp" name="keyword" type="text" class="input-xlarge form-control" >
                        </div>
                      </div>

                      <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="translation">
                         ` WILang::get("lang_trans") `
                        </label>
                        <div class="controls col-lg-9">
                          <textarea id="translationp" name="translation" type="text" class="input-xlarge form-control" ></textarea>
                        </div>                      </div>

                     
                  </form>';
  }

  public function add($title)
  {
        echo ' <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newItem" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <select id="menuSection">';
                     self::kitchenSection();
                           // <option value=""></option>
                     echo '</select>


                  </div>
                  <div class="panel-body">
                     <div id="card" class="row" id="">
                        <div class="col-md-6 headshot" id="newmenuItemPic">';
                        echo '<img class="profile" src="WIMedia/Img/menu/default.jpg" width="218px" /><a href="javascript:void(0);" onclick="WIKitchen.addphoto()" class="btn pic" style="margin-top: -241px;margin-left: 43px;">' . WILang::get("change_pic") . '</a><div id="status"></div>
                        </div>
                       
                        <div class="col-md-6">
        <ul style="padding: 0px;margin-left: -41px;" id="ingr">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addIngred();">Add New ingrediant</a></div>
            <li class="ingred">
           <div class="col-lg-12">
            <input class="ingredient" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>
        <ul style="padding: 0px;margin-left: -41px;margin-top: 214px;overflow: scroll;width: 100%;height: 246px;" id="method">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addMethod();">Add New method</a></div>
            <li>
            <div class="col-lg-12">
            <input class="method" type="text" name="method" placeholder="Place eample into example">
            </div>
            </li>
         </ul>
         <form>';
                     echo'</div>
                     <a href="javascript:void(0);" onclick="WIKitchen.newMenuItem();">Save</a>
                  </div>
               </div>
             </div>';
  }


   public function addBuff($title)
  {

      echo '<script>
      $( function() {

    var index = "key";
    //  Define friendly data store name
    var dataStore = window.sessionStorage;
    //  Start magic!
    try {
        // getter: Fetch previous value
        var oldIndex = dataStore.getItem(index);
    } catch(e) {
        // getter: Always default to first tab in error state
        var oldIndex = 0;
    }

    $( "#bufftabs" ).tabs({
        // The zero-based index of the panel that is active (open)
        active : oldIndex,
        // Triggered after a tab has been activated
        activate : function( event, ui ){
            //  Get future value
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            //  Set future value
            dataStore.setItem( index, newIndex ) 
        }
    })



   

   });

 $( "#bufftabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  </script>
  
</head>
<body>
 
<div id="bufftabs">
  <ul>
    <li><a href="#tabs-1">Nunc tincidunt</a></li><a href="javascript:void"><img src="#"></a>
  </ul>
  <div id="tabs-1">
              <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newItem" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <select id="menuSection">';
                     self::kitchenSection();
                           // <option value=""></option>
                     echo '</select>


                  </div>
                  <div class="panel-body">
                     <div id="card" class="row" id="">
                        <div class="col-md-6 headshot" id="newmenuItemPic">';
                        echo '<img class="profile" src="WIMedia/Img/menu/default.jpg" width="218px" /><a href="javascript:void(0);" onclick="WIKitchen.addphoto()" class="btn pic" style="margin-top: -241px;margin-left: 43px;">' . WILang::get("change_pic") . '</a><div id="status"></div>
                        </div>
                       
                        <div class="col-md-6">
        <ul style="padding: 0px;margin-left: -41px;" id="ingr">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addIngred();">Add New ingrediant</a></div>
            <li class="ingred">
           <div class="col-lg-12">
            <input class="ingredient" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>
        <ul style="padding: 0px;margin-left: -41px;margin-top: 214px;overflow: scroll;width: 100%;height: 246px;" id="method">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addMethod();">Add New method</a></div>
            <li>
            <div class="col-lg-12">
            <input class="method" type="text" name="method" placeholder="Place eample into example">
            </div>
            </li>
         </ul>
         <form>
         </div>
                     <a href="javascript:void(0);" onclick="WIKitchen.newMenuItem();">Save</a>
                  </div>
               </div>
             </div>
  </div>
</div>';
  }


    public function adddrink($title)
  {
        echo ' <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newItem" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <select id="menuSection">';
                     self::barSection();
                           // <option value=""></option>
                     echo '</select>


                  </div>
                  <div class="panel-body">
                  <div class="row">
                   <input class="glass" type="text" id="glass" name="glass" placeholder="glass">
                  </div>
                  <div id="sos_time" class="row">
                   <input class="sos_time" type="text" name="sos_time" id="time" placeholder="SOS TIME">
                  </div>
                     <div id="card" class="row">
                        <div class="col-md-6 headshot" id="newmenuItemPic">';
                        echo '<img class="profile" src="WIMedia/Img/menu/drinks/default.jpg" width="218px" /><a href="javascript:void(0);" onclick="WIBar.addphoto()" class="btn pic" style="margin-top: -241px;margin-left: 43px;">' . WILang::get("change_pic") . '</a><div id="status"></div>
                        </div>
                       
                        <div class="col-md-6">
        <ul style="padding: 0px;margin-left: -41px;" id="ingr">
         <div><a href="javascript:void(0);" onclick="WIBar.addIngred();">Add New ingrediant</a></div>
            <li class="ingred">
           <div class="col-lg-12">
            <input class="ingredient" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>
        <ul style="padding: 0px;margin-left: -41px;margin-top: 214px;overflow: scroll;width: 100%;height: 246px;" id="method">
         <div><a href="javascript:void(0);" onclick="WIBar.addMethod();">Add New method</a></div>
            <li>
            <div class="col-lg-12">
            <input class="method" type="text" name="method" placeholder="Place eample into example">
            </div>
            </li>
         </ul>
         <form>';
                     echo'</div>
                     <a href="javascript:void(0);" onclick="WIBar.newMenuItem();">Save</a>
                  </div>
               </div>
             </div>';
  }

    public function photo($title)
  {
       echo '<form class="form-horizontal" id="add_photo" enctype="multipart/form-data" method="POST">
          <style>
#menudragandrophandler
{
border:2px dotted #0B85A1;
width:400px;
color:#92AAB0;
text-align:left;vertical-align:middle;
padding:10px 10px 10 10px;
margin-bottom:10px;
font-size:200%;
}
.progressBar {
    width: 200px;
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
    width:700px;
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
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
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
</style>

<div id="menudragandrophandler">Drag & Drop Files Here</div><div id="menuupload" value="addmenu"></div>
<div id="newMenuItem"></div>
<br><br>
<div id="status1"></div>

                      <hr />
        <!-- Show the images preview here -->
        <div id="upload-preview"></div>

                  </form>';
  }

  public function change()
  {
    echo '<form class="form-horizontal" id="change_photo" enctype="multipart/form-data" method="POST">
          <style>
#dragandrophandler
{
border:2px dotted #0B85A1;
width:400px;
color:#92AAB0;
text-align:left;vertical-align:middle;
padding:10px 10px 10 10px;
margin-bottom:10px;
font-size:200%;
}
.progressBar {
    width: 200px;
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
    width:700px;
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
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
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
</style>


 
<div id="dragandrophandler">Drag & Drop Files Here</div><div id="supload" value="menu"></div>
<div id="menuItem"></div>
<br><br>
<div id="status1"></div>

                      <hr />
        <!-- Show the images preview here -->
        <div id="upload-preview"></div>

                  </form>';
  }



   public function drink_photo($title)
  {
       echo '<form class="form-horizontal" id="add_photo" enctype="multipart/form-data" method="POST">
          <style>
#menudragandrophandler
{
border:2px dotted #0B85A1;
width:400px;
color:#92AAB0;
text-align:left;vertical-align:middle;
padding:10px 10px 10 10px;
margin-bottom:10px;
font-size:200%;
}
.progressBar {
    width: 200px;
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
    width:700px;
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
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
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
</style>

<div id="menudragandrophandler">Drag & Drop Files Here</div><div id="menuupload" value="adddrink"></div>
<div id="newdrinkItem"></div>
<br><br>
<div id="status1"></div>

                      <hr />
        <!-- Show the images preview here -->
        <div id="upload-preview"></div>

                  </form>';
  }

  public function change_drink()
  {
    echo '<form class="form-horizontal" id="change_photo" enctype="multipart/form-data" method="POST">
          <style>
#dragandrophandler
{
border:2px dotted #0B85A1;
width:400px;
color:#92AAB0;
text-align:left;vertical-align:middle;
padding:10px 10px 10 10px;
margin-bottom:10px;
font-size:200%;
}
.progressBar {
    width: 200px;
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
    width:700px;
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
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
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
</style>


 
<div id="dragandrophandler">Drag & Drop Files Here</div><div id="supload" value="drink"></div>
<div id="menuItem"></div>
<br><br>
<div id="status1"></div>

                      <hr />
        <!-- Show the images preview here -->
        <div id="upload-preview"></div>

                  </form>';
  }




  public function kitchenSection()
  {
    $result = $this->WIdb->select("SELECT * FROM `wi_kitchen_section`");

    foreach ($result as $res) {
      echo '<option value="' .$res['id'] . '">' .$res['name'] . '</option>';
    }
  }


    public function barSection()
  {
    $result = $this->WIdb->select("SELECT * FROM `wi_bar_section`");

    foreach ($result as $res) {
      echo '<option value="' .$res['id'] . '">' .$res['name'] . '</option>';
    }
  }

  public function editComp()
  {
    echo '<div id="editComp"></div>';
  }


}

?>