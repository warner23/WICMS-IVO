<?php

class WISlideshow
{

    public function __construct() {
        $this->WIdb = WIdb::getInstance();
    }

   
    public function SlideShow()
    {

        $result = $this->WIdb->select('SELECT * FROM `wi_courses`  LIMIT 3');

        echo '<div class="slideshow-container col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        foreach($result as $res){
        echo '<div class="mySlides">';
         
        echo '<div class="course_img">
          <figure class="post-video">
          <video class="video" poster="WIAdmin/WIMedia/Img/courses/course/'.$res['photo'].'"  preload="metadata" onmouseover="this.play()" onmouseout="this.pause();this.currentTime=0;">

          <source src="../WIAdmin/WIMedia/Vid/courses/course/trailer/'.$res['trailer'].'" id="mmaVid" value="'.$res['trailer'].'" type="video/mp4">
          </video>
          </figure>
          </div>';

                if($res['name'] == ""){

                }else{
                  echo '<div class="text">'.$res['name'].'</div></div>';
                }
                

          }
              echo '<!-- Next and previous buttons -->
              <div class="sliderButs">
              <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
              <a class="next" onclick="plusSlides(1)">&#10095;</a>
              </div>
            
            <br>

            <!-- The dots/circles -->
            <div style="text-align:center">
              <span class="dot" onclick="currentSlide(1)"></span>
              <span class="dot" onclick="currentSlide(2)"></span>
              <span class="dot" onclick="currentSlide(3)"></span>
            </div>';

          
           echo '</div></div>
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
              setTimeout(showSlides, 4000); // Change image every 4 seconds
            }
            </script>';
    }

}