<?php
/**
* resource Class
* Created by Warner Infinity
* Author Jules Warner
*/
class WIBlog
{
	function __construct() 
	{
       $this->WIdb = WIdb::getInstance();
       $this->Comment = new WIComment();
       $this->Page    = new WIPage();
       $this->Mod     = new WIModules();
    }


    public function Cat()
	 {


    $result = $this->WIdb->select("SELECT * FROM wi_blogcategories");

  	echo '<div class="title_widget">									
  			<h3>Categories</h3>								
  			</div>								
  			<ul class="arrows_list">';
  	foreach($result as $res){
  		echo '<li><a href="javascript:void(0);" class="category" cid="' . $res['cat_id']. '"><i class="fa fa-angle-right"></i> ' . $res['title'] . '<span></span></a></li>';
  		}
  	echo '</ul>';
	}

	public function SelectCategory()
	{

    $result = $this->WIdb->select("SELECT * FROM wi_blogcategories");

		echo '<label for="Category">Category</label>
    <select name="Category" id="cat"><option value="" selected="selected">Select Category</option> ';

		foreach($result as $res){
			echo '<option value="' . $res['cat_id'] . '">' . $res['title'] . '</option>';
		}
		echo ' </select>';

	}


		public function selectedCat($cid)
	{

		 if(isset($_POST["page"])){
        $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
    }else{
        $page_number = 1; //if there's no page number, set it to 1
    }

        $item_per_page = 8;

        $result = $this->WIdb->select(
                    "SELECT * FROM `wi_blog`");
        $rows = count($result);

        //break records into pages
        $total_pages = ceil($rows/$item_per_page);

                //get starting position to fetch the records
        $page_position = (($page_number-1) * $item_per_page);


        $result = $this->WIdb->select(
                    "SELECT * FROM wi_blog WHERE Category = :cid",
                     array(
                       "cid" => $cid
                     )
                  );

        foreach($result as $res){
          $type = $res['type'];
          $date = $res['day'];

          if($type == "NoMedia"){
            self::NoMedia();
          }else if($type == "mediaSlider"){
            self::mediaSlider();
          }else if($type == "mediaVideo"){
            self::mediaVideo();
          }else if($type == "blog_image"){
            self::blogImage();
          }else if($type == "mediaaudio"){
            self::mediaAudio();
          }else if($type == "mediaYoutube")
            self::mediaYoutube();
        }
	}


		public function NoMedia($media)
    {
     
      echo '<article class="post_container">                             
       <div class="post-info">                                    
       <div class="post-date">                                        
       <span class="day">' . $media['day'] . '</span>                                        
       <span class="month">' . $media['month'] . '</span>                                    
       </div>                                    
       <div class="post-category">
       ' . self::blogCat($media['Category']) .'                            
       </div>                               
        </div>
        <div class="blog_no_media">
        <p style="font-size: 36px;margin-top: 38px;">                                      
             ' . $media['post'] . '                                  
         </p>
         </div>
         <div class="blog-meta">                                        
          <ul>                                            
          <li class="fa fa-user">                                               
           <a href="#">' . $media['user'] . '</a>                                            
           </li>                                            
           <li class="post-tags fa fa-tags">                                                
           <a href="#">news, </a>                                               
            <a href="#">dois</a>                                            
            </li>                                        
            </ul>                                   
             </div>           ';  
  self::userInteract($media['id']);                             
   echo '<div class="post-content">                                   
         <a href="javascript:void(0)"  onclick="WIBlog.postPage(`'. $media['title'] .'`,`' . $media['id'] . '`);">                                           
     <h4>' . $media['title'] . '</h4>                                    
     </a>                            
             </div>                        
              </article>';


    }

    public function mediaAudio()
    {

    }

