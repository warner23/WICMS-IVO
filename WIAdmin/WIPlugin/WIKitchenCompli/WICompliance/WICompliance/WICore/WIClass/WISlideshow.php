<?php
#[\AllowDynamicProperties]
class WISlideshow
{

    public function __construct() {
        $this->WIdb = WIdb::getInstance();
        $this->Pagin = new WIPagination();
    }

   

    public function SlideShow()
    {

        $result = $this->WIdb->select('SELECT * FROM `wi_courses` ORDER BY `id` DESC LIMIT 4');

        echo '<div class="slideshow-container col-lg-12 col-md-12 col-sm-12 col-xs-12">';
        foreach($result as $res){
        echo '<div class="mySlides">';
         
        echo '<div class="course_img">
          <figure class="post-video">
          <a href="javascript:void(0);" onclick="WIIndex.Course('.$res['id'] .')">
          <video class="video"  preload="metadata" onmouseover="this.play()" onmouseout="this.pause();this.currentTime=0;">

          <source src="../WIAdmin/WIMedia/Vid/courses/course/trailer/'.$res['trailer'].'" id="mmaVid" value="'.$res['trailer'].'" type="video/mp4">
          </video>
          </a>
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
            <div style="text-align:center">';
            $count = 0;
            foreach ($result as $res) {
              $count++;
              echo '<span class="dot" onclick="currentSlide('.$count.')"></span>';
            }
/*              <span class="dot" onclick="currentSlide(1)"></span>
              <span class="dot" onclick="currentSlide(2)"></span>
              <span class="dot" onclick="currentSlide(3)"></span>*/
            echo '</div>';

          
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

    public function Slider()
    {
      echo '  <section class="slider">
      <div class="fullwidthbanner-container">
        <div class="fullwidthbanner" style="height: 664px !important;">
          <ul>
            
                        <!-- THE FIRST SLIDE -->
            <li data-transition="turnoff" data-slotamount="1" data-masterspeed="300">
              <img src="WIAdmin/WIMedia/Img/revslider/SLIDER-1.jpg" data-lazyload="WIAdmin/WIMedia/Img/revslider/20170812_123359.jpg" alt="slidebg3" data-bgfit="contain" data-bgposition="left top" data-bgrepeat="no-repeat" />
              <div class="tp-caption very_large_text sfb"
                 data-x="70"
                 data-y="70"
                 data-speed="600"
                 data-start="1200"
                 data-easing="easeOutExpo" 
                 data-endspeed="300" data-endeasing="easeInSine" >Welcome to <span class="defcol">' . WEBSITE_NAME. '</span></div>
              <div class="caption medium_text sft stt"
                 data-x="210"
                 data-y="140"
                 data-speed="500"
                 data-start="1300"
                 data-easing="easeOutExpo" 
                 data-endspeed="300" data-endeasing="easeInSine" >MARTIAL ARTS FOR ALL</div>
              <div class="caption medium_text_def sfb"
                 data-x="250"
                 data-y="210"
                 data-speed="600"
                 data-start="1400"
                 data-easing="easeOutExpo" 
                 data-endspeed="300" data-endeasing="easeInSine" >
                 
              </div>
              <div class="caption medium_text_def sfb"
                 data-x="300"
                 data-y="240"
                 data-speed="600"
                 data-start="1400"
                 data-easing="easeOutExpo" 
                 data-endspeed="300" data-endeasing="easeInSine" >
                 
              </div>
              <div class="caption medium_text_def sfb"
                 data-x="350"
                 data-y="270"
                 data-speed="600"
                 data-start="1400"
                 data-easing="easeOutExpo" 
                 data-endspeed="300" data-endeasing="easeInSine" >
                 
              </div>
              <div class="caption randomrotate tp-resizeme"
                 data-x="420"
                 data-y="330"
                 data-speed="600"
                 data-start="1500"
                 data-easing="easeOutExpo" data-endspeed="300" data-endeasing="easeInSine" >
                 
              </div>
              <div class="caption randomrotate tp-resizeme"
                 data-x="590"
                 data-y="330"
                 data-speed="600"
                 data-start="1500"
                 data-easing="easeOutExpo" data-endspeed="300" data-endeasing="easeInSine" >
                 
              </div>
              
            </li>
            <!-- THE SECOND SLIDE -->
             <li data-transition="slidehorizontal" data-slotamount="7" data-masterspeed="1000"  data-fstransition="fade" data-fsmasterspeed="1000" data-fsslotamount="7">
              <img src="WIAdmin/WIMedia/Img/revslider/transparent.png" data-lazyload="WIAdmin/WIMedia/Img/revslider/20507810_674678502741134_857328141316812295_o.jpg"  alt="slidebg3" data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat" />
              <div class="tp-caption modern_big_mainbg sft stt"
                 data-x="330"
                 data-y="115"
                 data-speed="500"
                 data-start="1000"
                 data-easing="easeOutExpo"
                 
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                 <span>EMA</span> - Martial Arts For All
              </div>
              <div class="caption modern_big_mainbg sft stt"
                 data-x="140"
                 data-y="188"
                 data-speed="500"
                 data-start="1300"
                 data-easing="easeOutExpo"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                 Start The Positive Way
              </div>
              <div class="tp-caption modern_big_mainbg sft stt"
                 data-x="710"
                 data-y="190"
                 data-speed="500"
                 data-start="1600"
                 data-easing="easeOutExpo"
                 
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                 A Fun way
              </div>
              <div class="tp-caption randomrotate stt"
                data-x="415"
                data-y="205"
                data-speed="600"
                data-start="1900"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_6.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="558"
                data-y="156"
                data-speed="600"
                data-start="2100"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_11.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="564"
                data-y="205"
                data-speed="600"
                data-start="2400"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_9.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="558"
                data-y="260"
                data-speed="600"
                data-start="2700"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_8.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="500"
                data-y="320"
                data-speed="600"
                data-start="3000"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate sfl"
                 data-x="447"
                 data-y="304"
                 data-speed="600"
                 data-start="3300"
                 data-easing="easeInSine"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/icon-saf.png" alt="Image 1">
              </div>
              <div class="tp-caption randomrotate sfr"
                 data-x="626"
                 data-y="304"
                 data-speed="600"
                 data-start="3300"
                 data-easing="easeInSine"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/icon-ch.png" alt="Image 1">
              </div>
              <div class="tp-caption randomrotate"
                data-x="326"
                data-y="320"
                data-speed="600"
                data-start="3600"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="680"
                data-y="320"
                data-speed="600"
                data-start="3600"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate sfl"
                 data-x="275"
                 data-y="304"
                 data-speed="600"
                 data-start="3900"
                 data-easing="easeInSine"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/icon-op.png" alt="Image op">
              </div>
              <div class="tp-caption randomrotate sfr"
                 data-x="805"
                 data-y="304"
                 data-speed="600"
                 data-start="3900"
                 data-easing="easeInSine"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/icon-ff.png" alt="Image 1">
              </div>
              <div class="tp-caption randomrotate"
                data-x="150"
                data-y="320"
                data-speed="600"
                data-start="4200"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate sfb"
                data-x="858"
                data-y="320"
                data-speed="600"
                data-start="4200"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate sfl"
                 data-x="100"
                 data-y="304"
                 data-speed="600"
                 data-start="4500"
                 data-easing="ease"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/icon-ie.png" alt="Image 1">
              </div>
              <div class="tp-caption randomrotate sfr"
                 data-x="983"
                 data-y="304"
                 data-speed="600"
                 data-start="4500"
                 data-easing="easeInSine"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/icon-ie.png" alt="Image 1">
              </div>
              
            </li>


            <!-- THE third SLIDE -->
            <li data-transition="fade" data-slotamount="2" data-masterspeed="1000"  data-fstransition="fade" data-fsmasterspeed="1000" data-fsslotamount="2">
              <img src="WIAdmin/WIMedia/Img/revslider/transparent.png" data-lazyload="WIAdmin/WIMedia/Img/revslider/bg_8.jpg" alt="slidebg2" data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat" />
              <div class="tp-caption start sfb"
                data-x="340"
                data-y="105"
                data-speed="600"
                data-start="500"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/20507810_674678502741134_857328141316812295_o.jpg" alt="Image 1" width="455" height="369">
              </div>
              <div class="tp-caption randomrotate"
                data-x="220"
                data-y="105"
                data-speed="600"
                data-start="600"
                data-easing="easeOutExpo"
                
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_1.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="220"
                data-y="220"
                data-speed="600"
                data-start="700"
                data-easing="easeOutExpo"
                
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="220"
                data-y="318"
                data-speed="600"
                data-start="800"
                data-easing="easeOutExpo"
                
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_3.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="775"
                data-y="105"
                data-speed="600"
                data-start="900"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_4.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="775"
                data-y="220"
                data-speed="600"
                data-start="1000"
                data-easing="easeOutExpo"
                
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_5.png" alt="Image Slider">
              </div>
              <div class="tp-caption randomrotate"
                data-x="775"
                data-y="315"
                data-speed="600"
                data-start="1100"
                data-easing="easeOutExpo"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                <img src="WIAdmin/WIMedia/Img/revslider/line_6.png" alt="Image Slider">
              </div>
              <div class="tp-caption modern_big_mainbg randomrotate sft stt"
                 data-x="880"
                 data-y="78"
                 data-speed="100"
                 data-start="1800"
                 data-easing="easeOutExpo" 
                
                data-endspeed="300" 
                data-endeasing="easeInSine" >
                Fitness
              </div>
              
              <div class="tp-caption modern_big_mainbg randomrotate sft stt"
                data-x="154"
                data-hoffset="0"
                data-y="68"
                data-speed="500"
                data-start="1500"
                data-easing="easeOutExpo"
                data-end="7000" 
                data-endspeed="300" 
                data-endeasing="easeInSine">
                Confidence
              </div>
              <div class="tp-caption modern_big_mainbg randomrotate sft stt"
                data-x="120"
                data-hoffset="0"
                data-y="68"
                data-speed="500"
                data-start="7100"
                data-easing="easeOutExpo"
                data-endspeed="500"
                data-endeasing="easeInSine">
                Disapline
              </div>
              <div class="tp-caption modern_big_mainbg sfl stl"
                data-x="124"
                data-y="210"
                data-speed="500"
                data-start="1600"
                data-easing="easeOutExpo"
                data-endspeed="300"
                data-endeasing="easeOutExpo"
                data-captionhidden="off">
                Self Worth
              </div>
              <div class="tp-caption modern_big_mainbg sfb stb"
                data-x="145"
                data-y="370"
                data-speed="600"
                data-start="1700"
                data-easing="easeOutExpo" 
                data-end="5000"
                data-endspeed="300" 
                data-endeasing="easeInSine">
                Training
              </div>
              <div class="tp-caption modern_big_mainbg sfb stb"
                data-x="185"
                data-y="370"
                data-speed="600"
                data-start="5100"
                data-easing="easeOutExpo" >
                Sparring
              </div>
              <div class="tp-caption modern_big_mainbg sfr stt"
                 data-x="920"
                 data-y="210"
                 data-speed="600"
                 data-start="1900"
                 data-easing="easeOutExpo" 
                 data-end="6000"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                 Learning
              </div>
              <div class="tp-caption modern_big_mainbg sfr stt"
                 data-x="920"
                 data-y="210"
                 data-speed="600"
                 data-start="6100"
                 data-easing="easeOutExpo"
                 data-end="7500"
                 data-endspeed="300" 
                 data-endeasing="easeInSine" >
                 Endurance
              </div>
              <div class="caption modern_big_mainbg sfr stt"
                 data-x="920"
                 data-y="210"
                 data-speed="600"
                 data-start="7600"
                 data-easing="easeOutExpo" >
                 Quality of life
              </div>
              <div class="tp-caption modern_big_mainbg sfb stt"
                 data-x="900"
                 data-y="370"
                 data-speed="600"
                 data-start="2000"
                 data-easing="easeOutExpo" 
                data-end="4000"
                data-endspeed="300" 
                data-endeasing="easeInSine" >
               Passion
              </div>
              <div class="tp-caption modern_big_mainbg sfb stt"
                 data-x="900"
                 data-y="370"
                 data-speed="600"
                 data-start="4100"
                 data-easing="easeOutExpo" 
                data-endspeed="300" 
                data-endeasing="easeInSine" >
               Aura
              </div>
            </li>

          </ul>
        </div>
      </div>
    </section>';
    }




    public function ClassesSlideShow()
    {
          echo '<style> 
          .sliderButs{
              width: 100%;
              height: 300px;
              margin-top: -21pc;
              }

          .prev{
            margin: 0% 94% 0% 0%;
          }
          .slider-title{
                width: 100%;
    font-size: 18px;
          }
          </style>
        <div class="classInfo" id="classInfo">';
        $results = $this->WIdb->select('SELECT * FROM `wi_classes`');
        if(count($results) > 0){
            echo '<ul class="classFold">';
            foreach($results as $res){
                echo '<li class="c" id="'. $res['id']. '"><div class="photo">
                <img src="WIAdmin/WIMedia/Img/classes/'. $res['pic']. '" style="height: 265px;">
                <div class="slider-title">'. $res['name']. ' ( '. $res['age']. ' )</div>
                <div class="lvl">'. $res['level']. '</div>
                </div></li>';
            }

            echo '</ul>';

            echo '<!-- Next and previous buttons -->
              <div class="sliderButs">
              <a class="prev" onclick="plusSlides(-1)"> &#10094;</a>

              <a class="next" onclick="plusSlides(1)"> &#10095;</a>
              </div>
            </div>
            <br>
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
              var maxPic = 3;
              var slides = document.getElementsByClassName("c");
              if (n > slides.length) {
                slideIndex = 1
              }
              if (n < 1) {
                slideIndex = slides.length
              }
              for (i = 0; i < slides.length; i++) {
                if(slides[i] > maxPic){
                  slides[i].style.display = "none";
                }
              }
              
              slides[slideIndex-1].style.display = "block";
              setTimeout(showSlides, 4000); // Change image every 4 seconds
            }
            </script>';
        }
    }

    public function SlideShowFrame($ele, $pagin, $class, $id,$data, $container, $dots, $js, $buttons,$item_per_page, $current_page, $total_records, $total_pages)

    {
      echo '<style>

@media screen and (min-width: 500px) {
#classDiv{
  overflow: hidden;
      
}

}

@media screen and (max-width: 300px) {
#classDiv{
      overflow: hidden;
      margin-left: -6%;
}

}

@media screen and (max-width: 515px) {
#classDiv{
      overflow: hidden;
      margin-left: -2%;
}

}
      .slideshow-container-'. $id.' {
    position: relative;
    margin: 0 0 0 8px;
    overflow: hidden;
    width: 4496px;
}

