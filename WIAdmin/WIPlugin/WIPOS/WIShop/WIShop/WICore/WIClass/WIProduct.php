<?php



class WIProduct
{


    function __construct(){
       $this->WIdb = WIdb::getInstance();
       $this->Page = new WIPagination();
       $this->User = new WIUser(WISession::get('user_id'));
    }

    public function Product()
    {
         if(isset($_POST["page"])){
        $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
    }else{
        $page_number = 1; //if there's no page number, set it to 1
    }

        $item_per_page = 15;
        $result = $this->WIdb->select(
                    "SELECT * FROM `wi_product`");
        $rows = count($result);


        //break records into pages
        $total_pages = ceil($rows/$item_per_page);
        
        //get starting position to fetch the records
        $page_position = (($page_number-1) * $item_per_page);

        $sql = "SELECT * FROM `wi_product` ORDER BY RAND() ASC LIMIT :page, :item_per_page";
        $query = $this->WIdb->prepare($sql);
        $query->bindParam(':page', $page_position, PDO::PARAM_INT);
        $query->bindParam(':item_per_page', $item_per_page, PDO::PARAM_INT);
        $query->execute();

        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '  <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
        <a class="product_link" href="product.php" id="' . $result['id'] . '">
        <div class="panel panel-info" id="' . WISession::get('user_id') . '">
        <div class="panel-heading">' . $result['title'] . '</div>
        <div class="panel-body">
            <img src="../WIAdmin/WIMedia/Img/shop/products/' . $result['photo'] . '" class="img-responsive img img-fluid/>
        </div>
        <div class="panel-footer">' .CURRENCY_SYMBOL . '' . $result['price'] . '
        </div>
        </div></a>
    </div>';
        }

        $Pagin = $this->Page->Pagination($item_per_page, $page_number, $rows, $total_pages);
    //print_r($Pagination);