    public function mediaVideo($media)
    {
      
        echo '<article class="post_container">                              
<div class="post-info">                                   
 <div class="post-date">                                        
 <span class="day">' . $media['day'] . '</span>                                        
 <span class="month">' . $media['month'] . '</span>                                    
 </div>                                   
   <div class="post-category">
   ' . self::blogCat($media['Category']) .'                                  
    </div>                               
  </div><!-- .post-info end -->                                
  <figure class="post-video">                                    
  <video width="100%" height="600" controls>
  <source src="../WIAdmin/WIMedia/Vid/blog/' . $media['video'] . '" type="video/mp4">
</video>                                
  </figure><div class="blog-meta">                                        
   <ul>                                            
   <li class="fa fa-user">                                                
   <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
   </li>                                            
   <li class="post-tags fa fa-tags">                                                
   <a href="javascript:void(0)">news, </a>                                                
   <a href="javascript:void(0)">dois</a>                                            
   </li>                                        
   </ul>                                    
   </div>';  
  self::userInteract($media['id']);                             
   echo '<div class="post-content">                                    
      <a href="javascript:void(0)"  onclick="WIBlog.postPage(`'. $media['title'] .'`,`' . $media['id'] . '`);">                                           
     <h4>' . $media['title'] . '</h4>                                    
     </a>                              
    <p>                                     
    ' . $media['post'] . '                                 
    </p>                                    
     </div>                        
         </article>';
      
    }

    public function mediaSlider($media)
    {

        echo '<!-- .latest-posts start -->                            
<article class="post_container">                              
<div class="post-info">                                   
 <div class="post-date">                                        
 <span class="day">' . $media['day'] . '</span>                                        
 <span class="month">' . $media['month'] . '</span>                                   
  </div>                                    
  <div class="post-category">
  ' . self::blogCat($media['Category']) .'                                     
  </div>                                
  </div><!-- .post-info end -->                               
   <figure class="post-image">                                  
   <div class="slideshow-container">

<div class="mySlides-' . $media['id'] . '">
  <div class="numbertext"></div>
   <img src="../WIAdmin/WIMedia/Img/blog/revslider/' . $media['image'] . '" style="width:100%;height:500px;">
  <div class="text">' . $media['caption'] . '</div>
</div>

<div class="mySlides-' . $media['id'] . '">
  <div class="numbertext"></div>
<img src="../WIAdmin/WIMedia/Img/blog/revslider/' . $media['image2'] . '" style="width:100%;height:500px;">        
  <div class="text">' . $media['caption1'] . '</div>
</div>

<div class="mySlides-' . $media['id'] . '">
  <div class="numbertext"></div>
<img src="../WIAdmin/WIMedia/Img/blog/revslider/' . $media['image3'] . '" style="width:100%;height:500px;">
  <div class="text">' . $media['caption2'] . '</div>
</div>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

</div>
<br>

<div style="text-align:center">
  <span class="dot-' . $media['id'] . '" onclick="currentSlide(1)"></span>
  <span class="dot-' . $media['id'] . '" onclick="currentSlide(2)"></span> 
  <span class="dot-' . $media['id'] . '" onclick="currentSlide(3)"></span>
</div>                                             
   </figure><div class="blog-meta">                                        
    <ul>                                            
    <li class="fa fa-user">                                                
    <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
    </li>                                           
     <li class="post-tags fa fa-tags">                                               
      <a href="javascript:void(0)">news, </a>                                                
      <a href="javascript:void(0)">dois</a>                                            
      </li>                                        
</ul>                                    
</div>';  
  self::userInteract($media['id']);                             
   echo '<div class="post-content">                                    
    <a href="javascript:void(0)"  onclick="WIBlog.postPage(`'. $media['title'] .'`,`' . $media['id'] . '`);">                                           
     <h4>' . $media['title'] . '</h4>                                    
     </a>                                                                    
<p>                                        
' . $media['post'] . '                                  
</p>                                    
 </div>                         
         </article>
         <style>
         /* Hide the images by default */
        .mySlides-' . $media['id'] . ' {
          display: none;
        }

        /* The dots/bullets/indicators */
        .dot-' . $media['id'] . ' {
          cursor: pointer;
          height: 15px;
          width: 15px;
          margin: 0 2px;
          background-color: #bbb;
          border-radius: 50%;
          display: inline-block;
          transition: background-color 0.6s ease;
        }
         </style>
         <script type="text/javascript">
            var slideIndex = 1;
            showSlides(slideIndex);

            // Next/previous controls
            function plusSlides(n) {
              showSlides(slideIndex += n);
            }

            // Thumbnail image controls
            function currentSlide(n) {
              showSlides(slideIndex = n);
            }

            function showSlides(n) {
              var i;
              var slides = document.getElementsByClassName("mySlides-' . $media['id'] . '");
              var dots = document.getElementsByClassName("dot-' . $media['id'] . '");
              if (n > slides.length) {slideIndex = 1}
              if (n < 1) {slideIndex = slides.length}
              for (i = 0; i < slides.length; i++) {
                  slides[i].style.display = "none";
              }
              for (i = 0; i < dots.length; i++) {
                  dots[i].className = dots[i].className.replace(" active", "");
              }
              slides[slideIndex-1].style.display = "block";
              dots[slideIndex-1].className += " active";
            }

            showSlides();


            function showSlides() {
  var i;
  var slides = document.getElementsByClassName("mySlides-' . $media['id'] . '");
  var dots = document.getElementsByClassName("dot-' . $media['id'] . '");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  slideIndex++;
  if (slideIndex > slides.length) {slideIndex = 1}    
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  console.log()
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
  setTimeout(showSlides, 4000); // Change image every 4 seconds
}
  </script>';
      
    }

