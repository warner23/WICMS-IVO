<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIModal
| File: WIModal.php
| Location: /WIAdmin/WICore/WIClass/WIModal.php
| Type: Modal UI Helper
| Layer: Admin UI
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Shared modal renderer for admin UI elements.
|
| Notes:
| - UI helper only
| - No business logic
| - Uses WIdb only for simple option lists
| - Keeps legacy modal method names for compatibility
|--------------------------------------------------------------------------
*/

class WIModal 
{

      protected WIdb $WIdb;
      protected ?WIEditor $Edit = null;
      protected ?WIImage $Img = null;
      protected ?WISite $site = null;
      protected ?WIPage $page = null;
      protected ?object $comp = null;
      protected ?object $forum = null;
      protected ?object $pos = null;
      protected ?WIHR $Hr = null;
      protected ?WIBuilder $builder = null;

        function __construct()
    {
        $this->WIdb = WIdb::getInstance();

    $this->Edit = class_exists('WIEditor') ? new WIEditor() : null;
    $this->Img  = class_exists('WIImage') ? new WIImage() : null;
    $this->site = class_exists('WISite') ? new WISite() : null;
    $this->page = class_exists('WIPage') ? new WIPage() : null;

    $this->Hr = class_exists('WIHR') ? new WIHR() : null;
    $this->builder = class_exists('WIBuilder') ? new WIBuilder() : null;

    if (class_exists('WIForum')) {
        $this->forum = new WIForum();
    }

    if (class_exists('WIPos')) {
        $this->pos = new WIPos();
    }

    if (class_exists('WICompliance')) {
        $this->comp = new WICompliance();
    }

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
<div class="modal hide" id="modal-'.$ele_id.'-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">';
        self::header($action, $ele_id, $title);
        echo '</button>
      </div>
      <div class="modal-body" style="max-height:500px; overflow-y:auto;">
      <div id="details-body">';
        self::$function($action,$title,$ele_id);
      echo '</div></div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>';
    }

    public function delete($action,$title,$ele_id)
    {
        echo '<div class="deleting" id="deleting"> <p>Are you sure you want to delete this bar item </p> <button onclick="'.$action.'.'.$title.'()">YES</button> <button onclick="'.$action.'.Closed(`'.$ele_id.'`)">NO </button></div>';
    }

    public function header($action, $ele_id, $title)
    {
        echo '<h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
        <button type="button" class="close " onclick="'.$action.'.closed(`'.$ele_id.'`)" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>';
    }

