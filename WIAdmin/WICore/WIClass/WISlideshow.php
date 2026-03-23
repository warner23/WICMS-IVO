<?php
#[\AllowDynamicProperties]
class WISlideshow
{

    public function __construct() {
        $this->WIdb   = WIdb::getInstance();
        $this->Pagin  = new WIPagination();
    }


    public function SlideShowFrame($ele, $pagin, $class, $id,$data, $container, $dots, $js, $buttons,$item_per_page, $current_page, $total_records, $total_pages)

    {
      echo '<style>

      .slideshow-container-'. $id.' {
    position: relative;
    margin: 0 0 0 58px;
    overflow: hidden;
    width: 4496px;
}

.'.$ele.'{
    margin: 1px 0px 0px 5px;
    width: 100%;


    }

}

      </style>
      <div class="slideshow-container-'. $id.'">';
           self::$container( $class, $data,$current_page, $item_per_page);
           
          
           if($dots == "no"){

           }else{
            self::SliderDots();
           }
           
      echo '</div>';
        self::$buttons($ele, $pagin, $class,$item_per_page, $current_page, $total_records, $total_pages);
      echo $js;

    }


  

    public function SliderButtons()
    {
    echo '<div class="sliderButs">
              <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
              <a class="next" onclick="plusSlides(1)">&#10095;</a>
              </div>';

             
    }

     public function SliderPaginationButtons($ele, $pagin, $class, $item_per_page, $current_page, $total_records, $total_pages)
    {
      $pag = $this->Pagin->SlidePagination($ele, $pagin, $class, $item_per_page, $current_page, $total_records, $total_pages);
          echo '<div class="'.$ele.'">';
                echo $pag;
              echo '</div>';
        
    }


    public function SliderDots()
    {
      echo '<div style="text-align:center">
              <span class="dot" onclick="currentSlide(1)"></span>
              <span class="dot" onclick="currentSlide(2)"></span>
              <span class="dot" onclick="currentSlide(3)"></span>
            </div>';
    }



    public function SlideShow()
    {
         $result = $this->WIdb->select('SELECT * FROM `wi_slideshow` WHERE `name`=:slider',array("slider" => $slider));
       if($result > 0){
      $id = $result[0]['id'];
      $result = $this->WIdb->select('SELECT * FROM `wi_slideshow_slides` WHERE `slide_id`=:id',array("id" => $id));
      if($result > 0){
      foreach($result as $res){

        echo '<!-- Slideshow container -->
            <div class="slideshow-container">

              <!-- Full-width images with number and caption text -->
              <div class="mySlides">';
                if($res['type'] == "blog_youtube"){
                    echo '<figure class="post-video">'.$res['youtube'].'</figure> ';
                }else{
                    echo '<img src="'.$res['image'].'" style="width:100%">';
                }
                
                echo '<div class="text">'.$res['title'].'</div>
              </div>';
          }
              echo '<!-- Next and previous buttons -->
              <div class="sliderButs">
              <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
              <a class="next" onclick="plusSlides(1)">&#10095;</a>
              </div>
            </div>
            <br>

            <!-- The dots/circles -->
            <div style="text-align:center">
              <span class="dot" onclick="currentSlide(1)"></span>
              <span class="dot" onclick="currentSlide(2)"></span>
              <span class="dot" onclick="currentSlide(3)"></span>
            </div>
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
        }
    }

    
  // testing

     public function ClassSlideShowFrame($ele, $pagin, $class,$data, $container, $dots, $js, $buttons,$item_per_page, $current_page, $total_records, $total_pages, $dir)

    {
      echo '<style>

      .slideshow-container {
    position: relative;
    margin: 0 0 0 8px;
    overflow: hidden;
    width: 4496px;
}

.'.$ele.'{
   margin: -80px 0px 0px 3px;
    width: 100%;


    }

  


      </style>
      <div class="slideshow-container">';
           self::$container($class, $data,$current_page, $item_per_page, $dir);
           
          
           if($dots == "no"){

           }else{
            self::SliderDots();
           }
           
      echo '</div>';
        self::$buttons($ele, $pagin, $class,$item_per_page, $current_page, $total_records, $total_pages);
      echo $js;

    }

    

}