    public function mediaYoutube($media)
    {
        echo '<!-- .latest-posts start -->                            
    <article class="post_container">                              
    <div class="post-info">                                    
    <div class="post-date">                                        
    <span class="day">' . $media['day']. '</span>                                        
    <span class="month">' . $media['month'] .'</span>                                    
    </div>                                    
    <div class="post-category">
    ' . self::blogCat($media['Category']) .'                                     
    </div>                                
    </div><!-- .post-info end -->                                
    <figure class="post-video">                                    
   ' . $media['youtube'] .'      
     </figure><div class="blog-meta">                                        
     <ul>                                           
      <li class="fa fa-user">                                                
      <a href="javascript:void(0);">' . $media['user'] . '</a>                                            
      </li>                                            
      <li class="post-tags fa fa-tags">                                                
      <a href="#">news, </a>                                                
     <a href="#">dois</a>                                            
      </li> 

      <li>Lke</li>                                       
    </ul>                                    
    </div> ';  
  self::userInteract($media['id']);                             
   echo '<div class="post-content">                                    
     <a href="javascript:void(0)"  onclick="WIBlog.postPage(`'. $media['title'] .'`,`' . $media['id'] . '`);">                                           
     <h4>' . $media['title'] . '</h4>                                    
     </a>                                    
                                        
    <p>                                     
    ' . $media['post'] . '                                 
    </p>                                                               
    </div>                        
    </article>
      <!-- .blog-post end -->';
   }

    public function blogImage($media)
    {

        echo '<article class="post_container">                              
        <div class="post-info">                                    
        <div class="post-date">                                        
        <span class="day">' . $post['day'] . '</span>                                        
        <span class="month">' . $post['month'] . '</span>                                    
        </div>                                    
        <div class="post-category"> 
        ' . self::blogCat($media['Category']) .'
        </div>                                
        </div>                                
        <figure class="post-image">                 
        <a href="#"><img src="WIMedia/Img/blog/' . $post['image'] . '" alt=""></a>                
        </figure>
        <div class="blog-meta">                                        
         <ul>                                            
         <li class="fa fa-user">                                                
         <a href="#">' . $post['user'] . '</a>                                           
          </li>                                           
           <li class="post-tags fa fa-tags">                                               
            <a href="#">news, </a>                                                
            <a href="#">dois</a>                                           
             </li>                                        
             </ul>                                    
             </div>';  
  self::userInteract($media['id']);                             
   echo '<div class="post-content">                                    
          <a href="javascript:void(0)"  onclick="WIBlog.postPage(`'. $media['title'] .'`,`' . $media['id'] . '`);">                                           
     <h4>' . $media['title'] . '</h4>                                    
     </a>                                    
          <p>                                      
             ' . $post['post'] . '                               
             </p>                                                               
             </div>                         
             </article>';
        
    }