.'.$ele.'{
   margin: -80px 0px 0px 3px;
    width: 100%;


    }

  #classDiv{
      overflow: hidden;

}

  .arrow{
    color: black;
  }
}

      </style>
      <div class="slideshow-container-'. $id.'">';
           self::$container($class, $data,$current_page, $item_per_page);
           
          
           if($dots == "no"){

           }else{
            self::SliderDots();
           }
           
      echo '</div>';
        self::$buttons($ele, $pagin, $class,$item_per_page, $current_page, $total_records, $total_pages);
      echo $js;

    }

     public function SliderContainer($class, $data, $current_page, $item_per_page)
    {
      $no = 0;
      $counter = 0;
      foreach($data as $res){
        $no++;
        $counter++;

        

        if($no <= $item_per_page && $counter < $item_per_page){
         echo '<div class="mySlides course_container ' . $class.'" id="'.$no.'" data-page="'.$current_page.'" style="display:block;">
          <div class="course_data">
          <div class="course_name">
          <h4>' .$res['name'] . '</h4>
          </div>
          <div class="course_desc">
          <p>' .$res['level'] . '</p>
          </div> 
          </div>

          <a class="course_link" href="javascript:void(0)" id="' . $res['id'] . '" onclick="WIIndex.class(`' .$res['href'] . '`,`' . $res['id'] . '`)">

          <div class="course_img">
          <figure class="post-video">
          <img src="WIAdmin/WIMedia/Img/classes/'. $res['pic']. '" style="height: 265px;">
          </figure>
          </div>

          </a>

          </div>';
        }elseif($no > $item_per_page) {
          echo '<div class="mySlides course_container ' . $class.'" id="'.$no.'" data-page="'.$current_page.'" style="display:none;">
          <div class="course_data">
          <div class="course_name">
          <h4>' .$res['name'] . '</h4>
          </div>
          <div class="course_desc">
          <p>' .$res['level'] . '</p>
          </div> 
          </div>

          <a class="course_link" href="javascript:void(0)" id="' . $res['id'] . '" onclick="WIIndex.class(`' .$res['href'] . '`,`' . $res['id'] . '`)">


          <div class="course_img">
          <figure class="post-video">
          <img src="WIAdmin/WIMedia/Img/classes/'. $res['pic']. '" style="height: 265px;">
          </figure>
          </div>
          </a>
          
          </div>';
        }elseif($counter == $item_per_page){
          if($no > $item_per_page){
            echo '<div class="mySlides course_container ' . $class.'" id="'.$no.'" data-page="'.$current_page.'" style="display:none;">';
          }

          echo '<div class="mySlides course_container ' . $class.'" id="'.$no.'" data-page="'.$current_page.'" style="display:block;">
          <div class="course_data">
          <div class="course_name">
          <h4>' .$res['name'] . '</h4>
          </div>
          <div class="course_desc">
          <p>' .$res['level'] . '</p>
          </div> 
          </div>

          <a class="course_link" href="javascript:void(0)" id="' . $res['id'] . '" onclick="WIIndex.class(`' .$res['href'] . '`,`' . $res['id'] . '`)">
          <div class="course_img">
          <figure class="post-video">
          <img src="WIAdmin/WIMedia/Img/classes/'. $res['pic']. '" style="height: 265px;">
          </figure>
          </div>
          </a>
         
          </div>';
        }

        if($counter == $item_per_page){
          $current_page ++;
          $counter = 0;
        }
      
        }
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

    public function SlideShowBlog()
    {

        $result = $this->WIdb->select('SELECT * FROM `wi_blog` LIMIT 3');

        echo '<div class="slideshow-container col-lg-8 col-md-8 col-sm-8 col-xs-8">';
        foreach($result as $res){
        echo '<div class="mySlides">';
                if($res['type'] == "blog_youtube"){
                    echo '<figure class="post-video">'.$res['youtube'].'</figure> ';
                }else{
                    echo '<img src="'.$res['image'].'" style="width:100%">';
                }

                if($res['title'] == ""){

                }else{
                  echo '<div class="text">'.$res['title'].'</div></div>';
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
            }
            </script>';
    }

}