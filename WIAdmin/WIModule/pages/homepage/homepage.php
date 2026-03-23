<?php
#[\AllowDynamicProperties]
/**
* 
*/
class homepage
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->page = new WIPage();
        $this->mod  = new WIModules();
    }

    public function editMod()
    {
          
     echo `<div id="remove">
      <a href="javavscript:void(0);">
      <button id="delete" onclick="WIMod.delete(event);">Delete</button>
      </a>
       <div id="dialog-confirm" title="Remove Module?" class="hide">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;">
  </span>Are you sure?</p>
  <p> This will remove the module and any unsaved data.</p>
  <span><button class="btn btn-danger" onclick="WIMod.remove(event);">Remove</button> <button class="btn" onclick="WIMod.close(event);">Close</button></span>
</div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="660472" onclick="WIScript.gridEdit(`12`)" class="edit-660472"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-12 column ui-sortable"><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="289484" onclick="WIScript.gridEdit(`12`)" class="edit-289484"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-12 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000;" id="editorId">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                    <button type="button" class="btn btn-mini" role="button" id="editorModal" onclick="WIScript.Editor();">Editor</button>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Orientation<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="#" rel="">Default</a></li>
                          <li class=""><a href="#" rel="btn-group-vertical">Vertical</a></li>
                        </ul>
                      </span>
                    </span>
                    <div class="preview">Jumbotron</div>
                    <div class="view">
                      <div class="`hero-unit`" contenteditable="`true`">
                        <h1>Hello, world!</h1>
                        <p>This is a template for a simple marketing or information website.
                          It includes a large callout called the herop unit and three  supporting pieaces of content. Use iot as starting point to create something more unique
                        </p>
                        <p><a class="`btn" btn-primary="" btn-large`="" href="`javascript:void(0);`">Learn More »</a></p>
                      </div>
                    </div>
                  </div></div>
                      </div>
                    </div>
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="351734" onclick="WIScript.gridEdit(`444`)" class="edit-351734"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="4 4 4" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-4 column ui-sortable"></div>
                        <div class="col-xs-4 column ui-sortable"></div>
                        <div class="col-xs-4 column ui-sortable"></div>
                      </div>
                    </div>
                  </div></div>
                      </div>
                    </div>
                  </div></div>`;
 
    }

    public function editPageContent($page)
    {
        echo `<div class="container-fluid text-center" id="col">`; 

          $lsc = $this->page->GetColums($page, "left_sidebar");
          $rsc = $this->page->GetColums($page, "right_sidebar");
        if ($lsc > 0) {

              echo `<div class="col-sm-1 col-lg-2 col-md-2 col-xl-2 col-xs-2 sidenav" id="sidenavL">`;
         $this->mod->getMod("left_sidebar");  

            echo `</div>
            <div class="col-lg-10 col-md-8 col-sm-8 block" id="block">
            <div class="col-lg-10 col-md-8 col-sm-8" id="Mid">`;
        }

        if ($lsc && $rsc > 0) {
            echo `<div class="col-lg-10 col-md-8 col-sm-8 block" id="block"><div class="col-lg-12 col-md-8 col-sm-8" id="Mid">`;
        }else if($rsc > 0){
            echo `<div class="col-lg-10 col-md-8 col-sm-8 block" id="block"><div class="col-lg-12 col-md-8 col-sm-8" id="Mid">`;

         }else{
        echo `<div class="col-lg-12 col-md-12 col-sm-12 block" id="block"><div class="col-lg-12 col-md-12 col-sm-12" id="Mid">`;
        }
          echo `<div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="660472" onclick="WIScript.gridEdit(`12`)" class="edit-660472"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-12 column ui-sortable"><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="289484" onclick="WIScript.gridEdit(`12`)" class="edit-289484"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-12 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000;" id="editorId">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                    <button type="button" class="btn btn-mini" role="button" id="editorModal" onclick="WIScript.Editor();">Editor</button>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Orientation<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="#" rel="">Default</a></li>
                          <li class=""><a href="#" rel="btn-group-vertical">Vertical</a></li>
                        </ul>
                      </span>
                    </span>
                    <div class="preview">Jumbotron</div>
                    <div class="view">
                      <div class="`hero-unit`" contenteditable="`true`">
                        <h1>Hello, world!</h1>
                        <p>This is a template for a simple marketing or information website.
                          It includes a large callout called the herop unit and three  supporting pieaces of content. Use iot as starting point to create something more unique
                        </p>
                        <p><a class="`btn" btn-primary="" btn-large`="" href="`javascript:void(0);`">Learn More »</a></p>
                      </div>
                    </div>
                  </div></div>
                      </div>
                    </div>
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="351734" onclick="WIScript.gridEdit(`444`)" class="edit-351734"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="4 4 4" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-4 column ui-sortable"></div>
                        <div class="col-xs-4 column ui-sortable"></div>
                        <div class="col-xs-4 column ui-sortable"></div>
                      </div>
                    </div>
                  </div></div>
                      </div>
                    </div>
                  </div>`;

         if ($rsc > 0) {

              echo `</div><div class="col-sm-1 col-lg-2 cool-md-2 col-xl-2 col-xs-2 sidenav" id="sidenavR">`;
          $this->mod->getMod("right_sidebar");  

            echo `</div></div>`;
        }

        echo `</div>
            </div>`;
    }

    public function mod_name()
    {
      echo `<div class="container-fluid">
	<div class="grid row-fluid col-lg-12 col-md-12 col-sm-12">
		 <a href="javascript:void(0);" id="660472" class="edit-660472"><em class="far fa-edit"></em>Edit</a>
		<div class="rowActions groupActions hide">
			<div class="actionBtnWrapper">
				<button class="btn item_handle" type="button"></button><button class="btn item_editToggle" type="button"></button><button class="btn item_clone" type="button"></button><button class="btn item_remove" type="button"></button>
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="row-fluid">
				<div class="col-xs-12">
					<div class="grid row-fluid col-lg-12 col-md-12 col-sm-12">
						 <a href="javascript:void(0);" id="289484" class="edit-289484"><em class="far fa-edit"></em>Edit</a>
						<div class="rowActions groupActions hide">
							<div class="actionBtnWrapper">
								<button class="btn item_handle" type="button"></button><button class="btn item_editToggle" type="button"></button><button class="btn item_clone" type="button"></button><button class="btn item_remove" type="button"></button>
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="row-fluid">
								<div class="col-xs-12">
									<div class="col-lg-12 col-md-12 col-sm-12" id="editorId">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<div class="`hero-unit`">
												<h1>
													Hello, world!
												</h1>
												<p>
													This is a template for a simple marketing or information website. It includes a large callout called the herop unit and three supporting pieaces of content. Use iot as starting point to create something more unique
												</p>
												<p>
													<a class="`btn" href="`javascript:void(0);`">Learn More »</a>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="grid row-fluid col-lg-12 col-md-12 col-sm-12">
						 <a href="javascript:void(0);" id="351734" class="edit-351734"><em class="far fa-edit"></em>Edit</a>
						<div class="rowActions groupActions hide">
							<div class="actionBtnWrapper">
								<button class="btn item_handle" type="button"></button><button class="btn item_editToggle" type="button"></button><button class="btn item_clone" type="button"></button><button class="btn item_remove" type="button"></button>
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="row-fluid">
								<div class="col-xs-4">
								</div>
								<div class="col-xs-4">
								</div>
								<div class="col-xs-4">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>`;
    }
     
    
}