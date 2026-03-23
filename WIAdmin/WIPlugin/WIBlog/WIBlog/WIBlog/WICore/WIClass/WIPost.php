<?php
/**
* resource Class
* Created by Warner Infinity
* Author Jules Warner
*/
class WIPost
{
	function __construct() 
	{
     $this->WIdb = WIdb::getInstance();
     $this->Comment = new WIComment();
     $this->Page    = new WIPage();
  }

  public function hasPost($id)
  {
    $res = $this->WIdb->select(
                    "SELECT * FROM `wi_blog`
                     WHERE `id` = :id",
                     array(
                       "id" => $id
                     )
                  );
    
    //print_r($res);
    if(count($res) < 1){
      echo "No Posts Yet.";
    }else{

    foreach ($res as $media) {
    
    if($media['type'] === "NoMedia"){
        
        self::MediaNo($media);
        
    }
      if($media['type'] === "blog_slider"){
      self::MediaSlider($media);
    }

    if($media['type'] === "blog_video"){
      self::MediaVideo($media);
      
    }
    //$type = "blog_image";
    if($media['type'] === "blog_image"){
      
        self::MediaImage($media);
    }

    if($media['type'] === "blog_audio"){
      
      self::MediaAudio($media);
    }

    if($media['type'] === "blog_youtube"){
      
      $link = $media['title'];
      if( strpos($link, " ") !== false )
      {
        $link = preg_replace('/\s+/', '_', $link);
      }
      self::MediaYoutube($media);
        
          }
      }
    }
  }

  public function MediaYoutube($media)
  {

    echo '<div class="preview">blog media youtube</div>
                    <div class="view">
                   
                  <!-- .latest-posts start --><div class="blog_style_2">                           
    <article class="post_container">                              
    <div class="post-info">                                    
    <div class="post-date">                                        
    <span class="day">' . $media['day']. '</span>                                        
    <span class="month">' . $media['month'] .'</span>                                    
    </div>                                    
    <div class="post-category">                                     
    <i class="fa fa-picture-o"></i>                                    
    </div>                                
    </div><!-- .post-info end -->                                
    <figure class="post-video">                                    
   "' . $media['youtube'] .'"       
     </figure>';
  self::userInteract($media['id']); 
  echo '<div class="post-content">                                    
     <h4>' . $media['title'] . '</h4>                                    
     <div class="blog-meta">                                        
     <ul>                                           
      <li class="fa fa-user">                                                
      <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
      </li>                                            
      <li class="post-tags fa fa-tags">                                                
      <a href="javascript:void(0)">news, </a>                                                
     <a href="javascript:void(0)">dois</a>                                            
      </li>                                        
    </ul>                                    
    </div>                                    
    <p>                                     
    ' . $media['post'] . '                                 
    </p>                                    
     </div>  
         <div class="comments-comments">';
              $comments = $this->Comment->getBlogComments($media['id']);
              foreach ($comments as $c) {
                echo '<blockquote>' . $c['comment'] . '
                <small>' . $c['posted_by_name'] . ' <em>at ' . $c['post_time'] . '</em></small>
                </backquote>';
              }
               echo '<div id="abs"></div></div>   

         <textarea class="form-control" name="comment" rows="3" id="comment-text-'.$media['id'].'"></textarea> 

          <div class="form-group">
                    <button class="btn btn-primary" id="btn-comment-'.$media['id'].'" onclick="WIComment.newCommment(`'.$media['id'].'`)" type="submit">
                        <i class="fa fa-comment"></i>
                        '.WILang::get("comment").'
                    </button>
                </div>                        
                                
         </article>
                </div>';
              
  }

  public function MediaNo($media)
  {

    echo '<div class="blog_style_2"><article class="post_container">                             
         <div class="post-info">                                    
         <div class="post-date">                                        
         <span class="day">' . $media['day'] . '</span>                                        
         <span class="month">' . $media['month'] . '</span>                                    
         </div>                                    
         <div class="post-category">                                      
         <i class="fa fa-file-text-o"></i>                                    
         </div>                               
          </div><!-- .post-info end -->                                
          <div class="post-content">                                   
           <a href="blog_post.html">                                       
            <h4>' . $media['title'] . '</h4>                                    
            </a>                                    
            <div class="blog-meta">                                        
            <ul>                                            
            <li class="fa fa-user">                                               
             <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
             </li>                                            
             <li class="post-tags fa fa-tags">                                                
             <a href="javascript:void(0)">news, </a>                                               
              <a href="#">dois</a>                                            
              </li>                                        
              </ul>                                   
               </div>                                    
               <p>                                      
               ' . $media['post'] . '                                  
               </p>                                    
                                           
                </div>  
         <div class="comments-comments">';
              $comments = $this->Comment->getBlogComments($media['id']);
              foreach ($comments as $c) {
                echo '<blockquote>' . $c['comment'] . '
                <small>' . $c['posted_by_name'] . ' <em>at ' . $c['post_time'] . '</em></small>
                </backquote>';
              }
               echo '<div id="abs"></div></div>   

         <textarea class="form-control" name="comment" rows="3" id="comment-text-'.$media['id'].'"></textarea> 

          <div class="form-group">
                    <button class="btn btn-primary" id="btn-comment-'.$media['id'].'" onclick="WIComment.newCommment(`'.$media['id'].'`)" type="submit">
                        <i class="fa fa-comment"></i>
                        '.WILang::get("comment").'
                    </button>
                </div>                        
                                
         </article>';
       
  }