         echo '<div align="center">';
    /* We call the pagination function here to generate Pagination link for us. 
    As you can see I have passed several parameters to the function. */
    echo $Pagin;
    echo '</div>';
    }


    public function getProductInfor($id)
    {

        $result = $this->WIdb->select("SELECT * FROM wi_product WHERE `id`=:id",
            array(
            "id" => $id
             )
        );

        echo '<div id="product_msg"></div>
        <style>
        ul > li{margin-right:25px;font-weight:lighter;cursor:pointer}
        li.active{border-bottom:3px solid silver;}

        .item-photo{display:flex;justify-content:center;align-items:center;border-right:1px solid #f6f6f6;}
        .menu-items{list-style-type:none;font-size:11px;display:inline-flex;margin-bottom:0;margin-top:20px}
        .btn-success{width:100%;border-radius:0;}
        .section{width:100%;margin-left:-15px;padding:2px;padding-left:15px;padding-right:15px;background:#f8f9f9}
        .title-price{margin-top:30px;margin-bottom:0;color:black}
        .title-attr{margin-top:0;margin-bottom:0;color:black;}
        .btn-minus{cursor:pointer;font-size:7px;display:flex;align-items:center;padding:5px;padding-left:10px;padding-right:10px;border:1px solid gray;border-radius:2px;border-right:0;}
        .btn-plus{cursor:pointer;font-size:7px;display:flex;align-items:center;padding:5px;padding-left:10px;padding-right:10px;border:1px solid gray;border-radius:2px;border-left:0;}
        div.section > div {width:100%;display:inline-flex;}
        div.section > div > input {margin:0;padding-left:5px;font-size:10px;padding-right:5px;max-width:18%;text-align:center;}
        .attr,.attr2{cursor:pointer;margin-right:5px;height:20px;font-size:10px;padding:2px;border:1px solid gray;border-radius:2px;}
        .attr.active,.attr2.active{ border:1px solid orange;}
        </style>';

        foreach($result as $res){
            echo '<div class="col-xs-4 item-photo">
                    <img style="max-width:100%;" src="../../WIAdmin/WIMedia/Img/shop/products/' . $res['photo']. '" />
                </div>
                <div class="col-xs-5" style="border:0px solid gray">
                   
                    <h3>'  . $res['title'] . '</h3>    
                    <h5 style="color:#337ab7">
                     <a href="#">'  . self::brandSelector($res['brand_id']) . '</a> · <small style="color:#337ab7"></small></h5>
        
                    <!-- Precios -->
                    <h6 class="title-price"><small>PRICE</small></h6>
                    <h3 style="margin-top:0px;">' .CURRENCY_SYMBOL . ''  . $res['price'] . '</h3>
        
                    <!-- Detalles especificos del producto -->
                    <div class="section">
                        <h6 class="title-attr" style="margin-top:15px;" ><small>COLOR</small></h6>                    
                        <div>
                            <div class="attr" style="width:25px;background:#5a5a5a;"></div>
                            <div class="attr" style="width:25px;background:white;"></div>
                        </div>
                    </div>
                    <div class="section" style="padding-bottom:5px;">
                        <h6 class="title-attr"><small></small></h6>                    
                        <div>
                            <div class="attr2">16 GB</div>
                            <div class="attr2">32 GB</div>
                        </div>
                    </div>   
                    <div class="section" style="padding-bottom:20px;">
                        <h6 class="title-attr"><small></small></h6>                    
                        <div>
                            <div class="btn-minus"><span class="glyphicon glyphicon-minus"></span></div>
                            <input id="quantity" value="1" />
                            <div class="btn-plus"><span class="glyphicon glyphicon-plus"></span></div>
                        </div>
                    </div>                
        
                    <div class="section" style="padding-bottom:20px;">
                        <button class="btn btn-success" id="product" product="'  . $res['id'] . '">
                        <span style="margin-right:20px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Add to cart</button>
                        <h6>
                        <a href="#"><span class="glyphicon glyphicon-heart-empty" style="cursor:pointer;"></span> Add to Wishlists</a></h6>
                    </div>                                        
                </div> 

                <script type="text/javascript">

                   $(document).ready(function(){
            //-- Click on detail
            $("ul.menu-items > li").on("click",function(){
                $("ul.menu-items > li").removeClass("active");
                $(this).addClass("active");
            })

            $(".attr,.attr2").on("click",function(){
                var clase = $(this).attr("class");

                $("." + clase).removeClass("active");
                $(this).addClass("active");
            })

            //-- Click on QUANTITY
            $(".btn-minus").on("click",function(){
                var now = $("#quantity").val();

               if ($.isNumeric(now)){
                    if (parseInt(now) -1 > 0){ now--;}
                    $("#quantity").attr("value",now);
                }else{
                    $("#quantity").attr("value","1");
                }
            })            
            $(".btn-plus").on("click",function(){
            var now = $("#quantity").val();

            console.log(now);
                if ($.isNumeric(now)){
                    console.log(parseInt(now)+1);
                    $("#quantity").attr("value",parseInt(now)+1);
                }else{
                    $("#quantity").attr("value","1");
                }
            })                        
        }) 
                </script>';
        }

    }

    public function brandSelector($id)
    {
        $brand = $this->WIdb->select("SELECT * FROM wi_brands WHERE `brand_id`=:id",
            array(
            "id" => $id
             )
        );

        return $brand['title'];
    }


    public function getProductOverView($id)
    {
        $result = $this->WIdb->select("SELECT * FROM wi_product WHERE `id`=:id",
            array(
            "id" => $id
             )
        );
        //var_dump($result);
        $summary = $result[0]['summary'];
        echo  $summary;
    }


    public function getProductReviews($id)
    {
        $result = $this->WIdb->select("SELECT * FROM wi_product_review WHERE `productId`=:id",
            array(
            "id" => $id
             )
        );
        //var_dump($result);
        $len = count($result);
        if(count($result) < 1 ){
            echo 'there are currently no reviews to show, be the first to leave a review <a href="javascript:void(0)" onclick="WIProducts.OpenReview(`'.$id.'`);" class="btn">Leave a review</a>.';
        }else{
            foreach($result as $res){
                           echo '
            <style>
            }
                .reviews{
                  padding: 15px;
                  max-width: 768px;
                  margin: 0 auto;
                }

                .review-item{
                  background-color: white;
                  padding: 15px;
                  margin-bottom: 5px;
                  box-shadow: 1px 1px 5px #343a40;
                }

                .review-item .review-date{
                  color: #cecece;
                }
                .review-item .review-text{
                  font-size: 16px;
                  font-weight: normal;
                  margin-top: 5px;
                  color: #343a40;
                }

                .review-item .reviewer{
                  width: 100px;
                  height: 100px;
                  border: 1px solid #cecece;
                }

                /****Rating Stars***/
                .raterater-bg-layer {
                    color: rgba( 0, 0, 0, 0.25 );
                }
                .raterater-hover-layer {
                    color: rgba( 255, 255, 0, 0.75 );
                }
                .raterater-hover-layer.rated { /* after the user selects a rating */
                    color: rgba( 255, 255, 0, 1 );
                }
                .raterater-rating-layer {
                    color: rgba( 255, 155, 0, 0.75 );
                }
                .raterater-outline-layer {
                    color: rgba( 0, 0, 0, 0.25 );
                }

                /* dont change these - you might break something.. */
                .raterater-wrapper {
                    overflow:visible;
                }

                .software .raterater-wrapper {
                    margin-top: 4px;
                }

                .raterater-layer,
                .raterater-layer i {
                    display: block;
                    position: absolute;
                    overflow: visible;
                    top: 0px;
                    left: 0px;
                }
                .raterater-hover-layer {
                    display: none;
                }
                .raterater-hover-layer i,
                .raterater-rating-layer i {
                    width: 0px;
                    overflow: hidden;
                }

                .rating-container .clear-rating {
                color: #aaa;
                cursor: not-allowed;
                display: inline-block;
                vertical-align: middle;
                font-size: 60%;
                padding-right: 5px;
            }

            .clear-rating-active {
                cursor: pointer!important;
            }

            .glyphicon-minus-sign:before {
                content: "\e082";
            }

            :before, :after {
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }


            .glyphicon {
                position: relative;
                top: 1px;
                display: inline-block;
                font-family: `Glyphicons Halflings`;
                font-style: normal;
                font-weight: 400;
                line-height: 1;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }


            .rating-container .rating-stars {
                position: relative;
                cursor: pointer;
                vertical-align: middle;
                display: inline-block;
                overflow: hidden;
                white-space: nowrap;
            }


            .rating-container .empty-stars {
                color: #aaa;
            }

            .rating-container .star {
                display: inline-block;
                margin: 0 3px;
                text-align: center;
            }

            .glyphicon-star-empty:before {
                content: "\e007";
            }

            .rating-animate .filled-stars {
                transition: width .25s ease;
                -o-transition: width .25s ease;
                -moz-transition: width .25s ease;
                -webkit-transition: width .25s ease;
            }

            .rating-container .filled-stars {
                position: absolute;
                left: 0;
                top: 0;
                margin: auto;
                color: #fde16d;
                white-space: nowrap;
                overflow: hidden;
                -webkit-text-stroke: 1px #777;
                text-shadow: 1px 1px #999;
            }

            .rating-container .rating-input {
                position: absolute;
                cursor: pointer;
                width: 100%;
                height: 1px;
                bottom: 0;
                left: 0;
                font-size: 1px;
                border: none;
                background: 0 0;
                padding: 0;
                margin: 0;
            }

            .rating-container .caption {
                color: #999;
                display: inline-block;
                vertical-align: middle;
                font-size: 60%;
                margin-top: -.6em;
                margin-left: 5px;
                margin-right: 0;
            }

            .label-warning {
                background-color: #f0ad4e;
            }

            </style>
            <div class="reviews">
              <div class="row blockquote review-item">
                <div class="col-md-3 text-center">
                  <img class="rounded-circle reviewer" src="../../WIAdmin/WIMedia/Img/avator/'. $this->User->id().'/' .self::getUserPhoto($res['id']) . '">
                  <div class="caption">
                    <small>by <a href="javascript:void(0);">' .self::getUsername($res['parentId']) . '</a></small>
                  </div>

                </div>
                <div class="col-md-9">
                  <h4>' . $res['title'].'</h4>
                  <div class="ratebox text-center" data-id="0" data-rating="5">

                  <div class="rating-container rating-md rating-animate">
                <div class="rating-stars">
                <span class="empty-stars">
                    <span class="star" style="font-size:40px;" id="1one">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;" id="2one">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;" id="3one">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;" id="4one">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;" id="5one">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                </span>
                <span class="filled-stars" id="' .$res['id'] . '" style="width:0%;">
                    <span class="star" style="font-size:40px;">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;" >
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                    <span class="star" style="font-size:40px;">
                    <i class="glyphicon glyphicon-star-empty"></i>
                    </span>
                </span>
                </div>
                </div>
                <input id="star_rating_'. $res['id'] . '" name="input-1" class="rating rating-loading" data-min="0" data-max="5" data-step="0.1" value="' .$res['rating'] .'" type="hidden">
                  <p class="review-text">' . $res['content'] . '</p>

                  <small class="review-date">March 26, 2017</small>
                </div>                          
              </div>  
            </div>';

            echo '<script type="text/javascript">

                var rating = $("#star_rating_' . $res['id'] . '").val();

                if(rating == 1){
                    $("#' .$res['id'].'").css("width", "20%");
                    }else if(rating == 2){
                        $("#' .$res['id'].'").css("width", "40%");
                    }else if(rating == 3){
                        $("#' .$res['id'].'").css("width", "60%");
                        }else if(rating == 4){
                            $("#' .$res['id'].'").css("width", "80%");
                            }else if(rating == 5){
                                $("#' .$res['id'].'").css("width", "100%");
                            }
                
            </script>';
            }
 
            }
        

    }


    public function getUsername($id) 
    {
        $result = $this->WIdb->select(
                    "SELECT * FROM `wi_members` WHERE `user_id` = :id",
                    array ("id" => $this->User->id() )
                  );
        if ( count($result) > 0 )
            return $result[0]['username'];
        else
            return "user";
    }


    public function getUserPhoto($id) 
    {
    $result = $this->WIdb->select(
                    "SELECT * FROM `wi_user_details` WHERE `user_id` = :id",
                    array ("id" => $this->User->id() )
                  );
        if ( count($result) > 0 )
            return $result[0]['avatar'];
        else
            return "user";
    }
}