		public function Resource()
	  {

      $result = $this->WIdb->select(
                    "SELECT * FROM wi_resources ORDER BY RAND() LIMIT 0,9");


		foreach($result as $res){
			echo '	<div class="col-md-4 col-lg-4 col-sm-4">
		<div class="panel panel-info">
		<div class="panel-heading">' . $res['title'] . '</div>
		<div class="panel-body">
			<img src="WIMedia/Img/resources/' . $res['image'] . '" style="width:160px;height:250px;"/>
		</div>
		<div class="panel-footer">' . $res['publish_date'] . '
			<button rid="' . $res['resource_id'] . '" onclick="WIResources.Source(' . $res['resource_id'] . ')" style="float:right;" class="btn btn-danger btn-xs" id="OSDP_resource">View Resource</button>
		</div>
		</div>
	</div>';
		}
	}

	public function InsertnoMedia($PostNoMedia)
	{
    $data = $PostNoMedia['PostData'];
     $this->WIdb->insert('wi_blog', array(
            "type"     => $data['type'],
            "day"  => $data['day'],
            "month"  => $data['month'],
            "title" => strip_tags($data['title']),
            "user" => $data['user'],
            "post" => $data['post'],
            "Category"  => $data['Category']
            ));

		 $msg = "Successfully added to db";

		 $result = array(
		 	"status" => "complete",
		 	"msg"    => $msg
		 );

		 echo json_encode($result);

	}


		public function blogPostImage($PostImage)
	{
		//echo $type;
		 $this->WIdb->insert('wi_blog', array(
            "type"     => $data['type'],
            "day"  => $data['day'],
            "month"  => $data['month'],
            "title" => strip_tags($data['title']),
            "user" => $data['user'],
            "post" => $data['post'],
            "Category"  => $data['Category']
            ));

	}

	public function blogPostSlider($postSlider)
	{
		 $data = $postSlider['PostData'];
    //echo $type;
     $this->WIdb->insert('wi_blog', array(
            "type"     => $data['type'],
            "day"  => $data['day'],
            "month"  => $data['month'],
            "title" => strip_tags($data['title']),
            "image" => $data['image'],
            "image2" => $data['image2'],
            "image3" => $data['image3'],
            "caption" => $data['caption'],
            "caption1" => $data['caption1'],
            "caption2" => $data['caption2'],
            "user" => $data['user'],
            "post" => $data['post'],
            "Category"  => $data['Category']
            ));

     $blog_id = $this->WIdb->lastInsertId();

     if(count($blog_id) > 0){
          //create main blog page for the main post
     $page = $postSlider['PostData']['title'];
     $this->Page->newPage($page);

     $msg = "Successfully added to db";

     $result = array(
      "status" => "complete",
      "msg"    => $msg
     );

     echo json_encode($result);
     }else{

     $msg = "Something went wrong";

     $result = array(
      "status" => "error",
      "msg"    => $msg
     );

     echo json_encode($result);
     }


	}

	public function blogPostAudio($PostAudio)
	{
		//echo $type;
		 $this->WIdb->insert('wi_blog', $PostAudio);

	}

	public function blogPostVideo($PostVideo)
	{
    $data = $PostVideo['PostData'];
		//echo $type;
		 $this->WIdb->insert('wi_blog', array(
            "type"     => $data['type'],
            "day"  => $data['day'],
            "month"  => $data['month'],
            "title" => strip_tags($data['title']),
            "video" => $data['video'],
            "user" => $data['user'],
            "post" => $data['post'],
            "Category"  => $data['Category']
            ));

     $blog_id = $this->WIdb->lastInsertId();

     if(count($blog_id) > 0){
          //create main blog page for the main post
     $page = $PostVideo['PostData']['title'];
     $this->Page->newPage($page);

     $msg = "Successfully added to db";

     $result = array(
      "status" => "complete",
      "msg"    => $msg
     );

     echo json_encode($result);
     }else{

     $msg = "Something went wrong";

     $result = array(
      "status" => "error",
      "msg"    => $msg
     );

     echo json_encode($result);
     }

    
	}