  public function MediaImage($media)
  {

      echo '<!-- .latest-posts start --><div class="blog_style_2">                            
                              <article class="post_container">                              
                              <div class="post-info">                                    
                              <div class="post-date">                                        
                              <span class="day">' . $media['day'] . '</span>                                        
                              <span class="month">' . $media['month'] . '</span>                                    
                              </div>                                    
                              <div class="post-category">                                     
                              <i class="fa fa-picture-o"></i>                                    
                              </div>                                
                              </div><!-- .post-info end -->                                
                              <figure class="post-image">                 
                              <a href="javascript:void(0)"><img src="../../../WIAdmin/WIMedia/Img/blog/' . $media['image'] . '" alt=""></a>                
                              </figure>                               
                               <div class="post-content">                                    
                               <a href="blog_post.html">                                        
                               <h4>' . $media['title'] . '</h4>                                    
                               </a>                                    
                               <div class="blog-meta">                                        
                               <ul>                                            
                               <li class="fa fa-user">                                                
                               <a href="javascript:void(0)">' . $media['user'] . '</a>                                           
                                </li>                                           
                                 <li class="post-tags fa fa-tags">                                               
                                  <a href="javascript:void(0)">news, </a>                                                
                                  <a href="javascript:void(0)">dois</a>                                           
                                   </li>                                        
                                   </ul>                                    
                                   </div>                                    
                                   <p>                                      
                                   ' . $media['post'] . '                               
                                   </p>                                    
                                    </div>  
         <div class="comments-comments">';
              $comments = $this->Comment->getBlogComments($media['id']);
              foreach ($comments as $c) {
                echo '<blockquote>' . $c['comment'] . '
                <small>' . $c['posted_by_name'] . ' <em>at ' . $c['post_time'] . '</em></small>
                </backquote>';
              }
               echo '<div id="abs"></div></div>   

         <textarea class="form-control" name="comment" rows="3" id="comment-text-'.$media['id'].'"></textarea> 

          <div class="form-group">
                    <button class="btn btn-primary" id="btn-comment-'.$media['id'].'" onclick="WIComment.newCommment(`'.$media['id'].'`)" type="submit">
                        <i class="fa fa-comment"></i>
                        '.WILang::get("comment").'
                    </button>
                </div>                        
                                
         </article>';

    
  }

  public function MediaSlider($media)
  {

      echo '<!-- .latest-posts start -->                            
<article class="post_container">                              
<div class="post-info">                                   
 <div class="post-date">                                        
 <span class="day">' . $media['day'] . '</span>                                        
 <span class="month">' . $media['month'] . '</span>                                   
  </div>                                    
  <div class="post-category">                                     
  <i class="fa fa-picture-o"></i>                                    
  </div>                                
  </div><!-- .post-info end -->                               
   <figure class="post-image">                                  
   <div class="slideshow-container">

<div class="mySlides">
  <div class="numbertext"></div>
   <img src="../../../WIAdmin/WIMedia/Img/blog/revslider/' . $media['image'] . '" style="width:100%">
  <div class="text">' . $media['caption'] . '</div>
</div>

<div class="mySlides">
  <div class="numbertext"></div>
<img src="../../../WIAdmin/WIMedia/Img/blog/revslider/' . $media['image2'] . '" style="width:100%">        
  <div class="text">' . $media['caption1'] . '</div>
</div>

<div class="mySlides">
  <div class="numbertext"></div>
<img src="../../../WIAdmin/WIMedia/Img/blog/revslider/' . $media['image3'] . '" style="width:100%">
  <div class="text">' . $media['caption2'] . '</div>
</div>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

</div>
<br>

<div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span> 
  <span class="dot" onclick="currentSlide(3)"></span>
</div>                                             
   </figure>                               
    <div class="post-content">                                    
    <a href="blog_post.html">                                        
    <h4>' . $media['title'] . '</h4>                                    
    </a>                                    
    <div class="blog-meta">                                        
    <ul>                                            
    <li class="fa fa-user">                                                
    <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
    </li>                                           
     <li class="post-tags fa fa-tags">                                               
      <a href="javascript:void(0)">news, </a>                                                
      <a href="javascript:void(0)">dois</a>                                            
      </li>                                        
</ul>                                    
</div>                                    
<p>                                        
' . $media['post'] . '                                  
</p>                                    
 </div>  
         <div class="comments-comments">';
              $comments = $this->Comment->getBlogComments($media['id']);
              foreach ($comments as $c) {
                echo '<blockquote>' . $c['comment'] . '
                <small>' . $c['posted_by_name'] . ' <em>at ' . $c['post_time'] . '</em></small>
                </backquote>';
              }
               echo '<div id="abs"></div></div>   

         <textarea class="form-control" name="comment" rows="3" id="comment-text-'.$media['id'].'"></textarea> 

          <div class="form-group">
                    <button class="btn btn-primary" id="btn-comment-'.$media['id'].'" onclick="WIComment.newCommment(`'.$media['id'].'`)" type="submit">
                        <i class="fa fa-comment"></i>
                        '.WILang::get("comment").'
                    </button>
                </div>                        
                                
         </article>
<script type="text/javascript">
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
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
</script>';
    

  }

