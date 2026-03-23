<?php

/**
* 
*/
class platform
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
            <a href="javascript:void(0);" id="476234" onclick="WIScript.gridEdit(`12`)" class="edit-476234"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                        <div class="col-lg-12 col-md-12 col-sm-12 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
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
                      <div class="hero-unit" contenteditable="true">
                        <h1>Hello, world!</h1>
                        <p>This is a template for a simple marketing or information website.
                          It includes a large callout called the herop unit and three  supporting pieaces of content. Use iot as starting point to create something more unique
                        </p>
                        <p><a class="btn btn-primary btn-large" href="javascript:void(0);">Learn More »</a></p>
                      </div>
                    </div>
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1; height: auto;">
            <a href="javascript:void(0);" id="928565" onclick="WIScript.gridEdit(`444`)" class="edit-928565"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="4 4 4" type="text">
                    </div>
                    <div class="view">
                        <div class="col-lg-4 col-md-4 col-sm-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1; height: auto;">
                 
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret">Styles</span></a>
                        <ul class="dropdown-menu"><li class="" rel="fa-6"><a href="#" rel="fa-6">Large</a></li>
                          <li class="" rel="fa-5"><a href="#" rel="fa-5">Big</a></li>
                           <li class="" rel="fa-4"><a href="#" rel="fa-4">Medium</a></li>
                           <li class="" rel="fa-3"><a href="#" rel="fa-3">Normal</a></li>
                          <li class="" rel="fa-2"><a href="#" rel="fa-2">Small</a></li>
                <li class="" rel="fa-1"><a href="#" rel="fa-1">Tiny</a></li></ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" onclick="WIScript.font_awesome()">All the Icons <span class="caret">All the Icons</span></a>
                        <ul class="dropdown-menu" id="font_awesome">
                        </ul>
                      </span>
                    </span>
                    </div>
                    <nav class="panel-nav" id="FieldId-332820" style="display:none;">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav><div class="preview">Font Awesome</div>
                    <div class="view"> 
                    <span contenteditable="true" class="fa fa-address-book fa-3" aria-hidden="true">
                    </span>                           
       </div>
                  </div></div>
                        <div class="col-lg-4 col-md-4 col-sm-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000; height: auto;">
                 
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret">Styles</span></a>
                        <ul class="dropdown-menu"><li class="" rel="fa-6"><a href="#" rel="fa-6">Large</a></li>
                          <li class="" rel="fa-5"><a href="#" rel="fa-5">Big</a></li>
                           <li class="" rel="fa-4"><a href="#" rel="fa-4">Medium</a></li>
                           <li class="" rel="fa-3"><a href="#" rel="fa-3">Normal</a></li>
                          <li class="" rel="fa-2"><a href="#" rel="fa-2">Small</a></li>
                <li class="" rel="fa-1"><a href="#" rel="fa-1">Tiny</a></li></ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" onclick="WIScript.font_awesome()">All the Icons <span class="caret">All the Icons</span></a>
                        <ul class="dropdown-menu" id="font_awesome">
                        </ul>
                      </span>
                    </span>
                    </div>
                    <nav class="panel-nav" id="FieldId-936215" style="display:none;">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav><div class="preview">Font Awesome</div>
                    <div class="view"> 
                    <span contenteditable="true" class="fa fa-address-book fa-3" aria-hidden="true">
                    </span>                           
       </div>
                  </div></div>
                        <div class="col-lg-4 col-md-4 col-sm-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000; height: auto;">
                 
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret">Styles</span></a>
                        <ul class="dropdown-menu"><li class="" rel="fa-6"><a href="#" rel="fa-6">Large</a></li>
                          <li class="" rel="fa-5"><a href="#" rel="fa-5">Big</a></li>
                           <li class="" rel="fa-4"><a href="#" rel="fa-4">Medium</a></li>
                           <li class="" rel="fa-3"><a href="#" rel="fa-3">Normal</a></li>
                          <li class="" rel="fa-2"><a href="#" rel="fa-2">Small</a></li>
                <li class="" rel="fa-1"><a href="#" rel="fa-1">Tiny</a></li></ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" onclick="WIScript.font_awesome()">All the Icons <span class="caret">All the Icons</span></a>
                        <ul class="dropdown-menu" id="font_awesome">
                        </ul>
                      </span>
                    </span>
                    </div>
                    <nav class="panel-nav" id="FieldId-764508" style="display:none;">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav><div class="preview">Font Awesome</div>
                    <div class="view"> 
                    <span contenteditable="true" class="fa fa-address-book fa-3" aria-hidden="true">
                    </span>                           
       </div>
                  </div></div>
                    </div>
                  </div></div>
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
            <a href="javascript:void(0);" id="476234" onclick="WIScript.gridEdit(`12`)" class="edit-476234"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                        <div class="col-lg-12 col-md-12 col-sm-12 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
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
                      <div class="hero-unit" contenteditable="true">
                        <h1>Hello, world!</h1>
                        <p>This is a template for a simple marketing or information website.
                          It includes a large callout called the herop unit and three  supporting pieaces of content. Use iot as starting point to create something more unique
                        </p>
                        <p><a class="btn btn-primary btn-large" href="javascript:void(0);">Learn More »</a></p>
                      </div>
                    </div>
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1; height: auto;">
            <a href="javascript:void(0);" id="928565" onclick="WIScript.gridEdit(`444`)" class="edit-928565"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions">
                    <div class="actionBtnWrapper">
                <button class="btn item_handle" type="button"></button>
                <button class="btn item_editToggle" type="button"></button>
                <button class="btn item_clone" type="button"></button>
                <button class="btn item_remove" type="button"></button>
                </div>
                </div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span><div class="preview">
                      <input value="4 4 4" type="text">
                    </div>
                    <div class="view">
                        <div class="col-lg-4 col-md-4 col-sm-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1; height: auto;">
                 
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret">Styles</span></a>
                        <ul class="dropdown-menu"><li class="" rel="fa-6"><a href="#" rel="fa-6">Large</a></li>
                          <li class="" rel="fa-5"><a href="#" rel="fa-5">Big</a></li>
                           <li class="" rel="fa-4"><a href="#" rel="fa-4">Medium</a></li>
                           <li class="" rel="fa-3"><a href="#" rel="fa-3">Normal</a></li>
                          <li class="" rel="fa-2"><a href="#" rel="fa-2">Small</a></li>
                <li class="" rel="fa-1"><a href="#" rel="fa-1">Tiny</a></li></ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" onclick="WIScript.font_awesome()">All the Icons <span class="caret">All the Icons</span></a>
                        <ul class="dropdown-menu" id="font_awesome">
                        </ul>
                      </span>
                    </span>
                    </div>
                    <nav class="panel-nav" id="FieldId-332820" style="display:none;">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav><div class="preview">Font Awesome</div>
                    <div class="view"> 
                    <span contenteditable="true" class="fa fa-address-book fa-3" aria-hidden="true">
                    </span>                           
       </div>
                  </div></div>
                        <div class="col-lg-4 col-md-4 col-sm-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000; height: auto;">
                 
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret">Styles</span></a>
                        <ul class="dropdown-menu"><li class="" rel="fa-6"><a href="#" rel="fa-6">Large</a></li>
                          <li class="" rel="fa-5"><a href="#" rel="fa-5">Big</a></li>
                           <li class="" rel="fa-4"><a href="#" rel="fa-4">Medium</a></li>
                           <li class="" rel="fa-3"><a href="#" rel="fa-3">Normal</a></li>
                          <li class="" rel="fa-2"><a href="#" rel="fa-2">Small</a></li>
                <li class="" rel="fa-1"><a href="#" rel="fa-1">Tiny</a></li></ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" onclick="WIScript.font_awesome()">All the Icons <span class="caret">All the Icons</span></a>
                        <ul class="dropdown-menu" id="font_awesome">
                        </ul>
                      </span>
                    </span>
                    </div>
                    <nav class="panel-nav" id="FieldId-936215" style="display:none;">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav><div class="preview">Font Awesome</div>
                    <div class="view"> 
                    <span contenteditable="true" class="fa fa-address-book fa-3" aria-hidden="true">
                    </span>                           
       </div>
                  </div></div>
                        <div class="col-lg-4 col-md-4 col-sm-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000; height: auto;">
                 
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret">Styles</span></a>
                        <ul class="dropdown-menu"><li class="" rel="fa-6"><a href="#" rel="fa-6">Large</a></li>
                          <li class="" rel="fa-5"><a href="#" rel="fa-5">Big</a></li>
                           <li class="" rel="fa-4"><a href="#" rel="fa-4">Medium</a></li>
                           <li class="" rel="fa-3"><a href="#" rel="fa-3">Normal</a></li>
                          <li class="" rel="fa-2"><a href="#" rel="fa-2">Small</a></li>
                <li class="" rel="fa-1"><a href="#" rel="fa-1">Tiny</a></li></ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" onclick="WIScript.font_awesome()">All the Icons <span class="caret">All the Icons</span></a>
                        <ul class="dropdown-menu" id="font_awesome">
                        </ul>
                      </span>
                    </span>
                    </div>
                    <nav class="panel-nav" id="FieldId-764508" style="display:none;">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav><div class="preview">Font Awesome</div>
                    <div class="view"> 
                    <span contenteditable="true" class="fa fa-address-book fa-3" aria-hidden="true">
                    </span>                           
       </div>
                  </div></div>
                    </div>
                  </div></div>
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
		 <a href="javascript:void(0);" id="476234" class="edit-476234"><em class="far fa-edit"></em>Edit</a>
		<div class="rowActions groupActions">
			<div class="actionBtnWrapper">
				<button class="btn item_handle" type="button"></button><button class="btn item_editToggle" type="button"></button><button class="btn item_clone" type="button"></button><button class="btn item_remove" type="button"></button>
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="col-lg-12 col-md-12 col-sm-12 column">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="hero-unit">
							<h1>
								Hello, world!
							</h1>
							<p>
								This is a template for a simple marketing or information website. It includes a large callout called the herop unit and three supporting pieaces of content. Use iot as starting point to create something more unique
							</p>
							<p>
								<a class="btn btn-primary btn-large" href="javascript:void(0);">Learn More »</a>
							</p>
						</div>
					</div>
				</div>
				<div class="grid row-fluid col-lg-12 col-md-12 col-sm-12">
					 <a href="javascript:void(0);" id="928565" class="edit-928565"><em class="far fa-edit"></em>Edit</a>
					<div class="rowActions groupActions">
						<div class="actionBtnWrapper">
							<button class="btn item_handle" type="button"></button><button class="btn item_editToggle" type="button"></button><button class="btn item_clone" type="button"></button><button class="btn item_remove" type="button"></button>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="col-lg-4 col-md-4 col-sm-4 column">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<span class="fa fa-address-book fa-3" aria-hidden="true"></span>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 column">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div id="gridbase">
									<a href="javascript:void(0);" id="936215" class="fieldEdit-936215"><em class="far fa-edit"></em>edit div</a>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12">
									<span class="fa fa-address-book fa-3" aria-hidden="true"></span>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 column">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div id="gridbase">
									<a href="javascript:void(0);" id="764508" class="fieldEdit-764508"><em class="far fa-edit"></em>edit div</a>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12">
									<span class="fa fa-address-book fa-3" aria-hidden="true"></span>
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