	public function YoutubeMedia($PostYouTube)
	{
     $data = $PostYouTube['PostData'];
      // insert blog post into db
         $this->WIdb->insert('wi_blog', array(
            "type"     => $data['type'],
            "day"  => $data['day'],
            "month"  => $data['month'],
            "title" => strip_tags($data['title']),
            "youtube" => $data['ytlink'],
            "user" => $data['user'],
            "post" => $data['post'],
            "Category"  => $data['Category']
            ));

     //create main blog page for the main post
     $page = $PostYouTube['PostData']['title'];
     $this->Page->newPage($page);

     // install module into db from template & create the module and assocated files


     //$this->Mod->save_mod($page, $editcontent, $content)
     

		 $msg = "Successfully added to db";

		 $result = array(
		 	"status" => "complete",
		 	"msg"    => $msg
		 );

		 echo json_encode($result);

	}

	public function Search($keywords)
	{

          $result = $this->WIdb->select(
                    "SELECT * FROM wi_resources WHERE keywords LIKE :keyword",
                     array(
                       "keyword" => $keywords
                     )
                  );


    		foreach($result as $res){
    			echo '	<div class="col-md-4 col-lg-8 col-sm-4">
    		<div class="panel panel-info">
    		<div class="panel-heading">' . $res['title'] . '</div>
    		<div class="panel-body">
    			<img src="WIMedia/Img/resources/' . $res['image'] . '" style="width:160px;height:250px;"/>
    			
    		</div>
    		<div class="panel-heading">' . $res['publish_date'] . '
    			<button pid="' . $res['resource_id'] . '" style="float:right;" class="btn btn-danger btn-xs" id="product">Add to cart</button>
    		</div>
    		</div>
    	</div>';
		}
	}

	public function getResource($rid)
	{

        $result = $this->WIdb->select(
              "SELECT * FROM wi_resources WHERE resource_id =:rid",
               array(
                 "rid" => $rid
               )
            );



		foreach($result as $res){

			echo '<div class="row">
          <div class="span5">
            <div id="items-carousel" class="carousel slide mbottom0">
              <div class="carousel-inner">
                <div class="active item">
                  <img class="media-object" id="media-object" src="WIMedia/Img/resources/' . $res['image'] . '" style="width:160px;height:250px;" alt="" />
                </div>

                <div class="item">
                  <img class="media-object" src="" alt="" />
                </div>

                <div class="item">
                  <img class="media-object" src="" alt="" />
                </div>
              </div>
              <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
              <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
            </div>
          </div>

          <div class="span4">
            <h4 id="brand">' . WIResources::catType($res['cat'])  . '</h4>
            <h5 id="name" class="item_name">' . $res['title'] . '</h5>
            <p id="descript"></p>
            
              <a href="WIMedia/Resources/' . $res['dl_link'] . '" class="btn btn-primary" id="osdp" onclick="WIResources.Download(event)">View & download</a>
            
            
            
          </div>
        </div>';
		}
		

	
	}

	public function Share()
	{
		echo '<ul class="shares">									
		<li class="shareslabel"><h3>Share</h3></li>									
		<li><a title="" data-toggle="tooltip" data-placement="top" href="#" class="twitter" data-original-title="Twitter"></a></li>	
		<li><a title="" data-toggle="tooltip" data-placement="top" href="#" class="facebook" data-original-title="Facebook"></a></li>									
		<li><a title="" data-toggle="tooltip" data-placement="top" href="#" class="gplus" data-original-title="Google Plus"></a></li>									
		<li><a title="" data-toggle="tooltip" data-placement="top" href="#" class="pinterest" data-original-title="Pinterest"></a></li>									
		<li><a title="" data-toggle="tooltip" data-placement="top" href="#" class="yahoo" data-original-title="Yahoo"></a></li>									
		<li><a title="" data-toggle="tooltip" data-placement="top" href="#" class="linkedin" data-original-title="LinkedIn"></a></li>								</ul>';
	}

