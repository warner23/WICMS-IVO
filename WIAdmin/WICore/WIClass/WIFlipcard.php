<?php
#[\AllowDynamicProperties]
/**
* Flipcard Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIFlipcard
{
    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

    }

    public function Flipcard($product)
    {
        echo '<div class="item  col-xs-12 col-lg-12 col-md-12 col-sm-12">
        <div class="flipcard-' . $product['prod_id'] . '">
        <span class="btn-edit editing-' . $product['prod_id'] . '">Edit</span>
        <span class="btn-edit show-delete" onclick="WIProduct.delete(`' . $product['prod_id'] . '`);">Delete</span>
        <div class="back">';
            self::backSideFlip($product);
         echo '</div>

                  
        
         <div class="front text-center">';
            self::frontSideFlip($product);
          echo '</div>
              </div>
                </div>
              <style>
               .editing-' . $product['prod_id'] . '{
                        background-color: #bf6cda;
                      color: white;
                      perspective: 638px;
                      margin-left: 10px;
                      margin-top: 10px;
                      cursor:pointer;
                  }
                    .flipcard-' . $product['prod_id'] . ' {
                      position: relative;
                      width: 100%;
                      height: 275px;
                      perspective: 638px;
                      margin: 0px 6px 10px 0px;
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .front {
                    transform: rotateY(180deg);
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .back {
                    transform: rotateY(0deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .back{
                    transform: rotateY(-180deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .front, .flipcard-' . $product['prod_id'] . ' .back
                  {
                      background-color: #eae8e8;
                      border: 1px solid #222;
                      border-radius: 5px;
                      box-shadow: 0 5px 15px rgba(0,0,0,.5);
                      position: absolute;
                      width: 100%;
                      height: 100%;
                      box-sizing: border-box;
                      transition: all 1s ease 0s;
                      color: #a94442;
                      padding: 10px 5px;
                      backface-visibility: hidden;
                      top: 5px;
                      z-index: -1;
                  }
                    </style>
                              <script type="text/javascript">
                              $(document).ready(function(){

                               $(".editing-' . $product['prod_id'] . '").on("click", function(event) {
                                event.stopPropagation();
                                $(".flipcard-' . $product['prod_id'] . '").toggleClass("flip");
                                          });

                              var obj = $("#dragandrophandler-' . $product['prod_id'] . '");
                              var dir = $(".supload").attr("value");
                              var ele_id = $(".img-preview-' . $product['prod_id'] . '").attr("prod_id");

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
    }

        public function FlipcardExtras($product)
    {
        echo '<div class="item  col-xs-12 col-lg-12 col-md-12 col-sm-12">
        <div class="flipcard-' . $product['prod_id'] . '">
        <span class="btn-edit editing-' . $product['prod_id'] . '">Edit</span>
        <span class="btn-edit show-delete" onclick="WIProduct.delete(`' . $product['prod_id'] . '`);">Delete</span>
        <div class="back">';
            self::backSideFlipExtras($product);
         echo '</div>

                  
        
         <div class="front text-center">';
            self::frontSideFlipExtras($product);
          echo '</div>
              </div>
                </div>
              <style>
               .editing-' . $product['prod_id'] . '{
                        background-color: #bf6cda;
                      color: white;
                      perspective: 638px;
                      margin-left: 10px;
                      margin-top: 10px;
                      cursor:pointer;
                  }
                    .flipcard-' . $product['prod_id'] . ' {
                      position: relative;
                      width: 100%;
                      height: 275px;
                      perspective: 638px;
                      margin: 0px 6px 10px 0px;
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .front {
                    transform: rotateY(180deg);
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .back {
                    transform: rotateY(0deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .back{
                    transform: rotateY(-180deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .front, .flipcard-' . $product['prod_id'] . ' .back
                  {
                      background-color: #eae8e8;
                      border: 1px solid #222;
                      border-radius: 5px;
                      box-shadow: 0 5px 15px rgba(0,0,0,.5);
                      position: absolute;
                      width: 100%;
                      height: 100%;
                      box-sizing: border-box;
                      transition: all 1s ease 0s;
                      color: #a94442;
                      padding: 10px 5px;
                      backface-visibility: hidden;
                      top: 5px;
                      z-index: -1;
                  }
                    </style>
                              <script type="text/javascript">
                              $(document).ready(function(){

                               $(".editing-' . $product['prod_id'] . '").on("click", function(event) {
                                event.stopPropagation();
                                $(".flipcard-' . $product['prod_id'] . '").toggleClass("flip");
                                          });

                              var obj = $("#dragandrophandler-' . $product['prod_id'] . '");
                              var dir = $(".supload").attr("value");
                              var ele_id = $(".img-preview-' . $product['prod_id'] . '").attr("prod_id");

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
    }

    public function frontSideFlip($product)
    {

        echo '<div class="front text-center">
         <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4 food-image">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload img-responsive product">
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">
              <h3 class="food-title">
                <span class="food-name">' . $product['prod_name'] . '</span>
                <span class="food-price float-right">
                ' .CURRENCY_SYMBOL . '' . $product['prod_price'] . '</span>
                

              </h3>
              <p class="food-ingredients">
                ' . $product['prod_desc'] . '
              </p>
            </div>
          </div>
            </div>
              </div>';
    }

    public function backSideFlip($product)
    {
        echo ' <div class="back">
           <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-2 col-md-4 col-sm-4 food-image" id="product_pic' . $product['prod_id'] . '">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload product cp"
                id="productPic' . $product['prod_id'] . '">
                <span><a href="javascript:void(0);" onclick="WIMedia.changeProductPic(`' . $product['prod_id'] . '`)">Change PHoto</a></span>
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">
              <h3 class="food-title">
                <span class="food-name"><input type="text" id="prod_name' . $product['prod_id'] . '" value="' . $product['prod_name'] . '"></span>
                <span class="food-price float-right">
                ' .CURRENCY_SYMBOL . '<input type="text" id="prod_price' . $product['prod_id'] . '" value="' . $product['prod_price'] . '"></span>
               
              </h3>
              <p class="food-ingredients">
                <textarea id="prod_desc' . $product['prod_id'] . '" value="'. $product['prod_desc'] . '">'. $product['prod_desc'] . '</textarea>
              </p>
            </div>
            <a href="javascript:void(0);" id="productSave" onclick="WIProduct.saveProduct(`' . $product['prod_id'] . '`)">Save</a>
          </div>
            </div>
                  </div>';
    }


    public function frontSideFlipExtras($product)
    {
      //var_dump($product);
        echo '<div class="front text-center">
         <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4 food-image">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload img-responsive product">
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">';
             $results = $this->WIdb->select('SELECT * FROM `wipos_extras` WHERE `prod_id`=:id', array("id" => $product['prod_id']));
             //var_dump($results);
             echo '<ul>';
             foreach($results as $res){
              echo '<li>' . $res['name'] . '</li>';
             }
            echo '</div>
          </div>
            </div>
              </div>';
    }

    public function backSideFlipExtras($product)
    {
        echo ' <div class="back">
           <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-2 col-md-4 col-sm-4 food-image" id="product_pic' . $product['prod_id'] . '">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload product cp"
                id="productPic' . $product['prod_id'] . '">
                <span><a href="javascript:void(0);" onclick="WIMedia.changeProductPic(`' . $product['prod_id'] . '`)">Change PHoto</a></span>
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">
              <h3 class="food-title">
                <span class="food-name"><input type="text" id="prod_name' . $product['prod_id'] . '" value="' . $product['prod_name'] . '"></span>
                <span class="food-price float-right">
                ' .CURRENCY_SYMBOL . '<input type="text" id="prod_price' . $product['prod_id'] . '" value="' . $product['prod_price'] . '"></span>
               
              </h3>
              <p class="food-ingredients">
                <textarea id="prod_desc' . $product['prod_id'] . '" value="'. $product['prod_desc'] . '">'. $product['prod_desc'] . '</textarea>
              </p>
            </div>
            <a href="javascript:void(0);" id="productSave" onclick="WIProduct.saveProduct(`' . $product['prod_id'] . '`)">Save</a>
          </div>
            </div>
                  </div>';
    }



}