  public function MediaVideo($media)
  {

      echo '<article class="post_container">                              
<div class="post-info">                                   
 <div class="post-date">                                        
 <span class="day">' . $media['day'] . '</span>                                        
 <span class="month">' . $media['month'] . '</span>                                    
 </div>                                   
  <div class="post-category">                                     
  <i class="fa fa-picture-o"></i>                                    
  </div>                                
  </div><!-- .post-info end -->                                
  <figure class="post-video">                                    
  <video controls>
  <source src="../../../WIAdmin/WIMedia/Vid/blog/' . $media['video'] . '" type="video/mp4">
</video>                                
  </figure>';
  self::userInteract($media['id']); 
  echo '<div class="post-content">                                    
   <h4>' . $media['title'] . '</h4>                                    
   <div class="blog-meta">                                        
   <ul>                                            
   <li class="fa fa-user">                                                
   <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
   </li>                                            
   <li class="post-tags fa fa-tags">                                                
   <a href="javascript:void(0)">news, </a>                                                
   <a href="javascript:void(0)">dois</a>                                            
   </li>                                        
   </ul>                                    
   </div>                                   
    <p>                                     
    ' . $media['post'] . '                                 
    </p>                                    
     </div>  
         <div class="comments-comments">';
              $comments = $this->Comment->getBlogComments($media['id']);
              foreach ($comments as $c) {
                echo '<blockquote>' . $c['comment'] . '
                <small>' . $c['posted_by_name'] . ' <em>at ' . $c['post_time'] . '</em></small>
                </backquote>';
              }
               echo '</div>   
               <div id="status" style="width: 100%;height: 44px;"></div>
         <textarea class="form-control" name="comment" rows="3" id="comment-text-'.$media['id'].'"></textarea> 

          <div class="form-group">
                    <button class="btn btn-primary" id="btn-comment-'.$media['id'].'" onclick="WIComment.newCommment(`'.$media['id'].'`)" type="submit">
                        <i class="fa fa-comment"></i>
                        '.WILang::get("comment").'
                    </button>
                </div>                        
                                
         </article>';
    
  }

  public function MediaAudio($media)
  {

      echo ' <!-- .latest-posts start -->                            
    <article class="post_container">                              
    <div class="post-info">                                    
    <div class="post-date">                                        
    <span class="day">' . $media['day'] . '</span>                                        
    <span class="month">' . $media['month'] . '</span>                                   
     </div>                                    
     <div class="post-category">                                      
     <i class="fa fa-picture-o"></i>                                   
      </div>                                
      </div><!-- .post-info end -->                                
      <figure class="post-audio">                 
      <audio controls>
      <source src="../../../WIAdmin/WIMedia/Audio/blog/' . $media['audio'] . '" type="audio/mp3"></audio>             
      </figure>                               
       <div class="post-content">                                    
       <a href="blog_post.html">                                        
       <h4>' . $media['title'] . '</h4>                                    
       </a>                                    
       <div class="blog-meta">                                        
       <ul>                                            
       <li class="fa fa-user">                                                
       <a href="javascript:void(0)">' . $media['user'] . '</a>                                            
       </li>                                            
       <li class="post-tags fa fa-tags">                                               
        <a href="javascript:void(0)">news, </a>                                                
        <a href="javascript:void(0)">dois</a>                                            
        </li>                                        
        </ul>                                    
        </div>                                   
         <p>                                      
         ' . $media['post'] . '</p> 

          </div>  
         <div class="comments-comments">';
              $comments = $this->Comment->getBlogComments($media['id']);
              foreach ($comments as $c) {
                echo '<blockquote>' . $c['comment'] . '
                <small>' . $c['posted_by_name'] . ' <em>at ' . $c['post_time'] . '</em></small>
                </backquote>';
              }
               echo '<div id="abs"></div></div>   

         <textarea class="form-control" name="comment" rows="3" id="comment-text-'.$media['id'].'"></textarea> 

          <div class="form-group">
                    <button class="btn btn-primary" id="btn-comment-'.$media['id'].'" onclick="WIComment.newCommment(`'.$media['id'].'`)" type="submit">
                        <i class="fa fa-comment"></i>
                        '.WILang::get("comment").'
                    </button>
                </div>                        
                                
         </article>';
    


    
  }



 /* User interactions */

  public function userInteract($postId)
  {
    echo '<div id="user_interact">
            <ul class="user_interact">
                <li class="ui" onclick="WIPost.thumbsUp(`'.$postId.'`);">
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

              <li class="ui" onclick="WIPost.thumbsDown(`'.$postId.'`);">
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

                <li class="ui" onclick="WIPost.Love(`'.$postId.'`);">
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