	public function catType($cat_id)
	{
		 $result = $this->WIdb->select(
                    "SELECT `title` FROM `wi_categories`
                     WHERE `cat_id` = :cat_id",
                     array(
                       "cat_id" => $cat_id
                     )
                  );
		//var_dump($result) ;
		  if ( count ( $result ) > 0 )
            return $result[0]['title'];
        else
            return null;
	}


	public function OSDPResource($rid, $column)
	{
     $result = $this->WIdb->select(
                    "SELECT * FROM wi_resources WHERE resource_id =:rid",
                     array(
                       "rid" => $rid
                     )
                  );

		echo $result[0][$column];
	}

	public function hasPosts()
	{
		
		$res = $this->WIdb->select('SELECT * FROM `wi_blog` ORDER BY day DESC');
		//print_r($res);
		if(count($res) < 1){
			echo "No Posts Yet.";
		}else{

		foreach ($res as $media) {
		
		if($media['type'] === "NoMedia"){
				
        self::NoMedia($media);
				
		}
	    if($media['type'] === "blog_slider"){
			self::mediaSlider($media);
		}

		if($media['type'] === "blog_video"){
			self::mediaVideo($media);
			
		}
		//$type = "blog_image";
		if($media['type'] === "blog_image"){
			
				self::blogImage($media);
		}

		if($media['type'] === "blog_audio"){
			
			self::mediaAudio($media);
		}

		if($media['type'] === "blog_youtube"){
			
      $link = $media['title'];
      if( strpos($link, " ") !== false )
      {
        $link = preg_replace('/\s+/', '_', $link);
      }
		  self::mediaYoutube($media);
			  
          }
      }
		}
	}


  public function blogCat($id)
  {
            $result = $this->WIdb->select(
                    "SELECT * FROM `wi_blogcategories`
                     WHERE `cat_id` = :id",
                     array(
                       "id" => $id
                     )
                  );
        //print_r($result);
        
        if(count($result) > 0) 
        {
          return $result[0]['title'];
        }else{

        }
  }



  /* User interactions */

  public function userInteract($postId)
  {
    echo '<div id="user_interact">
            <ul class="user_interact">
                <li class="ui" onclick="WIBlog.thumbsUp(`'.$postId.'`);">
              <span>';
              $utustatus = self::likingChecker($postId, "thumbs_up");
              if($utustatus == "true"){
                echo '<img id="tus'.$postId.'" title="LIKE" src="../WIAdmin/WIMedia/Img/user_interact/tu_clicked.jpg" style="width:16px;height:16px;"><span class="badge" id="cartBadge">'; echo  self::thumbUpCount($postId); 
                 echo '</span>';
              }else{
                echo '<img id="tus'.$postId.'" title="LIKE" src="../WIAdmin/WIMedia/Img/user_interact/tu_unclicked.png" style="width:16px;height:16px;"><span class="badge" id="cartBadge">'; echo  self::thumbUpCount($postId); 
                 echo '</span>';
              }
              echo '</span></li>

              <li class="ui" onclick="WIBlog.thumbsDown(`'.$postId.'`);">
              <span>';
              $utdstatus = self::likingChecker($postId, "thumbs_down");
              if($utdstatus == "true"){
                echo '<img id="tds'.$postId.'" title="DISLIKE" src="../WIAdmin/WIMedia/Img/user_interact/td_clicked.png" style="width:16px;height:16px;"><span class="badge" id="cartBadge">'; echo  self::thumbDownCount($postId); 
                 echo '</span>';
              }else{
                echo '<img id="tds'.$postId.'" title="DISLIKE" src="../WIAdmin/WIMedia/Img/user_interact/td_unclicked.jpg" style="width:16px;height:16px;"><span class="badge" id="cartBadge">'; echo  self::thumbDownCount($postId); 
                 echo '</span>';
              }
              echo '</span></li>

                <li class="ui" onclick="WIBlog.Love(`'.$postId.'`);">
              <span>';
              $utdstatus = self::likingChecker($postId, "love");
              if($utdstatus == "true"){
                echo '<img id="love'.$postId.'" title="LOVE" src="../WIAdmin/WIMedia/Img/user_interact/love_clicked.jpg" style="width:16px;height:16px;"><span class="badge" id="cartBadge">'; echo  self::loveCount($postId); 
                 echo '</span>';
              }else{
                echo '<img id="love'.$postId.'" title="LOVE" src="../WIAdmin/WIMedia/Img/user_interact/love_unclicked.png" style="width:16px;height:16px;"><span class="badge" id="cartBadge">'; echo  self::loveCount($postId); 
                 echo '</span>';
              }
              echo '</span></li>
              </ul></div>';
  }