    public function footer($button)
    {
        echo ' <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">'.$button.'.</button>';
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


  public function addQuestion($title)
  {
        echo '<style>
        .inputters{
          color:black;
        }
        </style> 
        <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title">' .$title .'</h2>
                  </div>

                  <div class="panel-body">
                     <div id="card" class="row" id="">

                       <div class="container-fluid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="panel-title"><input id="inputQuestion" type="text" name="question" placeholder="Input your question here ..."></h2>
                
            </div>
            <div class="modal-body">
                <div class="col-xs-3 5"> </div>
                <div class="quiz" id="quiz" data-toggle="buttons"> 
                <label class="element-animation1 btn btn-lg btn-danger btn-block">
                    <span class="btn-label">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </span> 
                  <input class="inputters" type="text" name="q_answer" id="1" placeholder="Place option 1 here ..."> 

                  </label> 

                  <label class="element-animation2 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="2" placeholder="Place option 2 here ...">

                  </label> 

                  <label class="element-animation3 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="3" placeholder="Place option 3 here ...">

                  </label> 

                  <label class="element-animation4 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="4" placeholder="Place last option here ..."> 

                  </label> 

                   <label class="element-animationAnswer btn btn-lg btn-primary btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="answer" id="answer" placeholder="Place answer here ..."> 

                  </label> 
                  </div>
                  <a href="javascript:void(0);" onclick="WIQuiz.SaveQuestion();">Save</a>
                  <div id="msg"></div>
            </div>
        </div>
    </div>
</div>
                       
                     
        
                    </div>
                  </div>
               </div>
             </div>';
  }


  public function addkitchenqandaQuestion($title)
  {
        echo '<style>
        .inputters{
          color:black;
        }
        </style> 
        <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title">' .$title .'</h2>
                  </div>

                  <div class="panel-body">
                     <div id="card" class="row" id="">

                       <div class="container-fluid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="panel-title"><input id="inputqandaQuestion" type="text" name="question" placeholder="Input your question here ..."></h2>
                
            </div>
            <div class="modal-body">
                <div class="col-xs-3 5"> </div>
                <div class="quiz" id="quiz" data-toggle="buttons"> 
                <label class="element-animation1 btn btn-lg btn-danger btn-block">
                    <span class="btn-label">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </span> 
                  <input class="inputters" type="text" name="q_answer" id="1" placeholder="Place option 1 here ..."> 

                  </label> 

                  <label class="element-animation2 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="2" placeholder="Place option 2 here ...">

                  </label> 

                  <label class="element-animation3 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="3" placeholder="Place option 3 here ...">

                  </label> 

                  <label class="element-animation4 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="4" placeholder="Place last option here ..."> 

                  </label> 

                   <label class="element-animationAnswer btn btn-lg btn-primary btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="answer" id="qandaanswer" placeholder="Place answer here ..."> 

                  </label> 
                  </div>
                  <a href="javascript:void(0);" onclick="WIQuiz.SaveQandAQuestion();">Save</a>
                  <div id="msg"></div>
            </div>
        </div>
    </div>
</div>
                       
                     
        
                    </div>
                  </div>
               </div>
             </div>';
  }

  public function addKitchentofQuestion($title)
  {
        echo '<style>
        .inputters{
          color:black;
        }
        </style> 
        <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title">' .$title .'</h2>
                  </div>

                  <div class="panel-body">
                     <div id="card" class="row" id="">

                       <div class="container-fluid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="panel-title"><input id="inputtofQuestion" type="text" name="question" placeholder="Input your question here ..."></h2>
                
            </div>
            <div class="modal-body">
                <div class="col-xs-3 5"> </div>
                <div class="quiz" id="quiz" data-toggle="buttons"> 
               
                   <label class="element-animationAnswer btn btn-lg btn-primary btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="answer" id="tofanswer" placeholder="Place answer here ..."> 

                  </label> 
                  </div>
                  <a href="javascript:void(0);" onclick="WIQuiz.SavetofQuestion();">Save</a>
                  <div id="msg"></div>
            </div>
        </div>
    </div>
</div>
                       
                     
        
                    </div>
                  </div>
               </div>
             </div>';
  }


    public function addkitchenmcQuestion($title)
  {
        echo '<style>
        .inputters{
          color:black;
        }
        </style> 
        <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title">' .$title .'</h2>
                  </div>

                  <div class="panel-body">
                     <div id="card" class="row" id="">

                       <div class="container-fluid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="panel-title"><input id="inputmcQuestion" type="text" name="question" placeholder="Input your question here ..."></h2>
                
            </div>
            <div class="modal-body">
                <div class="col-xs-3 5"> </div>
                <div class="quiz" id="quiz" data-toggle="buttons"> 
                
                  <label class="element-animation1 btn btn-lg btn-danger btn-block">
                    <span class="btn-label">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </span> 
                  <input class="inputters" type="text" name="q_answer" id="5" placeholder="Place option 1 here ..."> 

                  </label> 

                  <label class="element-animation2 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="6" placeholder="Place option 2 here ...">

                  </label> 

                  <label class="element-animation3 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="7" placeholder="Place option 3 here ...">

                  </label> 

                  <label class="element-animation4 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="8" placeholder="Place last option here ..."> 

                  </label> 

                   <label class="element-animationAnswer btn btn-lg btn-primary btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="answer" id="mcanswer" placeholder="Place answer here ..."> 

                  </label> 
                  </div>
                  <a href="javascript:void(0);" onclick="WIQuiz.SavemcQuestion();">Save</a>
                  <div id="msg"></div>
            </div>
        </div>
    </div>
</div>
                       
                     
        
                    </div>
                  </div>
               </div>
             </div>';
  }


  public function addkitchenntfQuestion($title)
  {
        echo '<style>
        .inputters{
          color:black;
        }
        </style> 
        <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title">' .$title .'</h2>
                  </div>

                  <div class="panel-body">
                     <div id="card" class="row" id="">

                       <div class="container-fluid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="panel-title"><input id="inputntfQuestion" type="text" name="question" placeholder="Input your question here ..."></h2>
                
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="kitvhenImg">
                 <img class="img-responsive cp" id="kitchenPic" src="WIMedia/Img/kitchen/ntf/default.jpg" style="width:120px; height:120px;">
                    <button class="btn mediaPic" onclick="WIMedia.changePic(`kitchen-edit`)">Change Picture</button>
                    </div>
            <div class="modal-body">
                <div class="col-xs-3 5"> </div>
                
                <div class="quiz" id="quiz" data-toggle="buttons"> 
              <label class="element-animation1 btn btn-lg btn-danger btn-block">
                    <span class="btn-label">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </span> 
                  <input class="inputters" type="text" name="q_answer" id="1" placeholder="Place option 1 here ..."> 

                  </label> 

                  <label class="element-animation2 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="2" placeholder="Place option 2 here ...">

                  </label> 

                  <label class="element-animation3 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="3" placeholder="Place option 3 here ...">

                  </label> 

                  <label class="element-animation4 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="4" placeholder="Place last option here ..."> 

                  </label> 

                   <label class="element-animationAnswer btn btn-lg btn-primary btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="answer" id="ntfanswer" placeholder="Place answer here ..."> 

                  </label> 
                  </div>
                  <a href="javascript:void(0);" onclick="WIQuiz.SaventfQuestion();">Save</a>
                  <div id="msg"></div>
            </div>
        </div>
    </div>
</div>
                       
                     
        
                    </div>
                  </div>
               </div>
             </div>';
  }
  public function addBarQuestion($title)
  {
        echo '<style>
        .inputters{
          color:black;
        }
        </style> 
        <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title">' .$title .'</h2>
                  </div>

                  <div class="panel-body">
                     <div id="card" class="row" id="">

                       <div class="container-fluid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="panel-title"><input id="inputQuestion" type="text" name="question" placeholder="Input your question here ..."></h2>
                
            </div>
            <div class="modal-body">
                <div class="col-xs-3 5"> </div>
                <div class="quiz" id="quiz" data-toggle="buttons"> 
                <label class="element-animation1 btn btn-lg btn-danger btn-block">
                    <span class="btn-label">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </span> 
                  <input class="inputters" type="text" name="q_answer" id="1" placeholder="Place option 1 here ..."> 

                  </label> 

                  <label class="element-animation2 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="2" placeholder="Place option 2 here ...">

                  </label> 

                  <label class="element-animation3 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="3" placeholder="Place option 3 here ...">

                  </label> 

                  <label class="element-animation4 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="q_answer" id="4" placeholder="Place last option here ..."> 

                  </label> 

                   <label class="element-animationAnswer btn btn-lg btn-primary btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="answer" id="answer" placeholder="Place answer here ..."> 

                  </label> 
                  </div>
                  <a href="javascript:void(0);" onclick="WIQuizBar.SaveQuestion();">Save</a>
                  <div id="msg"></div>
            </div>
        </div>
    </div>
</div>
                       
                     
        
                    </div>
                  </div>
               </div>
             </div>';
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
        <ul style="padding: 0px; margin-left: -41px; height: 157px; overflow: scroll;" id="ingr">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addIngred();">Add New ingrediant</a></div>
            <li class="ingred">
           <div class="col-lg-12">
            <input class="ingrediant" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>
        <ul style="padding: 0px; margin-left: -41px; margin-top: 153px; overflow: scroll; width: 100%; height: 140px; overflow: scroll;" id="method">
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


  public function addProduct($title)
  {
        echo ' <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newItem" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <div class="col-lg-12">
                     <input type="text" placeholder="product id" id="product_id"><button onclick="WIProducts.prodIdGenerator()">id generator</button>
                     </div>
                     <div class="col-lg-12">
                     <input type="text" placeholder="product code" id="product_code"><button onclick="WIProducts.prodCodeGenerator()">code generator</button>
                     </div>
                     <select id="menuSection">';
                     self::kitchenSection();
                           // <option value=""></option>
                     echo '</select>

                  </div>
                  <div class="panel-body">
                     <div id="card" class="row" id="">
                     <div class="col-lg-12">

                    <img class="profile" src="WIMedia/Img/menu/drinks/allegian/vegan.jpg" width="40px" />

                     <div class="btn-switch">                  
                     <input type="radio"  name="switch-vegan" class="btn-switch__radio btn-switch__radio_yes vegan" value="1" id="yesvegan" data-id="vegan" />
                     <input type="radio" checked  name="switch-vegan" class="btn-switch__radio btn-switch__radio_no vegan" value="0" id="novegan" data-id="vegan" />       
                    <label for="yesvegan" class="btn-switch__label btn-switch__label_yes">
                    <span class="btn-switch__txt">Yes</span></label>                               
                    <label for="novegan" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
                      </div>
                    
                    <img class="profile" src="WIMedia/Img/menu/drinks/allegian/VEG.png" width="40px" />
                    
                    <div class="btn-switch">                  
                     <input type="radio"  name="switch-veg" class="btn-switch__radio btn-switch__radio_yes veg" value="1" id="yesveg" data-id="veg" />
                     <input type="radio" checked  name="switch-veg" class="btn-switch__radio btn-switch__radio_no veg" value="0" id="noveg" data-id="veg" />       
                    <label for="yesveg" class="btn-switch__label btn-switch__label_yes">
                    <span class="btn-switch__txt">Yes</span></label>                               
                    <label for="noveg" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
                      </div>

                    <img class="profile" src="WIMedia/Img/menu/drinks/allegian/new.png" width="40px" />
                    
                    <div class="btn-switch">                  
                     <input type="radio"  name="switch-new" class="btn-switch__radio btn-switch__radio_yes new" value="1" id="yesnew" data-id="new" />
                     <input type="radio" checked  name="switch-new" class="btn-switch__radio btn-switch__radio_no new" value="0" id="nonew" data-id="new" />       
                    <label for="yesnew" class="btn-switch__label btn-switch__label_yes">
                    <span class="btn-switch__txt">Yes</span></label>                               
                    <label for="nonew" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
                      </div>

                   
                    <img class="profile" src="WIMedia/Img/menu/drinks/allegian/gf.jpg" width="40px" />
                  
                  <div class="btn-switch">                  
                     <input type="radio"  name="switch-gf" class="btn-switch__radio btn-switch__radio_yes gf" value="1" id="yesgf" data-id="gf" />
                     <input type="radio" checked  name="switch-gf" class="btn-switch__radio btn-switch__radio_no gf" value="0" id="nogf" data-id="gf" />       
                    <label for="yesgf" class="btn-switch__label btn-switch__label_yes">
                    <span class="btn-switch__txt">Yes</span></label>                               
                    <label for="nogf" class="btn-switch__label btn-switch__label_no"><span class="btn-switch__txt">No</span></label>                           
                      </div>
                     </div>
                        <div class="col-md-6 headshot" id="newmenuItemPic">';
                        echo '<img class="profile" src="WIMedia/Img/menu/default.jpg" width="218px" /><a href="javascript:void(0);" onclick="WIProducts.addphoto()" class="btn pic" style="margin-top: -241px;margin-left: 43px;">' . WILang::get("change_pic") . '</a><div id="status"></div>
                        </div>
                       
                        <div class="col-md-6">
           <div class="col-lg-12">
            <textarea class="description" type="text" name="description" placeholder="product description" id="desc"></textarea
            </div>

            <div class="col-lg-4">
            <input class="price" type="text" name="price" placeholder="product price" id="price">
            </div>
            </div>
            <form>';
                     echo'</div>
                     <a href="javascript:void(0);" onclick="WIProducts.newMenuItem();">Save</a>
                  </div>
               </div>
             </div>
             </div>';
  }


   public function addBuff($title)
  {

          echo ' <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newItem" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <select id="SectionId">';
                     self::kitchenSection();
                           // <option value=""></option>
                     echo '</select>
                     <select id="buffetSection">';
                     self::buffetSection();
                           // <option value=""></option>
                     echo '</select>

                  </div>
                  <div class="panel-body">
                     <div id="card" class="row" id="">
                        <div class="col-md-6 headshot" id="newBuffMenuItem">';
                        echo '<img class="profile" src="WIMedia/Img/menu/default.jpg" width="218px" /><a href="javascript:void(0);" onclick="WIKitchen.addBuffphoto()" class="btn pic" style="margin-top: -241px;margin-left: 43px;">' . WILang::get("change_pic") . '</a><div id="status"></div>
                        </div>
                       
                        <div class="col-md-6">
        <ul style="padding: 0px;margin-left: -41px;" id="ingr">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addBuffIngred();">Add New ingrediant</a></div>
            <li class="ingred">
           <div class="col-lg-12">
            <input class="ingrediant" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>
        <ul style="padding: 0px;margin-left: -41px;margin-top: 214px;overflow: scroll;width: 100%;height: 246px;" id="method">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addBuffMethod();">Add New method</a></div>
            <li>
            <div class="col-lg-12">
            <input class="method" type="text" name="method" placeholder="Place eample into example">
            </div>
            </li>
         </ul>
         <form>';
                     echo'</div>
                     <a href="javascript:void(0);" onclick="WIKitchen.newBuffetItem();">Save</a>
                  </div>
               </div>
             </div>';
  }


     public function addPrep($title)
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

    $( "#preptabs" ).tabs({
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

 $( "#preptabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  </script>
  
</head>
<body>
 
<div id="preptabs">
  <ul>
    <li><a href="#tabs-1">Nunc tincidunt</a></li><a href="javascript:void(0)" onclick="WIKitchen.addPrep();"><img src="WIMedia/Img/clear.jpg" class="profile" style="width:5%" title="add new dish"></a>
  </ul>
  <div id="tabs-1">
              <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newprep" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <select id="prepSection">';
                     self::kitchenSection();
                           // <option value=""></option>
                     echo '</select>


                  </div>
                  <div class="panel-body">
                     <div id="card" class="row" id="">
                       
                        <div class="col-md-6">
        <ul style="padding: 0px;margin-left: -41px;" id="prepingr">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addPrepIngred();">Add New ingrediant</a></div>
            <li class="prepingred">
           <div class="col-lg-12">
            <input class="prepingredient" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>
        <ul style="padding: 0px;margin-left: -41px;margin-top: 214px;overflow: scroll;width: 100%;" id="prepmethod">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addPrepMethod();">Add New method</a></div>
            <li>
            <div class="col-lg-12">
            <input class="prepmethod" type="text" name="method" placeholder="Place eample into example">
            </div>
            </li>
         </ul>
         <form>
         </div>
                     <a href="javascript:void(0);" onclick="WIKitchen.newPrepItem();">Save</a>
                  </div>
               </div>
             </div>
  </div>
</div>';
  }


  public function addTake($title)
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

    $( "#Taketabs" ).tabs({
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

 $( "#Taketabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  </script>
  
</head>
<body>
 
<div id="Taketabs">
  <ul>
    <li><a href="#tabs-1">t</a></li><a href="javascript:void(0)" onclick="WIKitchen.addTake();"><img src="WIMedia/Img/clear.jpg" class="profile" style="width:5%" title="add new dish"></a>
  </ul>
  <div id="tabs-1">
              <div class="col-md-12">
               <div id="contact-card" class="panel panel-default">
                  <div class="panel-heading">
                     <h2 class="panel-title"><input id="newprep" type="text" name="menu_name" placeholder="' .$title .'"></h2>
                     <select id="TakeSection">';
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
        <ul style="padding: 0px;margin-left: -41px;" id="takeingr">
         <div><a href="javascript:void(0);" onclick="WIKitchen.addTakeIngred();">Add New ingrediant</a></div>
            <li class="ingred">
           <div class="col-lg-12">
            <input class="takeingredient" type="text" name="ingredient" placeholder="1 serving example">
            </div>
            </li>
         </ul>
         
         </div>

         <form>
         </div>
                     <a href="javascript:void(0);" onclick="WIKitchen.newTakeItem();">Save</a>
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

  public function buffphoto($title)
  {
       echo '<form class="form-horizontal" id="add_buff_photo" enctype="multipart/form-data" method="POST">
          <style>
#buffdragandrophandler
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

<div id="buffdragandrophandler">Drag & Drop Files Here</div><div id="buffsupload" value="buffmenu"></div>
<div id="newBuffMenuItem"></div>
<br><br>
<div id="buffystatus"></div>

                      <hr />
        <!-- Show the images preview here -->
        <div id="upload-preview"></div>

                  </form>';
  }

  public function change()
  {
    echo '<form class="form-horizontal" id="change_menu_photo" enctype="multipart/form-data" method="POST">
      <input type="hidden" id="change_menu_pic" value="">
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


           
          <div id="changedragandrophandler" class="menuChange">Drag & Drop Files Here</div>
          <div id="supload" value="menu"></div>
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
    $result = $this->WIdb->select("SELECT * FROM `wipos_sections`");

    foreach ($result as $res) {
      echo '<option value="' .$res['id'] . '">' .$res['name'] . '</option>';
    }
  }


    public function buffetSection()
  {
    $result = $this->WIdb->select("SELECT * FROM `wi_kitchen_21` WHERE `section_id`=9");

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

  public function delete_spec($id)
  {

  }

  public function addSite()
  {
       echo '<form class="form-horizontal" id="add_photo">
          <div id="addCompSite" class="col-lg-12">
          <label class="element-animation1 btn btn-lg btn-danger btn-block">
                    <span class="btn-label">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </span> 
                  <input class="inputters" type="text" name="location" id="site_location" placeholder="Location of site"> 

                  </label> 

                  <label class="element-animation2 btn btn-lg btn-danger btn-block">
                  <span class="btn-label">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  </span> 
                  <input class="inputters" type="text" name="site" id="site_name" placeholder="Site Name">

                  </label> 

                  <a href="javascript:void(0);" onclick="WICompliance.addSite();">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-user-edit"></i>
                            Add Site
                          </button>
                        </a>
          </div>

        </form>';
  }

  public function addons()
  {
    echo '<div id="eaddons"></div>
    <div id="extr"></div>';
  }


    /**
     * Render the shared compliance question builder form.
     *
     * @return void
     */
    public function QuestionForm(): void
    {

        $register = $this->comp instanceof WICompliance
    ? $this->comp->questions()->getQuestionRegisterData()
    : ['checklists' => []];

$checklists = is_array($register['checklists'] ?? null) ? $register['checklists'] : [];

$inputWidthOptions = $this->builder instanceof WIBuilder
    ? $this->builder->getInputWidthOptions()
    : [25 => '25%', 50 => '50%', 75 => '75%', 100 => '100%'];

$inputRowOptions = $this->builder instanceof WIBuilder
    ? $this->builder->getInputRowOptions()
    : [1 => '1 row', 3 => '3 rows', 6 => '6 rows'];

$wrapperWidthOptions = $this->builder instanceof WIBuilder
    ? $this->builder->getWrapperWidthOptions()
    : [50 => '50%', 100 => '100%'];

$wrapperHeightOptions = $this->builder instanceof WIBuilder
    ? $this->builder->getWrapperHeightOptions()
    : [0 => 'Auto', 120 => '120px', 200 => '200px'];

$supportsMedia = false;

        if ($this->comp !== null && method_exists($this->comp, 'getQuestionRegisterData')) {
            $register = (array) $this->comp->getQuestionRegisterData();
            $checklists = is_array($register['checklists'] ?? null) ? $register['checklists'] : [];
        }

        echo '<form class="form-horizontal wi-modal-form" id="wiQuestionForm" novalidate>';
        echo '<input type="hidden" id="wiQuestionId" name="id" value="0">';
        echo '<input type="hidden" id="wiQuestionChecklistId" name="checklist_id" value="0">';
        echo '<input type="hidden" id="wiQuestionSectionId" name="section_id" value="0">';

        echo '<div class="row">';
        echo '  <div class="col-md-6"><div class="form-group"><label for="wiQuestionCode">Question Code</label><input type="text" class="form-control" id="wiQuestionCode" name="question_code" maxlength="120"></div></div>';
        echo '  <div class="col-md-6"><div class="form-group"><label for="wiQuestionInputType">Input Type</label><select class="form-control" id="wiQuestionInputType" name="input_type">';
        foreach (['yes_no'=>'Yes / No','pass_fail'=>'Pass / Fail','text'=>'Text','textarea'=>'Textarea','number'=>'Number','temperature'=>'Temperature','date'=>'Date','time'=>'Time','datetime'=>'Datetime','select'=>'Select','radio'=>'Radio','checkbox'=>'Checkbox','signature'=>'Signature','photo'=>'Photo'] as $value => $label) {
            echo '<option value="' . $this->e($value) . '">' . $this->e($label) . '</option>';
        }
        echo '  </select></div></div>';
        echo '</div>';

        echo '<div class="form-group"><label for="wiQuestionText">Question Text</label><textarea class="form-control" id="wiQuestionText" name="question_text" rows="3" required></textarea></div>';
        echo '<div class="form-group"><label for="wiQuestionHelpText">Help Text</label><textarea class="form-control" id="wiQuestionHelpText" name="help_text" rows="2"></textarea></div>';

        echo '<div class="row">';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionSortOrder">Sort Order</label><input type="number" min="1" class="form-control" id="wiQuestionSortOrder" name="sort_order" value="1"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionScoreWeight">Score Weight</label><input type="text" class="form-control" id="wiQuestionScoreWeight" name="score_weight" value="1.00"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionUnitLabel">Unit Label</label><input type="text" class="form-control" id="wiQuestionUnitLabel" name="unit_label"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionDefaultValue">Default Value</label><input type="text" class="form-control" id="wiQuestionDefaultValue" name="default_value"></div></div>';
        echo '</div>';

        echo '<div class="row">';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionMinValue">Min Value</label><input type="text" class="form-control" id="wiQuestionMinValue" name="min_value"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionMaxValue">Max Value</label><input type="text" class="form-control" id="wiQuestionMaxValue" name="max_value"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionExpectedValue">Expected Value</label><input type="text" class="form-control" id="wiQuestionExpectedValue" name="expected_value"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionFailValue">Fail Value</label><input type="text" class="form-control" id="wiQuestionFailValue" name="fail_value"></div></div>';
        echo '</div>';

        echo '<div class="row">';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionInputSize">Input Size</label><select class="form-control" id="wiQuestionInputSize" name="input_size"><option value="sm">Small</option><option value="md" selected>Medium</option><option value="lg">Large</option><option value="xl">Extra Large</option></select></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionInputWidth">Input Width</label><select class="form-control" id="wiQuestionInputWidth" name="input_width"><option value="25">25%</option><option value="33">33%</option><option value="50" selected>50%</option><option value="66">66%</option><option value="75">75%</option><option value="100">100%</option></select></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionInputRows">Rows / Height</label><input type="number" min="1" class="form-control" id="wiQuestionInputRows" name="input_rows" value="3"></div></div>';
        echo '  <div class="col-md-3"><div class="form-group"><label for="wiQuestionMaxLength">Max Length</label><input type="number" min="0" class="form-control" id="wiQuestionMaxLength" name="max_length"></div></div>';
        echo '</div>';


        echo '    <div class="row">';
echo '        <div class="col-md-6">';
echo '            <div class="form-group">';
echo '                <label for="wiQuestionWrapperWidth">Field Wrapper Width</label>';
echo '                <select id="wiQuestionWrapperWidth" name="wrapper_width" class="form-control wi-builder-control">';
echo                      $this->renderOptions($wrapperWidthOptions, 100);
echo '                </select>';
echo '            </div>';
echo '        </div>';

echo '        <div class="col-md-6">';
echo '            <div class="form-group">';
echo '                <label for="wiQuestionWrapperMinHeight">Field Wrapper Min Height</label>';
echo '                <select id="wiQuestionWrapperMinHeight" name="wrapper_min_height" class="form-control wi-builder-control">';
echo                      $this->renderOptions($wrapperHeightOptions, 0);
echo '                </select>';
echo '            </div>';
echo '        </div>';
echo '    </div>';

        echo '<div class="row">';
        echo '  <div class="col-md-4"><div class="form-group"><label for="wiQuestionStepValue">Step</label><input type="text" class="form-control" id="wiQuestionStepValue" name="step_value"></div></div>';
        echo '  <div class="col-md-4"><div class="form-group"><label for="wiQuestionPlaceholderText">Placeholder Text</label><input type="text" class="form-control" id="wiQuestionPlaceholderText" name="placeholder_text"></div></div>';
        echo '  <div class="col-md-4"><div class="form-group"><label for="wiQuestionSectionId">Section ID</label><input type="number" min="0" class="form-control" id="wiQuestionSectionId" name="section_id" value="0"></div></div>';
        echo '</div>';

        echo '<div class="panel panel-default wi-modal-section"><div class="panel-heading"><strong>Checklist Assignment</strong></div><div class="panel-body">';
        if ($checklists !== []) {
            echo '<div class="row">';
            foreach ($checklists as $index => $checklist) {
                $checklistId = (int) ($checklist['id'] ?? 0);
                $title = (string) ($checklist['title'] ?? 'Untitled Checklist');
                echo '<div class="col-md-6"><label class="checkbox-inline"><input type="checkbox" class="wiQuestionChecklistCheckbox" value="' . $this->e((string) $checklistId) . '"> ' . $this->e($title) . '</label></div>';
                if (($index + 1) % 2 === 0) {
                    echo '</div><div class="row">';
                }
            }
            echo '</div>';
        } else {
            echo '<p class="text-muted">No checklist options are currently available.</p>';
        }
        echo '</div></div>';

        echo '<div class="panel panel-default wi-modal-section"><div class="panel-heading"><strong>Flags</strong></div><div class="panel-body">';
        $flags = [
            'wiQuestionIsRequired' => 'Required',
            'wiQuestionIsCritical' => 'Critical',
            'wiQuestionAllowNa' => 'Allow N/A',
            'wiQuestionRequiresPhotoOnFail' => 'Require Photo On Fail',
            'wiQuestionRequiresNoteOnFail' => 'Require Note On Fail',
            'wiQuestionRequiresManagerReviewOnFail' => 'Require Manager Review On Fail',
            'wiQuestionIsActive' => 'Active',
        ];
        echo '<div class="row">';
        foreach ($flags as $id => $label) {
            echo '<div class="col-md-4"><label class="checkbox-inline"><input type="checkbox" id="' . $this->e($id) . '" ' . ($id === 'wiQuestionIsRequired' || $id === 'wiQuestionIsActive' ? 'checked' : '') . '> ' . $this->e($label) . '</label></div>';
        }
        echo '</div></div></div>';

        echo '<div class="alert alert-info"><strong>Builder sizing:</strong> input width, input rows, and wrapper sizing are stored in the question config. Drag-resize can be added on top later through WIBuilder.</div>';

        echo '    <hr />';
        echo '    <div class="wi-builder-preview-wrap">';
        echo '        <label>Live Field Preview</label>';
        echo '        <div id="wiQuestionBuilderPreviewWrap" class="wi-builder-preview-wrap__inner" style="width:100%; min-height:0;">';
        echo '            <div id="wiQuestionBuilderPreviewField" class="wi-builder-preview-field" style="width:50%;">';
        echo '                <label id="wiQuestionBuilderPreviewLabel">Question preview</label>';
        echo '                <input type="text" id="wiQuestionBuilderPreviewInput" class="form-control" placeholder="Preview input" />';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        if ($supportsMedia) {
            echo '<hr />';
            echo '<div class="wi-modal-media-gate">';
            echo '    <label>Media Source</label>';
            echo '    <div class="btn-group" role="group" aria-label="Media source chooser">';
            echo '        <button type="button" class="btn btn-default" data-media-source="camera">Use Camera</button>';
            echo '        <button type="button" class="btn btn-default" data-media-source="upload">Upload</button>';
            echo '        <button type="button" class="btn btn-default" data-media-source="library">Media Library</button>';
            echo '    </div>';
            echo '</div>';
}
        echo '</form>';
    }

    /**
     * Render the shared compliance question CSV import form.
     *
     * @return void
     */
    /**
     * Render the Questions CSV import modal body.
     *
     * UI only.
     *
     * @return void
     */
    public function QuestionImportForm(): void
    {
        $checklists = [];

        if ($this->comp instanceof WICompliance) {
            $register = $this->comp->questions()->getQuestionRegisterData();
            $checklists = is_array($register['checklists'] ?? null) ? $register['checklists'] : [];
        }

        echo '<form id="wiQuestionImportForm" class="form-horizontal wi-modal-form" novalidate>';
        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <div class="form-group">';
        echo '                <label for="wiQuestionImportChecklistId">Default Checklist</label>';
        echo '                <select id="wiQuestionImportChecklistId" name="default_checklist_id" class="form-control">';
        echo '                    <option value="0">No default checklist</option>';

        foreach ($checklists as $checklist) {
            $id = (int) ($checklist['id'] ?? 0);
            $title = (string) ($checklist['title'] ?? 'Untitled Checklist');

            echo '                <option value="' . $this->e((string) $id) . '">' . $this->e($title) . '</option>';
        }

        echo '                </select>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <div class="form-group">';
        echo '                <label for="wiQuestionCsvContent">CSV Content</label>';
        echo '                <textarea id="wiQuestionCsvContent" name="csv_content" class="form-control" rows="12" placeholder="question_code,question_text,input_type,sort_order,is_required,is_critical"></textarea>';
        echo '                <p class="help-block">Paste raw CSV rows here for import.</p>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';
        echo '</form>';
    }

    /**
     * Escape output safely for HTML rendering.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    private function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render a select options list safely.
     *
     * @param array<int, string> $options Options as value => label.
     * @param mixed $selected Selected value.
     * @return string
     */
    protected function renderOptions(array $options, $selected): string
    {
        $html = '';

        foreach ($options as $value => $label) {
            $isSelected = (string) $value === (string) $selected ? ' selected="selected"' : '';
            $html .= '<option value="' . $this->e((string) $value) . '"' . $isSelected . '>' . $this->e($label) . '</option>';
        }

        return $html;
    }



    /**
     * Render the Checklist builder modal body.
     *
     * UI only.
     *
     * @return void
     */
    public function ChecklistForm(): void
    {
        $lookupData = [
            'roles' => [],
            'checklist_types' => [],
            'site_scopes' => [],
            'frequencies' => [],
            'rule_types' => [],
            'amber_types' => [],
        ];

        if (
            $this->comp instanceof WICompliance
            && method_exists($this->comp, 'getComplianceChecklistBuilderLookups')
        ) {
            $modernLookupData = $this->comp->getComplianceChecklistBuilderLookups();

            if (is_array($modernLookupData)) {
                $lookupData = array_merge($lookupData, $modernLookupData);
            }
        }

        $roles = is_array($lookupData['roles'] ?? null) ? $lookupData['roles'] : [];
        $checklistTypes = is_array($lookupData['checklist_types'] ?? null) ? $lookupData['checklist_types'] : [];
        $siteScopes = is_array($lookupData['site_scopes'] ?? null) ? $lookupData['site_scopes'] : [];
        $frequencies = is_array($lookupData['frequencies'] ?? null) ? $lookupData['frequencies'] : [];
        $ruleTypes = is_array($lookupData['rule_types'] ?? null) ? $lookupData['rule_types'] : [];
        $amberTypes = is_array($lookupData['amber_types'] ?? null) ? $lookupData['amber_types'] : [];

        echo '<form id="wiChecklistForm" class="form-horizontal wi-modal-form" novalidate>';
        echo '    <input type="hidden" id="wiChecklistId" name="id" value="0">';

        echo '    <div class="row">';
        echo '        <div class="col-md-8">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistTitle">Title</label>';
        echo '                <input type="text" id="wiChecklistTitle" name="title" class="form-control" value="">';
        echo '            </div>';
        echo '        </div>';
        echo '        <div class="col-md-4">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistCode">Code</label>';
        echo '                <input type="text" id="wiChecklistCode" name="code" class="form-control" value="">';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-4">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistCategory">Category</label>';
        echo '                <input type="text" id="wiChecklistCategory" name="category" class="form-control" value="">';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-4">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistType">Checklist Type</label>';
        echo '                <select id="wiChecklistType" name="checklist_type" class="form-control">';
        foreach ($checklistTypes as $item) {
            echo '<option value="' . $this->e((string) ($item['value'] ?? '')) . '">' . $this->e((string) ($item['label'] ?? '')) . '</option>';
        }
        echo '                </select>';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-4">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistFrequency">Frequency</label>';
        echo '                <select id="wiChecklistFrequency" name="frequency" class="form-control">';
        foreach ($frequencies as $item) {
            echo '<option value="' . $this->e((string) ($item['value'] ?? '')) . '">' . $this->e((string) ($item['label'] ?? '')) . '</option>';
        }
        echo '                </select>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-6">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistAssignedRoleId">Assigned Role</label>';
        echo '                <select id="wiChecklistAssignedRoleId" name="assigned_role_id" class="form-control">';
        echo '                    <option value="0">Select role</option>';
        foreach ($roles as $role) {
            $roleId = (int) ($role['role_id'] ?? 0);
            $roleName = (string) ($role['role'] ?? '');
            echo '<option value="' . $this->e((string) $roleId) . '">' . $this->e($roleName) . '</option>';
        }
        echo '                </select>';
        echo '                <input type="hidden" id="wiChecklistAssignedRole" name="assigned_role" value="">';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-6">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistSiteScope">Site Scope</label>';
        echo '                <select id="wiChecklistSiteScope" name="site_scope" class="form-control">';
        foreach ($siteScopes as $item) {
            echo '<option value="' . $this->e((string) ($item['value'] ?? '')) . '">' . $this->e((string) ($item['label'] ?? '')) . '</option>';
        }
        echo '                </select>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <hr />';

        echo '    <div class="row">';
        echo '        <div class="col-md-3">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistRequiresNotes" name="requires_notes" value="1"> Requires Notes</label></div>';
        echo '        </div>';
        echo '        <div class="col-md-3">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistRequiresPhoto" name="requires_photo" value="1"> Requires Photo</label></div>';
        echo '        </div>';
        echo '        <div class="col-md-3">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistEvidenceRequired" name="evidence_required" value="1"> Evidence Required</label></div>';
        echo '        </div>';
        echo '        <div class="col-md-3">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistRequiresSignoff" name="requires_signoff" value="1"> Requires Sign-Off</label></div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistIsActive" name="is_active" value="1" checked="checked"> Active</label></div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <hr />';

        echo '    <div class="row">';
        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistOpensRuleType">Opens Rule Type</label>';
        echo '                <select id="wiChecklistOpensRuleType" name="opens_rule_type" class="form-control">';
        foreach ($ruleTypes as $item) {
            echo '<option value="' . $this->e((string) ($item['value'] ?? '')) . '">' . $this->e((string) ($item['label'] ?? '')) . '</option>';
        }
        echo '                </select>';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistOpensRuleValue">Opens Rule Value</label>';
        echo '                <input type="text" id="wiChecklistOpensRuleValue" name="opens_rule_value" class="form-control" value="">';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistClosesRuleType">Closes Rule Type</label>';
        echo '                <select id="wiChecklistClosesRuleType" name="closes_rule_type" class="form-control">';
        foreach ($ruleTypes as $item) {
            echo '<option value="' . $this->e((string) ($item['value'] ?? '')) . '">' . $this->e((string) ($item['label'] ?? '')) . '</option>';
        }
        echo '                </select>';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistClosesRuleValue">Closes Rule Value</label>';
        echo '                <input type="text" id="wiChecklistClosesRuleValue" name="closes_rule_value" class="form-control" value="">';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistIntervalMinutes">Interval Minutes</label>';
        echo '                <input type="text" id="wiChecklistIntervalMinutes" name="interval_minutes" class="form-control" value="">';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistGraceMinutes">Grace Minutes</label>';
        echo '                <input type="text" id="wiChecklistGraceMinutes" name="grace_minutes" class="form-control" value="0">';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistAmberType">Amber Type</label>';
        echo '                <select id="wiChecklistAmberType" name="amber_type" class="form-control">';
        foreach ($amberTypes as $item) {
            echo '<option value="' . $this->e((string) ($item['value'] ?? '')) . '">' . $this->e((string) ($item['label'] ?? '')) . '</option>';
        }
        echo '                </select>';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="col-md-3">';
        echo '            <div class="form-group">';
        echo '                <label for="wiChecklistAmberValue">Amber Value</label>';
        echo '                <input type="text" id="wiChecklistAmberValue" name="amber_value" class="form-control" value="25">';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

            echo '    <hr />';

        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <div class="wi-builder-preview-wrap">';
        echo '                <label>Timing Preview</label>';
        echo '                <div id="wiChecklistTimingPreview" class="alert alert-info" style="margin-bottom:0;">';
        echo '                    Set checklist timing rules to preview the runtime window.';
        echo '                </div>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <hr />';

        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <h4 style="margin-top:0;">Evidence / Proof Rules</h4>';
        echo '            <p class="help-block">These controls determine whether the runtime should require proof before completion or on failure.</p>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-4">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistEvidenceRequired" name="evidence_required" value="1"> Evidence Required</label></div>';
        echo '        </div>';
        echo '        <div class="col-md-4">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistRequiresPhoto" name="requires_photo" value="1"> Photo / Media Proof</label></div>';
        echo '        </div>';
        echo '        <div class="col-md-4">';
        echo '            <div class="checkbox"><label><input type="checkbox" id="wiChecklistRequiresNotes" name="requires_notes" value="1"> Notes Required</label></div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <div id="wiChecklistEvidencePreview" class="alert alert-warning" style="margin-bottom:0;">';
        echo '                Runtime proof requirements will appear here based on the selected flags.';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div id="wiChecklistModalMessage" class="alert" style="display:none;"></div>';



        echo '    <div class="row" style="margin-top:12px;">';
echo '        <div class="col-md-12">';
echo '            <button type="button" class="btn btn-default" id="wiChecklistProofLauncherBtn">';
echo '                <i class="fa fa-shield" aria-hidden="true"></i> Open Proof Flow Preview';
echo '            </button>';
echo '            <div id="wiChecklistProofRulesPreview" class="alert alert-info" style="margin-top:10px; margin-bottom:0;">';
echo '                Checklist proof rules will appear here.';
echo '            </div>';
echo '        </div>';
echo '    </div>';


        echo '</form>';
    }

    /**
     * Render the shared media source chooser.
     *
     * UI only.
     *
     * @return void
     */
    public function MediaSourceChooser(): void
    {
        echo '<div class="wi-media-source-chooser">';
        echo '    <div class="row">';
        echo '        <div class="col-md-12">';
        echo '            <p class="help-block">Select how you want to provide evidence or proof.</p>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div class="row">';
        echo '        <div class="col-sm-4">';
        echo '            <button type="button" class="btn btn-default btn-block wiMediaSourceBtn" data-source="camera">';
        echo '                <i class="fa fa-camera" aria-hidden="true"></i> Use Camera';
        echo '            </button>';
        echo '        </div>';

        echo '        <div class="col-sm-4">';
        echo '            <button type="button" class="btn btn-default btn-block wiMediaSourceBtn" data-source="upload">';
        echo '                <i class="fa fa-upload" aria-hidden="true"></i> Upload';
        echo '            </button>';
        echo '        </div>';

        echo '        <div class="col-sm-4">';
        echo '            <button type="button" class="btn btn-default btn-block wiMediaSourceBtn" data-source="library">';
        echo '                <i class="fa fa-photo" aria-hidden="true"></i> Media Library';
        echo '            </button>';
        echo '        </div>';
        echo '    </div>';

        echo '    <hr />';

        echo '    <div id="wiMediaSourcePanel" class="wi-media-source-panel">';
        echo '        <div class="alert alert-info" style="margin-bottom:0;">';
        echo '            Choose a media source to continue.';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }


/**
     * Return whether a modal should expose media/proof controls.
     *
     * @param string $context Modal context name.
     * @return bool
     */
    protected function supportsMediaForContext(string $context): bool
    {
        $context = trim(strtolower($context));

        $mediaContexts = [
            'checklistevidence',
            'incidentevidence',
            'auditevidence',
            'correctiveactionevidence',
            'equipmentevidence',
            'questionevidence',
        ];

        return in_array($context, $mediaContexts, true);
    }



    /**
     * Render the shared checklist proof flow modal body.
     *
     * UI only.
     *
     * @return void
     */
    public function ChecklistProofFlow(): void
    {
        echo '<div class="wi-proof-flow">';
        echo '    <div id="wiProofSourceChooser" class="wi-proof-panel wi-proof-panel--active">';
        echo '        <div class="row">';
        echo '            <div class="col-md-12">';
        echo '                <p class="help-block">Select a media source to provide checklist proof.</p>';
        echo '            </div>';
        echo '        </div>';

        echo '        <div class="row">';
        echo '            <div class="col-sm-4">';
        echo '                <button type="button" class="btn btn-default btn-block wiProofSourceBtn" data-target-panel="camera">';
        echo '                    <i class="fa fa-camera" aria-hidden="true"></i> Use Camera';
        echo '                </button>';
        echo '            </div>';

        echo '            <div class="col-sm-4">';
        echo '                <button type="button" class="btn btn-default btn-block wiProofSourceBtn" data-target-panel="upload">';
        echo '                    <i class="fa fa-upload" aria-hidden="true"></i> Upload';
        echo '                </button>';
        echo '            </div>';

        echo '            <div class="col-sm-4">';
        echo '                <button type="button" class="btn btn-default btn-block wiProofSourceBtn" data-target-panel="library">';
        echo '                    <i class="fa fa-photo" aria-hidden="true"></i> Media Library';
        echo '                </button>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div id="wiProofPanelCamera" class="wi-proof-panel" style="display:none;">';
        echo '        <div class="alert alert-info">Camera capture panel placeholder. This will later use browser camera capture with fallback.</div>';
        echo '        <button type="button" class="btn btn-default wiProofBackBtn">Back</button>';
        echo '    </div>';

        echo '    <div id="wiProofPanelUpload" class="wi-proof-panel" style="display:none;">';
        echo '        <div class="alert alert-info">Upload panel placeholder. This will connect to WIMedia upload flow.</div>';
        echo '        <button type="button" class="btn btn-default wiProofBackBtn">Back</button>';
        echo '    </div>';

        echo '    <div id="wiProofPanelLibrary" class="wi-proof-panel" style="display:none;">';
        echo '        <div class="alert alert-info">Media library placeholder. This will connect to the shared media library selector.</div>';
        echo '        <button type="button" class="btn btn-default wiProofBackBtn">Back</button>';
        echo '    </div>';
        echo '</div>';
    }

    /**
     * Renders the checklist builder modal body.
     *
     * The modal frame, header and footer are rendered by moduleModal().
     * This method only renders the builder body content.
     *
     * @param string $action Modal action/controller name.
     * @param string $title Modal title.
     * @param string $ele_id Modal element ID.
     *
     * @return void
     */
    public function checklistsBuilder($action = '', $title = '', $ele_id = ''): void
    {
        echo '<div class="wi-checklist-builder-shell" data-wi-builder data-wi-checklist-builder>';
        echo '    <input type="hidden" data-builder-checklist-id value="0">';

        echo '    <div class="wi-builder-toolbar">';
        echo '        <div class="wi-builder-toolbar-main">';
        echo '            <strong data-builder-title>Checklist Builder</strong>';
        echo '            <small>Load a checklist to edit sections, questions, modules and layout.</small>';
        echo '        </div>';

        echo '        <div class="wi-builder-toolbar-actions">';
        echo '            <button type="button" class="btn btn-sm btn-secondary" data-builder-add-section>Add Section</button>';
        echo '            <button type="button" class="btn btn-sm btn-primary" data-builder-save>Save Builder</button>';
        echo '        </div>';
        echo '    </div>';

        echo '    <div data-builder-message></div>';
        echo '    <div class="wi-builder-summary" data-builder-summary></div>';

        echo '    <div class="wi-builder-grid">';
        echo '        <aside class="wi-builder-question-bank">';
        echo '            <h5>Question Bank</h5>';
        echo '            <div data-builder-question-bank>';
        echo '                <div class="wi-builder-empty">Select a checklist to load available questions.</div>';
        echo '            </div>';
        echo '        </aside>';

        echo '        <main class="wi-builder-canvas-wrap">';
        echo '            <h5>Checklist Layout</h5>';
        echo '            <div data-builder-canvas>';
        echo '                <div class="wi-builder-empty">No checklist layout loaded yet.</div>';
        echo '            </div>';
        echo '        </main>';
        echo '    </div>';
        echo '</div>';

        echo '        <aside class="wi-builder-inspector-wrap">';
        echo '            <h5>Layout Inspector</h5>';
        echo '            <div data-builder-inspector>';
        echo '                <div class="wi-builder-empty">Select a question block to edit size/layout.</div>';
        echo '            </div>';
        echo '        </aside>';

    }



    /**
     * Renders the Document Control Centre add/edit form body.
     *
     * This method is called by moduleModal() and must remain UI-only.
     * It delegates the form HTML to WIComplianceDocumentFormView so the form is
     * easy to maintain and does not live inside JavaScript.
     *
     * @param string $action Modal action/controller name.
     * @param string $title Modal title.
     * @param string $ele_id Modal element ID.
     *
     * @return void
     */
    public function complianceDocumentForm($action, $title, $ele_id): void
    {
        if (!class_exists('WIComplianceDocumentFormView')) {
            $path = __DIR__ . '/WIComplianceDocumentFormView.php';

            if (file_exists($path)) {
                require_once $path;
            }
        }

        if (!class_exists('WIComplianceDocumentFormView')) {
            echo '<div class="alert alert-warning">Document form renderer is not available.</div>';
            return;
        }

        $lookups = [];

        if ($this->comp && method_exists($this->comp, 'getComplianceDocumentFormLookups')) {
            $lookups = $this->comp->getComplianceDocumentFormLookups();
        }

        $form = new WIComplianceDocumentFormView($lookups);
        $form->render();
    }



    /**
     * Renders the WICOS document review modal body.
     *
     * @param string $action Modal action/controller name.
     * @param string $title Modal title.
     * @param string $ele_id Modal element ID.
     *
     * @return void
     */
    public function complianceDocumentReview($action, $title, $ele_id): void
    {
        if (!class_exists('WIComplianceDocumentReviewView')) {
            $path = dirname(__DIR__, 3)
                . '/WICompliance/WICore/WIClass/WIComplianceEngine/Documents/View/WIComplianceDocumentReviewView.php';

            if (file_exists($path)) {
                require_once $path;
            }
        }

        if (!class_exists('WIComplianceDocumentReviewView')) {
            echo '<div class="alert alert-warning">Document review renderer is not available.</div>';
            return;
        }

        $view = new WIComplianceDocumentReviewView();
        $view->render();
    }


    /*
    |--------------------------------------------------------------------------
    | Core WICMS Menu Manager Modals
    |--------------------------------------------------------------------------
    */

    public function menuEdit($action, $title, $ele_id): void
    {
        echo '<form class="form-horizontal" id="wi-menu-edit-form">';
        echo '<input type="hidden" id="edit_menu_id" value="">';
        echo '<div class="form-group"><label>Menu name</label><input type="text" id="edit_menu_name" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Language key</label><input type="text" id="edit_menu_lang" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Link</label><input type="text" id="edit_menu_link" class="form-control" autocomplete="off"></div>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-primary" onclick="WIMenu.menuEdit()">Save</button></div>';
        echo '</form>';
    }

    public function menunew($action, $title, $ele_id): void
    {
        echo '<form class="form-horizontal" id="wi-menu-new-form">';
        echo '<div class="form-group"><label>Menu name</label><input type="text" id="new_menu_name" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Language key</label><input type="text" id="new_menu_lang" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Link</label><input type="text" id="new_menu_link" class="form-control" autocomplete="off"></div>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-primary" onclick="WIMenu.menunew()">Add Menu</button></div>';
        echo '</form>';
    }

    public function deleteMEnu($action, $title, $ele_id): void
    {
        echo '<div class="delete_id" id=""></div>';
        echo '<p>Delete this site menu item?</p>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-danger" onclick="WIMenu.deleteMEnu()">Delete</button></div>';
    }

    public function adminMenuEdit($action, $title, $ele_id): void
    {
        echo '<form class="form-horizontal" id="wi-admin-menu-edit-form">';
        echo '<input type="hidden" id="edit_admin_menu_id" value="">';
        echo '<div class="form-group"><label>Admin menu name</label><input type="text" id="edit_admin_menu_name" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Language key</label><input type="text" id="edit_admin_menu_lang" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Link</label><input type="text" id="edit_admin_menu_link" class="form-control" autocomplete="off"></div>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-primary" onclick="WIMenu.adminMenuEdit()">Save</button></div>';
        echo '</form>';
    }

    public function adminMenuNew($action, $title, $ele_id): void
    {
        echo '<form class="form-horizontal" id="wi-admin-menu-new-form">';
        echo '<div class="form-group"><label>Admin menu name</label><input type="text" id="new_admin_menu_name" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Language key</label><input type="text" id="new_admin_menu_lang" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Link</label><input type="text" id="new_admin_menu_link" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Sort</label><input type="number" id="new_admin_menu_sort" class="form-control" value="0"></div>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-primary" onclick="WIMenu.adminMenuNew()">Add Menu Item</button></div>';
        echo '</form>';
    }

    public function deleteAdminMenuConfirm($action, $title, $ele_id): void
    {
        echo '<div class="delete_admin_menu_id" id=""></div>';
        echo '<p>Delete this admin menu item?</p>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-danger" onclick="WIMenu.deleteAdminMenuConfirm()">Delete</button></div>';
    }

    public function menuLink($action, $title, $ele_id): void
    {
        echo '<form class="form-horizontal" id="wi-sidebar-menu-new-form">';
        echo '<div class="form-group"><label>Sidebar label</label><input type="text" id="sidebar_new_menu_name" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Link</label><input type="text" id="sidebar_new_menu_link" class="form-control" autocomplete="off"></div>';
        echo '<div class="form-group"><label>Parent ID</label><input type="number" id="sidebar_new_menu_parent" class="form-control" value="0"></div>';
        echo '<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="WIMenu.closed(`' . htmlspecialchars((string) $ele_id, ENT_QUOTES, 'UTF-8') . '`)">Cancel</button><button type="button" class="btn btn-primary" onclick="WIMenu.menuLink()">Create</button></div>';
        echo '</form>';
    }

    
}

?>