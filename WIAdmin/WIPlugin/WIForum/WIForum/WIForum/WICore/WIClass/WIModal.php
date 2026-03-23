<?php

/**
* 
*/
class WIModal 
{

		function __construct()
	{
		$this->WIdb = WIdb::getInstance();
    $this->site = new WISite();
    $this->page = new WIPage();
    $this->forum = new WIForum();
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

	public function moduleModal($ele_id, $title, $action, $function, $button)
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
       <div align="center" class="ajax-loading hide"><img src="WIAdmin/WIMedia/Img/ajax_loader.gif" /></div>
      <div class="modal-footer">';
       self::footer($button, $action, $function);
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

	public function footer($button, $action, $function)
	{
    if($button == ""){

    }else{
      echo ' <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="'.$action.'.'.$function.'()">'.$button.'.</button>';
    }

	}



    public function ForumCategory()
  {
    echo '<form class="form-horizontal" id="add-cat-modal">
                      <div class="form-group">
                      <input id="css_id" name="css" type="hidden" class="input-xlarge form-control" >
                        <label class="control-label col-lg-3" for="css">New Category</label>
                        <div class="col-lg-12">
                          <input id="cat_name" name="cat" type="text" class="input-xlarge form-control" >
                      </div>
                      </div>
                  </form>';
  }

  public function ForumSection()
  {
    echo '<form class="form-horizontal" id="add-section-modal">
                      <div class="form-group">

                      <input id="cat_id" name="css" type="hidden" class="input-xlarge form-control" >
                      <div class="col-lg-12">
                        <label class="control-label col-lg-3" for="css">New Section</label>
                        <div class="col-lg-12">
                          <input id="section_name" name="section" type="text" class="input-xlarge form-control" >
                          </div>
                      </div>

                      <div class="col-lg-12">
                        <label class="control-label col-lg-3" for="css">Select a Category</label>
                        <div class="col-lg-12">
                          <select id="category_selector">';
                          $this->forum->category_selector();
                         echo '</select>
                          </div>
                      </div>

                      </div>
                  </form>';
  }

  public function ForumEditCategory()
  {
    echo '<form class="form-horizontal" id="add-section-modal">
                      <div class="form-group">
                      <input id="cat_id" name="css" type="hidden" class="input-xlarge form-control" >
                        <label class="control-label col-lg-3" for="css">New Section</label>
                        <div class="col-lg-12">
                          <input id="section_name" name="section" type="text" class="input-xlarge form-control" >
                      </div>
                      </div>
                  </form>';
  }

  public function ForumEditSection()
  {
    echo '<form class="form-horizontal" id="add-section-modal">
                      <div class="form-group">
                      <input id="cat_id" name="css" type="hidden" class="input-xlarge form-control" >
                        <label class="control-label col-lg-3" for="css">New Section</label>
                        <div class="col-lg-12">
                          <input id="section_name" name="section" type="text" class="input-xlarge form-control" >
                      </div>
                      </div>
                  </form>';
  }
  
}

?>