    public function likingChecker($id, $like)
  {
    $user_id = WISession::get('user_id');

    if(!$user_id > 0){
      return "false";
    }else{



    $tustatus = $this->WIdb->select("SELECT * FROM `wi_user_interact` WHERE `user_id`=:user_id AND `blog_id`=:blog_id", array("user_id" => $user_id, "blog_id" => $id));
   
   $count = count($tustatus);
   if($count > 0){
    $status = $tustatus[0][$like];
   return $status;

   }else{
    $status = "false";
    return $status;
   }
   
    }

  }


   public function thumbUp($id)
  {
    $user_id = WISession::get('user_id');
    if($user_id == "null" || !$user_id  > "0"){
      $res = array(
          "status" => "failed",
          "tdstatus"   => "login"
        );
      echo json_encode($res);
    }else{
    $tustatus = $this->WIdb->select("SELECT * FROM `wi_user_interact` WHERE `user_id`=:user_id AND `blog_id`=:blog_id", array("user_id" => $user_id, "blog_id" => $id));

    $count = count($tustatus);
    //echo $count;
    if($count > 0){

      $status = $tustatus[0]['thumbs_up'];
      $cid    = $tustatus[0]['id'];
      //echo $status;
    if($status == "true"){
      $s = "false";
      $this->WIdb->update(
                    "wi_user_interact", 
                    array(
                      "thumbs_up" => $s
                    ), 
                    "`id`=:id",
                    array( "id" => $cid )
               );

      $res = array(
          "status" => "success",
          "tustatus"   => "false",
          "src"   => "../WIAdmin/WIMedia/Img/user_interact/tu_unclicked.png"
        );
      echo json_encode($res);

    }else{
       $s = "true";
      $this->WIdb->update(
                    "wi_user_interact", 
                    array(
                      "thumbs_up" => $s
                    ), 
                    "`id`=:id",
                    array( "id" => $cid )
               );

      $res = array(
          "status" => "success",
          "tustatus"   => "true",
          "src"   => "../WIAdmin/WIMedia/Img/user_interact/tu_clicked.jpg"
        );
      echo json_encode($res);

    }

    }else{
       $this->WIdb->insert('wi_user_interact', array(
            "user_id"  => $user_id,
            "blog_id"  => $id,
            "thumbs_up" => "true"
          )); 
    }
  }
    

  }

