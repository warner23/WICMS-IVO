<?php
#[\AllowDynamicProperties]
/**
* 
*/
class custIndex
{
    function __construct()
    {
        $this->WIdb  = WIdb::getInstance();
        $this->page  = new WIPage();
        $this->mod   = new WIModules();
        $this->Boot  = new WIBootStrap();
        $this->Prod  = new WIProducts();
        $this->Web   = new WIWebsite();
        $this->Boot = new WIBootstrap();
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
            <a href="javascript:void(0);" id="563773" onclick="WIScript.gridEdit('12')" class="edit-563773"><i class="far fa-edit"></i>Edit</a>
                
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
                        <div class="col-xs-12 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
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
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="511992" onclick="WIScript.gridEdit('444')" class="edit-511992"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
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
                      <div class="row-fluid clearfix">
                        <div class="col-xs-4 column ui-sortable"></div>
                        <div class="col-xs-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1; height: auto;">
                           
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <button type="button" class="btn btn-mini" data-target="#editorModal" role="button" data-toggle="modal">Editor</button>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="javascript:void(0); " rel="">Default</a></li>
                          <li class=""><a href="javascript:void(0);" rel="text-left">Left</a></li>
                          <li class=""><a href="=javascript:void(0);" rel="text-center">Center</a></li>
                          <li class=""><a href="javascript:void(0);" rel="text-right">Right</a></li>
                        </ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Emphasis <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="javascript:void(0);" rel="">Default</a></li>
                          <li class=""><a href="#" rel="emphasized">Emphasized</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized2">Emphasized 2</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized3">Emphasized 3</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized4">Emphasized 4</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized-orange">Emphasized orange</a></li>
                        </ul>
                      </span>
                    </span>
                    </div><div id="FieldId-97472" style="display:none;">
    <nav class="panel-nav">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav>
      <div class="panels" style="height:116.313px;">
            <div class="Fpanel attrsPanels">
            <div class="fPanelWrap">
            <ul class="fieldEditGroup fieldEditAttrs">
            <li class="attrsClassNameWrap propWrapper controlCount=" 1"="" id="PanelWrapers">
            <div class="propControls">
            <button type="button" class="propRemove propControls"></button>
            </div>
            <div class="propInputs">
            <div class="fieldGroup">
            <label for="className">Class</label>
            <select name="className" id="className">
                <option value="fBtnGroup">Grouped</option>
                <option value="FieldGroup">Un-Grouped</option>
                </select>
            </div>
            </div>
            </li>
            </ul>
            <div class="panelActionButtons">
            <button type="button" class="addAttrs">+ Atrribute</button>
            </div>
            </div>
            <div class="Fpanel optionsPanel">
            <div class="FpanelWrap">
                <ul class="fieldEditGroup fieldEditOptions">
                    <li class="OptionsXWrapper propWrapper controlCount_2" id="propCont">
                    <div class="propControls">
                    <button type="button" class="propOrder propControls"></button>
                    <button type="button" class="propOrder propControls"></button>
                    </div>
                    <div class="propInput FinputGroup">
                    <input name="button" type="text" value="button" placeholder="label" id="buttons">
                    <select name="button" id="buttonz">
                    <option value="button" selected="true">appearing_button</option>
                    <option value="reset">Reset</option>
                    <option value="submit">Submit</option>
                    </select>
                    <select name="options" id="optional">
                    <option selected="true">default</option>
                    <option value="primary">Primary</option>
                    <option value="error">Error</option>
                    <option value="success">Success</option>
                    <option value="warning">Warning</option>
                    </select>
                    </div>
                    </li>
                </ul>
                </div>
                <div class="panelActionButtons">
                <button type="button" class="addOptions">+ Options</button>
                </div>
                </div>
                </div>
                </div>
                </div><div class="preview">Image</div>
                    <div class="view"> 
                    <img alt="140x140" src="WIMedia/Img/placeholder.jpg" style="width: 140px;height: 140px;"> </div>
                  </div></div>
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
         $this->mod->getMod("left_sidebar" , $page);  

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
            <a href="javascript:void(0);" id="563773" onclick="WIScript.gridEdit('12')" class="edit-563773"><i class="far fa-edit"></i>Edit</a>
                
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
                        <div class="col-xs-12 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
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
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
            <a href="javascript:void(0);" id="511992" onclick="WIScript.gridEdit('444')" class="edit-511992"><i class="far fa-edit"></i>Edit</a>
                
                <div class="rowActions groupActions hide">
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
                      <div class="row-fluid clearfix">
                        <div class="col-xs-4 column ui-sortable"></div>
                        <div class="col-xs-4 column ui-sortable"><div class="box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1; height: auto;">
                           
                    <div class="optset">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <button type="button" class="btn btn-mini" data-target="#editorModal" role="button" data-toggle="modal">Editor</button>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Align <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="javascript:void(0); " rel="">Default</a></li>
                          <li class=""><a href="javascript:void(0);" rel="text-left">Left</a></li>
                          <li class=""><a href="=javascript:void(0);" rel="text-center">Center</a></li>
                          <li class=""><a href="javascript:void(0);" rel="text-right">Right</a></li>
                        </ul>
                      </span>
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Emphasis <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="javascript:void(0);" rel="">Default</a></li>
                          <li class=""><a href="#" rel="emphasized">Emphasized</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized2">Emphasized 2</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized3">Emphasized 3</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized4">Emphasized 4</a></li>
                          <li class=""><a href="javascript:void(0);" rel="emphasized-orange">Emphasized orange</a></li>
                        </ul>
                      </span>
                    </span>
                    </div><div id="FieldId-97472" style="display:none;">
    <nav class="panel-nav">
      <button class="prev-group" title="previous group" type="button" data-toggle="tooltip" data-placement="top"></button>
      <div class="panel-labels">
      <div class="options">
      <h5 class="active-tab">Attrs</h5>
      <h5>Options</h5>
      </div>
      </div>
      <button class="next-group" title="Next group" type="button" data-toggle="tooltip" data-placement="top"></button>
      </nav>
      <div class="panels" style="height:116.313px;">
            <div class="Fpanel attrsPanels">
            <div class="fPanelWrap">
            <ul class="fieldEditGroup fieldEditAttrs">
            <li class="attrsClassNameWrap propWrapper controlCount=" 1"="" id="PanelWrapers">
            <div class="propControls">
            <button type="button" class="propRemove propControls"></button>
            </div>
            <div class="propInputs">
            <div class="fieldGroup">
            <label for="className">Class</label>
            <select name="className" id="className">
                <option value="fBtnGroup">Grouped</option>
                <option value="FieldGroup">Un-Grouped</option>
                </select>
            </div>
            </div>
            </li>
            </ul>
            <div class="panelActionButtons">
            <button type="button" class="addAttrs">+ Atrribute</button>
            </div>
            </div>
            <div class="Fpanel optionsPanel">
            <div class="FpanelWrap">
                <ul class="fieldEditGroup fieldEditOptions">
                    <li class="OptionsXWrapper propWrapper controlCount_2" id="propCont">
                    <div class="propControls">
                    <button type="button" class="propOrder propControls"></button>
                    <button type="button" class="propOrder propControls"></button>
                    </div>
                    <div class="propInput FinputGroup">
                    <input name="button" type="text" value="button" placeholder="label" id="buttons">
                    <select name="button" id="buttonz">
                    <option value="button" selected="true">appearing_button</option>
                    <option value="reset">Reset</option>
                    <option value="submit">Submit</option>
                    </select>
                    <select name="options" id="optional">
                    <option selected="true">default</option>
                    <option value="primary">Primary</option>
                    <option value="error">Error</option>
                    <option value="success">Success</option>
                    <option value="warning">Warning</option>
                    </select>
                    </div>
                    </li>
                </ul>
                </div>
                <div class="panelActionButtons">
                <button type="button" class="addOptions">+ Options</button>
                </div>
                </div>
                </div>
                </div>
                </div><div class="preview">Image</div>
                    <div class="view"> 
                    <img alt="140x140" src="WIMedia/Img/placeholder.jpg" style="width: 140px;height: 140px;"> </div>
                  </div></div>
                        <div class="col-xs-4 column ui-sortable"></div>
                      </div>
                    </div>
                  </div></div>
                      </div>
                    </div>
                  </div>`;

         if ($rsc > 0) {

              echo `</div><div class="col-sm-1 col-lg-2 cool-md-2 col-xl-2 col-xs-2 sidenav" id="sidenavR">`;
          $this->mod->getMod("right_sidebar", $page);  

            echo `</div></div>`;
        }

        echo `</div>
            </div>`;
    }

    public function mod_name($page)
    {
     $this->Web->CustomerMenu();
     $this->Boot->startMod($page);
     $this->Boot->startContentsHolder();
     $this->Prod->ProductMenu();
     $this->Boot->endContentsHolder(); 
     $this->Boot->endMod($page);
    }
     
    
}