<?php



class WIproductReview
{


    function __construct(){
       $this->WIdb = WIdb::getInstance();
       $this->Page = new WIPagination();
    }

    public function OpenReview($id)
    {
        echo '<style>
        .rating-md {
    font-size: 3.13em;
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


        <div class="container">
    <div class="row" style="margin-top:40px;">
        <div class="col-md-12">
        <div class="well well-sm">
            <div class="text-right">
                <a class="btn btn-success btn-green" href="javascript:void(0);" id="open-review-box">Leave a Review</a>
            </div>
        
            <div class="row" id="post-review-box" style="display:none;">
                <div class="col-md-12">
                    
                    <form accept-charset="UTF-8" action="" method="post">
                        <input id="ratings-hidden" name="rating" type="hidden"> 
                        <textarea class="form-control animated" cols="50" id="new-review" name="comment" placeholder="Enter your review here..." rows="5"></textarea>
        
                        <div class="text-right">


                        <div>
    <br/>
    <label for="input-1" class="control-label">Give a rating for this product:</label>

    <div class="rating-container rating-md rating-animate">
        <div class="clear-rating clear-rating-active" title="Clear">
            <i class="glyphicon glyphicon-minus-sign"></i>
        </div>

        <div class="rating-stars">
        <span class="empty-stars">
            <span class="star" onmouseover="WIReview.starMark(this);" style="font-size:40px;cursor:pointer;" onclick="WIReview.starMark(this);" id="1one">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star" onmouseover="WIReview.starMark(this);" style="font-size:40px;cursor:pointer;" onclick="WIReview.starMark(this);" id="2one">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star" onmouseover="WIReview.starMark(this);" style="font-size:40px;cursor:pointer;" onclick="WIReview.starMark(this);" id="3one">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star" onmouseover="WIReview.starMark(this);" style="font-size:40px;cursor:pointer;" onclick="WIReview.starMark(this);" id="4one">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star" onmouseover="WIReview.starMark(this);" style="font-size:40px;cursor:pointer;" onclick="WIReview.starMark(this);" id="5one">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
        </span>
        <span class="filled-stars" style="width:0%;">
            <span class="star">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
            <span class="star">
            <i class="glyphicon glyphicon-star-empty"></i>
            </span>
        </span>
        </div>
    </div>
    <input id="input-1" name="input-1" class="rating rating-loading" data-min="0" data-max="5" data-step="0.1" value="2" type="hidden">
    <br/>
            <div class="caption">
                <span class="label label-warning">Two Stars</span>
            </div>
</div>

                            <div class="stars starrr" data-rating="0"></div>
                            <a class="btn btn-danger btn-sm" href="javascript:viod(0);" id="close-review-box" style="display:none; margin-right: 10px;">
                            <span class="glyphicon glyphicon-remove"></span>Cancel</a>
                            <button class="btn btn-success btn-lg" onclick="WIReview.reviewSave();">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div> 
         
        </div>
    </div>
</div>

';


echo "<script type='text/javascript'>
var rating = '';
$('#open-review-box').click(function(){

    $('#post-review-box').css('display', 'block');
    })

</script>";
    }


    public function saveReview($review, $new_rating, $product_id)
    {
        $user_id = WISession::get('user_id');
        $this->WIdb->insert('wi_product_review', array(
            "productId"     => $product_id,
            "rating"  => $new_rating,
            "content" => $review,
            "parentId" => $user_id
        )); 

        $result = array(
        "status" => "successful",
        "msg"   => " You have successfully reviewed this product."
        );
    }

    
}