  public function thumbDown($id)
  {
        $user_id = WISession::get('user_id');

        if($user_id == "null" || !$user_id  > "0"){
      $res = array(
          "status" => "failed",
          "tdstatus"   => "login"
        );
      echo json_encode($res);
    }else{
    $tustatus = $this->WIdb->select("SELECT * FROM `wi_user_interact` WHERE `user_id`=:user_id AND `blog_id`=:blog_id", array("user_id" => $user_id, "blog_id" => $id));

    $count = count($tustatus);

    if($count > 0){

    $status = $tustatus[0]['thumbs_down'];
    $cid    = $tustatus[0]['id'];

    if($status == "true"){
      $this->WIdb->update(
                    "wi_user_interact", 
                    array(
                      "thumbs_down" => "false")
                    , 
                    "`id` =:id",
                    array( "id" => $cid )
               );
      $res = array(
          "status" => "success",
          "tdstatus"   => "false",
          "src"   => "../WIAdmin/WIMedia/Img/user_interact/td_unclicked.jpg"
        );
      echo json_encode($res);

    }else{
      $this->WIdb->update(
                    "wi_user_interact", 
                    array(
                      "thumbs_down" => "true"
                    ), 
                    "`id` =:id",
                    array( "id" => $cid )
               );
      $res = array(
          "status" => "success",
          "tdstatus"   => "true",
          "src"   => "../WIAdmin/WIMedia/Img/user_interact/td_clicked.png"
        );
      echo json_encode($res);

    }
  }else{
     $this->WIdb->insert('wi_user_interact', array(
            "user_id"     => $user_id,
            "blog_id"  => $id,
            "thumbs_down" => "true"
          )); 
     }
  }

  } 


  public function love($id)
  {
        $user_id = WISession::get('user_id');

        if($user_id == "null" || !$user_id  > "0"){
      $res = array(
          "status" => "failed",
          "tdstatus"   => "login"
        );
      echo json_encode($res);
    }else{
    $tustatus = $this->WIdb->select("SELECT * FROM `wi_user_interact` WHERE `user_id`=:user_id AND `blog_id`=:blog_id", array("user_id" => $user_id, "blog_id" => $id));

    $count = count($tustatus);

    if($count > 0){

    $status = $tustatus[0]['love'];
    $cid    = $tustatus[0]['id'];

    if($status == "true"){
      $this->WIdb->update(
                    "wi_user_interact", 
                    array(
                      "love" => "false")
                    , 
                    "`id` =:id",
                    array( "id" => $cid )
               );
      $res = array(
          "status" => "success",
          "tdstatus"   => "false",
          "src"   => "../WIAdmin/WIMedia/Img/user_interact/love_unclicked.png"
        );
      echo json_encode($res);

    }else{
      $this->WIdb->update(
                    "wi_user_interact", 
                    array(
                      "love" => "true"
                    ), 
                    "`id` =:id",
                    array( "id" => $cid )
               );
      $res = array(
          "status" => "success",
          "tdstatus"   => "true",
          "src"   => "../WIAdmin/WIMedia/Img/user_interact/love_clicked.jpg"
        );
      echo json_encode($res);

    }
  }else{
     $this->WIdb->insert('wi_user_interact', array(
            "user_id"     => $user_id,
            "blog_id"  => $id,
            "thumbs_down" => "true"
          )); 
     }
  }

  } 



    public function thumbUpCount($postId)
  {
   $result = $this->WIdb->select("SELECT 'thumbs_up' FROM `wi_user_interact`WHERE `thumbs_up`=:tu AND `blog_id` =:id", array(
    "tu" => "true",
    "id" => $postId
  ));
    
    if(count($result) > 0){
      $thumbs_up = count($result);

      return $thumbs_up;
    }else{
      return "0";
    }
  }

      public function thumbDownCount($postId)
  {
   $result = $this->WIdb->select("SELECT `thumbs_down` FROM `wi_user_interact` WHERE `thumbs_down`=:tu AND `blog_id` =:id", array(
    "tu" => "true",
    "id" => $postId
  ));
    
    if(count($result) > 0){
      $thumbs_down = count($result);

      return $thumbs_down;
    }else{
      return "0";
    }

  }

  public function loveCount($postId)
  {
   $result = $this->WIdb->select("SELECT 'love' FROM `wi_user_interact`WHERE `love`=:tu AND `blog_id` =:id", array(
    "tu" => "true",
    "id" => $postId
  ));
    
    if(count($result) > 0){
      $love = count($result);

      return $love;
    }else{
      return "0";
    }
  }


}


?>