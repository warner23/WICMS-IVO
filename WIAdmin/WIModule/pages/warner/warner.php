<?php

/**
* 
*/
class warner
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
    }

    public function editMod()
    {
        
    $this->Web->EditModTemp();
     $result = $this->WIdb->select("SELECT `edit_page_mod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page,));
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["edit_page_mod"];
        }
 
    }

    public function editPageContent($page)
    {
      $result = $this->WIdb->select("SELECT `edit_page_mod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page,));
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["edit_page_mod"];
        }

    }

    public function mod_name($page)
    {
    $this->Boot->startMod($page);

    $this->Boot->startContentsHolder();
        
     echo 
            <div class="wicreate" style="position: relative; opacity: 1; left: 0px; top: 0px;">

              
              <a href="#close" class="remove label label-important">
                <i class="icon-remove icon-white"></i>
                remove
              </a>
              <span class="drag label ui-sortable-handle">
                <i class="icon-move"></i>
              drag
            </span>
            <span class="configuration">
                      <button type="button" class="btn btn-mini" role="button" id="editorModal" onclick="WIScript.Editor();">Editor</button>
                      <a class="btn btn-mini" href="javascript:void(0);" rel="well">Well</a> 
                    </span>
              <div class="preview">12</div>
              <div class="view">
            <div class="col-lg-12 col-md-12 col-sm-12 column ui-sortable">
              <div class="box box-element ui-draggable" style="display: block; position: relative; opacity: 1; left: 0px; top: 0px;">
              <a href="#close" class="remove label label-important">
                <i class="icon-remove icon-white"></i>
                remove
              </a>
              <span class="drag label ui-sortable-handle">
                <i class="icon-move"></i>
              drag
            </span>
            <span class="configuration">
                      <button type="button" class="btn btn-mini" data-target="#editorModal" role="button" data-toggle="modal" onclick="WIScript.Editor();">Editor</button> 
                      <a class="btn btn-mini" href="#" rel="well">Well</a> 
                    </span>

                    <div class="preview">Jumbotron</div>
                      <div class="view">
              <div class="intro_box hero-unit" contenteditable="true">
              <h1><span>Hello, world!</span></h1>
              <p>This is a template for a simple marketing or informational website.
                          It includes a large callout called the hero unit and three supporting pieces of content.
                          Use it as a starting point to create something more unique.</p>
              </div>
            </div>
          </div><div class="base box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
                           
                    
                    <div class="fieldActions groupActions fieldActions-353845 show" id="353845"><div class="ractionBtnWrapper">
                <button class="btn ritemhandle" type="button" id="rgab">
                <i class="fas fa-grip-vertical right" aria-hidden="true"></i>
                </button>
                <button class="btn item_editToggle" type="button" onclick="WIPageBuilder.editAttr(`425825`);">
                <i class="fas fa-edit" aria-hidden="true"></i>    
                </button>
                <button class="btn item_clone" type="button">
                <i class="fas fa-copy" aria-hidden="true"></i>
                </button>
                <button class="btn item_remove" type="button">
                <i class="fas fa-times" aria-hidden="true"></i> 
                </button>
            </div></div>
                    <div class="fieldEdit-353845" id="425825" style="display:none; position:relative; opacity:1; height:auto;">   <script>
  $( function() {
    $( "#2030296851" ).tabs();
  } );
  </script>

<div id="2030296851" class="ui-tabs ui-corner-all ui-widget ui-widget-content">
  <ul role="tablist" class="ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header">
    <li role="tab" tabindex="0" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" aria-controls="attributes-576796425" aria-labelledby="ui-id-39" aria-selected="true" aria-expanded="true"><a href="#attributes-576796425" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-39">Attributes</a></li>
    <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab" aria-controls="options-576796425" aria-labelledby="ui-id-40" aria-selected="false" aria-expanded="false"><a href="#options-576796425" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-40">Options</a></li>
    <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab" aria-controls="conditions-576796425" aria-labelledby="ui-id-41" aria-selected="false" aria-expanded="false"><a href="#conditions-576796425" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-41">Conditions</a></li>
  </ul>
  <div id="attributes-576796425" aria-labelledby="ui-id-39" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false">
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
  </div>
  <div id="options-576796425" aria-labelledby="ui-id-40" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;">
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
          <option selected="true">defasult</option>
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
        <button type="button" class="addOptions" id="903186577">+ Options</button>
        </div>
        </div>
  </div>
  <div id="conditions-576796425" aria-labelledby="ui-id-41" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;">
     <div class="panel conditions-panel">
          <ul class="field-edit-group">
            <li class="field-conditions">
              <div class="field-prop">
                <div class="conditions-prop-inputs">
                  <div class="f-condition-row if-condition-row condition-source condition-target condition-sourceProperty-value condition-comparison-equals condition-targetProperty-value">
                    <label class="condition-label if-condition-label">IF</label>
                    <div class="condition-source">
                      <input type="text" name="f-autocomplete-display-field">
                      <input type="hidden" name="condition-source">
                      <ul class="f-autocomplete-list">
                        
                      </ul>
                    </div>
                    <select class="condition-sourceProperty">
                      <option value="value" selected="true">value</option>
                      <option value="isVisible">is visible</option>
                      <option value="isNotVisible">is not visible</option>
                    </select>

                    <select class="condition-comparison">
                      <option value="equals" selected="true">equals</option>
                      <option value="notEquals">not equals</option>
                      <option value="contains">contains</option>
                      <option value="notContains">not contains</option>
                    </select>

                    <select class="condition-comparison">
                      <option value="equals" selected="true">equals</option>
                      <option value="notEquals">not equals</option>
                      <option value="contains">contains</option>
                      <option value="notContains">not contains</option>
                    </select>
                      <div class="conditons-target">
                        <input type="text" name="" class="f-autocomplete-display-field" placeholder="target / value" autocomplete="off">
                        <input type="hidden" name="" class="condition-target">
                        <ul class="f-autocomplete-list">
            
                      </ul>
                      </div>

                       <select class="condition-targetProperty">
                      <option value="value" selected="true">value</option>
                      <option value="isVisible">is visible</option>
                      <option value="isNotVisible">is not visible</option>
                      
                    </select>
                  </div>
                  <div class="f-condition-row then-condition-row condition-target condition-value condition-targetProperty-value condition-assignment-equals">
                    <label class="condition-label then-condition-label">Then</label>
                    <div class="condition-target">
                       <input type="text" name="" class="f-autocomplete-display-field" placeholder="target / value" autocomplete="off">
                        <input type="hidden" name="" class="condition-target">
                        <ul class="f-autocomplete-list">
                        <li class="f-autocomplete-list-item" data-label="Button">
                          Button
                          <span class="component-label-count"></span>
                        <span class="component-type">Field</span>
                        </li>
                        <li class="f-autocomplete-list-item">External User
                          <span class="component-label-count"></span>
                        <span class="component-type">External</span>
                        </li>
                      </ul>
                    </div>
                     <select class="condition-targetProperty">
                      <option value="value" selected="true">value</option>
                      <option value="isVisible">is visible</option>
                      <option value="isNotVisible">is not visible</option>
                      
                    </select>
                    <select class="condition-assignment">
                      <option value="equals">Equals</option>
                    </select>
                    <input type="text" name="" class="condition-value" placeholder="value">
                  </div>
                </div>
                  <div class="conditions-prop-controls prop-controls">
                    <button class="prop-remove prop-control" type="button">remove</button>
                  </div>
              </div>
            </li>
          </ul> 
            <div class="panel-action-buttons">
              <button class="add-conditions" title="+ Condition" type="button">Add Condition</button>
            </div>
        </div>
  </div>
</div></div><div class="preview">Paragraph</div>
                    <div class="view" contenteditable="true">
                      <p>This is the Paragraph, add your own Paragraph here</p>
                    </div>
                  </div><div class="grid box-element wicreate ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 1;">
                <input type="hidden" id="37">
                <div class="rowActions groupActions hide" id="37"><div class="lactionBtnWrapper">
                <button class="btn litemhandle" type="button" id="lgab">
                <i class="fas fa-grip-vertical left" aria-hidden="true"></i>
                </button>
                <button class="btn item_editToggle" onclick="WIPageBuilder.edit();" type="button">
                <i class="fas fa-edit" aria-hidden="true"></i>    
                </button>
                <button class="btn item_clone" onclick="WIPageBuilder.clone();" type="button">
                <i class="fas fa-copy" aria-hidden="true"></i>
                </button>
                <button class="btn item_remove" onclick="WIPageBuilder.delete();" type="button">
                <i class="fas fa-times" aria-hidden="true"></i> 
                </button>
            </div></div>
               <div class="rowEdit groupConfig"><div class="fCheck">
        <label for="inputting">
          <input name="inputting" type="checkbox" aria="label=&quot;rowSeetingsInputGroupAria&quot;" id="inputGroup">
          <span class="checkable">Repeatable Region</span>
          </label>
        </div>
        <hr>
      <div class="FFieldGroup">
      <label>Wrap row in a <fieldset> tag
      <div class="inputGroup">
      <span class="inputGroupAddon">
      <input name="checkboxX" type="checkbox" aria-label="wrap Row in Fieldset" id="fieldset">
      </span>
      <input name="legend" type="text" aria-label="Legend for fieldset" placeholder="legend" id="legend">
      </div>
      </fieldset></label></div>
      <hr>
      <label>Define Column widths</label>
      <div class="FFieldGroupNew row">
      <label class="col-sm-4 form-control-label">Layout Preset</label>
      <div class="col-sm-8">

      <span class="help-block">
          xs (for phones - screens less than 768px wide)
        sm (for tablets - screens equal to or greater than 768px wide)
        md (for small laptops - screens equal to or greater than 992px wide)
        lg (for laptops and desktops - screens equal to or greater than 1200px wide)          
                </span>

        <select name="column" aria-label="Define a column layout" class="columnPreset" id="columnPreset">
        <option value="xs-12" label="col-xs-12 (100%)" selected="true">100%</option>
        <option value="xs-10" label="col-xs-10 (90%)" selected="true">col-xs-10 (90%)</option>
        <option value="xs-8" label="col-xs-8 (80%)" selected="true">col-xs-8 (80%)</option>
        <option value="xs-7" label="col-xs-7 (65%)" selected="true">col-xs-7 (65%)</option>
        <option value="xs-6" label="col-xs-6 (50%)" selected="true">col-xs-6 (50%)</option>
        <option value="xs-5" label="col-xs-5 (40%)" selected="true">col-xs-5 (40%)</option>
        <option value="xs-4" label="col-xs-4 (30%)" selected="true">col-xs-4 (30%)</option>
        <option value="xs-3" label="col-xs-3 (15%)" selected="true">col-xs-3 (15%)</option>
        <option value="xs-2" label="col-xs-2 (10%)" selected="true">col-xs-2 (10%)</option>
        <option value="xs-1" label="col-xs-1 (5%)" selected="true">col-xs-1 (5%)</option>

        <option value="sm-12" label="col-sm-12 (100%)" selected="true">col-sm-12 (100%)</option>
        <option value="sm-10" label="col-sm-10 (90%)" selected="true">col-sm-10 (90%)</option>
        <option value="sm-8" label="col-sm-8 (80%)" selected="true">col-sm-8 (80%)</option>
        <option value="sm-7" label="col-sm-7 (65%)" selected="true">col-sm-7 (65%)</option>
        <option value="sm-6" label="col-sm-6 (50%)" selected="true">col-sm-6 (50%)</option>
        <option value="sm-5" label="col-sm-5 (40%)" selected="true">col-sm-5 (40%)</option>
        <option value="sm-4" label="col-sm-4 (30%)" selected="true">col-sm-4 (30%)</option>
        <option value="sm-3" label="col-sm-3 (15%)" selected="true">col-sm-3 (15%)</option>
        <option value="sm-2" label="col-sm-2 (10%)" selected="true">col-sm-2 (10%)</option>
        <option value="sm-1" label="col-sm-1 (5%)" selected="true">col-sm-1 (5%)</option>

        <option value="md-12" label="col-md-12 (100%)" selected="true">col-md-12 (100%)</option>
        <option value="md-10" label="col-md-10 (90%)" selected="true">col-md-10 (90%)</option>
        <option value="md-8" label="col-md-8 (80%)" selected="true">col-md-8 (80%)</option>
        <option value="md-7" label="col-md-7 (65%)" selected="true">col-md-7 (65%)</option>
        <option value="md-6" label="col-md-6 (50%)" selected="true">col-md-6 (50%)</option>
        <option value="md-5" label="col-md-5 (40%)" selected="true">col-md-5 (40%)</option>
        <option value="md-4" label="col-md-4 (30%)" selected="true">col-md-4 (30%)</option>
        <option value="md-3" label="col-md-3 (15%)" selected="true">col-md-3 (15%)</option>
        <option value="md-2" label="col-md-2 (10%)" selected="true">col-md-2 (10%)</option>
        <option value="md-1" label="col-md-1 (5%)" selected="true">col-md-1 (5%)</option>

        <option value="lg-12" label="col-lg-12 (100%)" selected="true">col-lg-12 (100%)</option>
        <option value="lg-10" label="col-lg-10 (90%)" selected="true">col-lg-10 (90%)</option>
        <option value="lg-8" label="col-lg-8 (80%)" selected="true">col-lg-8 (80%)</option>
        <option value="lg-7" label="col-lg-7 (65%)" selected="true">col-lg-7 (65%)</option>
        <option value="lg-6" label="col-lg-6 (50%)" selected="true">col-lg-6 (50%)</option>
        <option value="lg-5" label="col-lg-5 (40%)" selected="true">col-lg-5 (40%)</option>
        <option value="lg-4" label="col-lg-4 (30%)" selected="true">col-lg-4 (30%)</option>
        <option value="lg-3" label="col-lg-3 (15%)" selected="true">col-lg-3 (15%)</option>
        <option value="lg-2" label="col-lg-2 (10%)" selected="true">col-lg-2 (10%)</option>
        <option value="lg-1" label="col-lg-1 (5%)" selected="true">col-lg-1 (5%)</option>
        </select>
        </div>
      </div>
      <script>
      $(`#columnPreset`).on(`change`, function() {
            // alert( this.value );
    $("#columnPreset").val(this.value).prop("selected", "selected");                      
    })
    </script></div>
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a>
                     <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span>
                     <div class="column-actions hide" id="37"><i class="fas fa-th" aria-hidden="true"></i><div class="mactionBtnWrapper">
            <button class="btn mitemhandle" type="button" id="mgab">
            <i class="fas fa-grip-vertical middle" aria-hidden="true"></i>
            </button>
            <button class="btn item_clone" type="button">
            <i class="fas fa-copy" aria-hidden="true"></i>
            </button>
            <button class="btn item_remove" type="button">
            <i class="fas fa-times" aria-hidden="true"></i> 
            </button>
        </div></div><div class="fieldPreview">
                        <div class="preview">
                      <input value="12" type="text">
                    </div>
                    <div class="view">
                      <div class="row-fluid clearfix">
                        <div class="col-xs-12 column ui-sortable"><div class="Components box box-element ui-draggable" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; opacity: 0.35; z-index: 1000; height: auto;">
                    <a href="#close" class="remove label label-important"><i class="icon-remove icon-white"></i>Remove</a> <span class="drag label ui-draggable-handle ui-sortable-handle"><i class="icon-move"></i>Drag</span>
                    <span class="configuration">
                      <span class="btn-group">
                        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">Orientation<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li class="active"><a href="#" rel="">Defasult</a></li>
                          <li><a href="#" rel="btn-group-vertical">Vertical</a></li>
                        </ul>
                      </span>
                    </span>
                    <div class="preview">Call To Action</div>
                    <div class="view">
                    <div class="overlay">
                    <div class="ao"><button onclick="WIScript.callToAction();">Call To Action</button></div>
                    </div>
                    </div>
                    
                  </div></div>
                      </div>
                    </div></div></div>
          </div>
        </div>
        
      





              
            </div>
          

     $this->Boot->endContentsHolder();           
    
    $this->Boot->endMod($page);
    }  
}