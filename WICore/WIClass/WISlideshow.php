<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WISlideshow
{
    private WIdb $WIdb;
    private $Pagin = null;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

        if (class_exists('WIPagination')) {
            try {
                $this->Pagin = new WIPagination();
            } catch (\Throwable $e) {
                $this->Pagin = null;
            }
        }
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function slideshowLink(string $href): string
    {
        $href = trim($href);

        if ($href === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $href)) {
            return $href;
        }

        if (preg_match('/\.php$/i', $href)) {
            return $href;
        }

        return $href . '.php';
    }

    /**
     * Database slideshow loader
     * Looks up wi_slideshow.name, then loads slides from wi_slideshow_slides.slide_id
     */
    public function SlideShow($slider): void
    {
        $slider = trim((string)$slider);

        if ($slider === '') {
            return;
        }

        $results = $this->WIdb->select(
            'SELECT * FROM `wi_slideshow` WHERE `name` = :slider LIMIT 1',
            ["slider" => $slider]
        );

        if (!is_array($results) || count($results) === 0) {
            return;
        }

        $slideshowId = (int)($results[0]['id'] ?? 0);

        if ($slideshowId <= 0) {
            return;
        }

        $slides = $this->WIdb->select(
            'SELECT * FROM `wi_slideshow_slides` WHERE `slide_id` = :id ORDER BY `id` ASC',
            ["id" => $slideshowId]
        );

        if (!is_array($slides) || count($slides) === 0) {
            return;
        }

        echo '<section class="slider">
        <div class="fullwidthbanner-container">
            <div class="fullwidthbanner" style="height: 664px !important;">
                <ul>';

        foreach ($slides as $res) {
            $transition   = $this->e($res['data-transition'] ?? 'fade');
            $slotamount   = $this->e($res['data-slotamount'] ?? '1');
            $masterspeed  = $this->e($res['data-masterspeed'] ?? '300');
            $image        = $this->e($res['image'] ?? '');
            $lazyload     = $this->e($res['data-lazyload'] ?? ($res['image'] ?? ''));
            $alt          = $this->e($res['alt'] ?? '');
            $bgfit        = $this->e($res['data-bgfit'] ?? 'cover');
            $bgposition   = $this->e($res['data-bgposition'] ?? 'left top');
            $bgrepeat     = $this->e($res['data-bgrepeat'] ?? 'no-repeat');
            $brandTitle   = $res['brand_title'] ?? '';
            $brandDesc    = $res['brand_desc'] ?? '';
            $href         = $this->slideshowLink((string)($res['href'] ?? ''));
            $button       = $res['button'] ?? '';
            $class        = trim((string)($res['clas'] ?? ''));

            echo '<li data-transition="' . $transition . '" data-slotamount="' . $slotamount . '" data-masterspeed="' . $masterspeed . '"' . ($class !== '' ? ' class="' . $this->e($class) . '"' : '') . ' style="display:block;">
                    <img src="' . $image . '" data-lazyload="' . $lazyload . '" alt="' . $alt . '" data-bgfit="' . $bgfit . '" data-bgposition="' . $bgposition . '" data-bgrepeat="' . $bgrepeat . '" />
                    <div class="carousel-caption">';

            if ($brandTitle !== '') {
                echo '<h1 class="brand-title display-2 animated fadeInDown">' . $brandTitle . '</h1>';
            }

            if ($brandDesc !== '') {
                echo '<h4 class="brand-description animated fadeInDown">' . $brandDesc . '</h4>';
            }

            if ($href !== '' && trim((string)$button) !== '') {
                echo '<a class="mt-3 btn btn-light animated fadeInUp" href="' . $this->e($href) . '" role="button">' . $this->e($button) . '</a>';
            }

            echo '  </div>
                </li>';
        }

        echo '      </ul>
            </div>
        </div>
        </section>';
    }

    /**
     * Static fallback/demo slider
     * Preserved from original styling/markup
     */
    public function Slider(): void
    {
        echo '<section class="slider">
        <div class="fullwidthbanner-container">
            <div class="fullwidthbanner" style="height: 664px !important;">
                <ul>

                    <li data-transition="turnoff" data-slotamount="1" data-masterspeed="300">
                        <img src="WIAdmin/WIMedia/Img/slideshow/cover-homepage-1.jpg" data-lazyload="WIAdmin/WIMedia/Img/slideshow/cover-homepage-1.jpg" alt="slidebg3" data-bgfit="contain" data-bgposition="left top" data-bgrepeat="no-repeat" />
                        <div class="tp-caption very_large_text sfb"
                             data-x="70"
                             data-y="70"
                             data-speed="600"
                             data-start="1200"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">Welcome to <span class="defcol">' . $this->e(WEBSITE_NAME) . '</span></div>
                        <div class="caption medium_text sft stt"
                             data-x="210"
                             data-y="140"
                             data-speed="500"
                             data-start="1300"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">MARTIAL ARTS FOR ALL</div>
                        <div class="caption medium_text_def sfb"
                             data-x="250"
                             data-y="210"
                             data-speed="600"
                             data-start="1400"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine"></div>
                        <div class="caption medium_text_def sfb"
                             data-x="300"
                             data-y="240"
                             data-speed="600"
                             data-start="1400"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine"></div>
                        <div class="caption medium_text_def sfb"
                             data-x="350"
                             data-y="270"
                             data-speed="600"
                             data-start="1400"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine"></div>
                        <div class="caption randomrotate tp-resizeme"
                             data-x="420"
                             data-y="330"
                             data-speed="600"
                             data-start="1500"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine"></div>
                        <div class="caption randomrotate tp-resizeme"
                             data-x="590"
                             data-y="330"
                             data-speed="600"
                             data-start="1500"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine"></div>
                    </li>

                    <li data-transition="slidehorizontal" data-slotamount="7" data-masterspeed="1000" data-fstransition="fade" data-fsmasterspeed="1000" data-fsslotamount="7">
                        <img src="WIAdmin/WIMedia/Img/slideshow/cover-homepage-2.jpg" data-lazyload="WIAdmin/WIMedia/Img/slideshow/cover-homepage-2.jpg" alt="slidebg3" data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat" />
                        <div class="tp-caption modern_big_mainbg sft stt"
                             data-x="330"
                             data-y="115"
                             data-speed="500"
                             data-start="1000"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <span>EMA</span> - Martial Arts For All
                        </div>
                        <div class="caption modern_big_mainbg sft stt"
                             data-x="140"
                             data-y="188"
                             data-speed="500"
                             data-start="1300"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             Start The Positive Way
                        </div>
                        <div class="tp-caption modern_big_mainbg sft stt"
                             data-x="710"
                             data-y="190"
                             data-speed="500"
                             data-start="1600"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             A Fun way
                        </div>
                        <div class="tp-caption randomrotate stt"
                             data-x="415"
                             data-y="205"
                             data-speed="600"
                             data-start="1900"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_6.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="558"
                             data-y="156"
                             data-speed="600"
                             data-start="2100"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_11.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="564"
                             data-y="205"
                             data-speed="600"
                             data-start="2400"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_9.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="558"
                             data-y="260"
                             data-speed="600"
                             data-start="2700"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_8.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="500"
                             data-y="320"
                             data-speed="600"
                             data-start="3000"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate sfl"
                             data-x="447"
                             data-y="304"
                             data-speed="600"
                             data-start="3300"
                             data-easing="easeInSine"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/icon-saf.png" alt="Image 1">
                        </div>
                        <div class="tp-caption randomrotate sfr"
                             data-x="626"
                             data-y="304"
                             data-speed="600"
                             data-start="3300"
                             data-easing="easeInSine"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/icon-ch.png" alt="Image 1">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="326"
                             data-y="320"
                             data-speed="600"
                             data-start="3600"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="680"
                             data-y="320"
                             data-speed="600"
                             data-start="3600"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate sfl"
                             data-x="275"
                             data-y="304"
                             data-speed="600"
                             data-start="3900"
                             data-easing="easeInSine"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/icon-op.png" alt="Image op">
                        </div>
                        <div class="tp-caption randomrotate sfr"
                             data-x="805"
                             data-y="304"
                             data-speed="600"
                             data-start="3900"
                             data-easing="easeInSine"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/icon-ff.png" alt="Image 1">
                        </div>
                        <div class="tp-caption randomrotate"
                             data-x="150"
                             data-y="320"
                             data-speed="600"
                             data-start="4200"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate sfb"
                             data-x="858"
                             data-y="320"
                             data-speed="600"
                             data-start="4200"
                             data-easing="easeOutExpo"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/line_2.png" alt="Image Slider">
                        </div>
                        <div class="tp-caption randomrotate sfl"
                             data-x="100"
                             data-y="304"
                             data-speed="600"
                             data-start="4500"
                             data-easing="ease"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/icon-tr.png" alt="Image tr">
                        </div>
                        <div class="tp-caption randomrotate sfr"
                             data-x="980"
                             data-y="304"
                             data-speed="600"
                             data-start="4500"
                             data-easing="ease"
                             data-endspeed="300"
                             data-endeasing="easeInSine">
                             <img src="WIAdmin/WIMedia/Img/revslider/icon-tw.png" alt="Image tw">
                        </div>
                    </li>

                    <li data-transition="turnoff" data-slotamount="1" data-masterspeed="300" style="display:block;">
                        <img src="WIAdmin/WIMedia/Img/slideshow/cover-homepage-3.jpg" data-lazyload="" alt="" data-bgfit="contain" data-bgposition="left top" data-bgrepeat="no-repeat" />
                        <div class="carousel-caption">
                            <h1 class="brand-title display-2 animated fadeInDown">THE</h1>
                            <h4 class="brand-description animated fadeInDown">TRAINING ROOM</h4>
                            <a class="mt-3 btn btn-light animated fadeInUp" href="reservations.php" role="button">BOOK NOW</a>
                        </div>
                    </li>

                    <li data-transition="turnoff" data-slotamount="1" data-masterspeed="300" style="display:block;">
                        <img src="WIAdmin/WIMedia/Img/slideshow/cover-homepage-4.jpg" data-lazyload="" alt="" data-bgfit="contain" data-bgposition="left top" data-bgrepeat="no-repeat" />
                        <div class="carousel-caption">
                            <h1 class="brand-title display-2 animated fadeInDown">LE</h1>
                            <h4 class="brand-description animated fadeInDown">BAR &amp; RESTAURANT</h4>
                            <a class="mt-3 btn btn-light animated fadeInUp" href="reservations.php" role="button">BOOK NOW</a>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
        </section>';
    }

    public function SlideShowFrame($ele, $pagin, $class, $id, $data, $container, $dots, $js, $buttons, $item_per_page, $current_page, $total_records, $total_pages): void
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

.slideshow-container-' . $this->e($id) . ' {
    position: relative;
    margin: 0 0 0 8px;
    overflow: hidden;
    width: 4496px;
}

.' . $this->e($ele) . '{
    margin: -80px 0px 0px 3px;
    width: 100%;
}

#classDiv{
    overflow: hidden;
}

.arrow{
    color: black;
}
</style>
<div class="slideshow-container-' . $this->e($id) . '">';

        if (is_string($container) && method_exists($this, $container)) {
            call_user_func([$this, $container], $class, $data, $current_page, $item_per_page);
        }

        if ($dots !== "no") {
            $this->SliderDots();
        }

        echo '</div>';

        if (is_string($buttons) && method_exists($this, $buttons)) {
            call_user_func([$this, $buttons], $ele, $pagin, $class, $item_per_page, $current_page, $total_records, $total_pages);
        }

        echo $js;
    }

    public function SliderContainer($class, $data, $current_page, $item_per_page): void
    {
        $no = 0;
        $counter = 0;

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $res) {
            $no++;
            $counter++;

            $display = ($no <= $item_per_page && $counter < $item_per_page) ? 'block' : 'none';

            if ($counter === $item_per_page) {
                $display = 'block';
            }

            echo '<div class="mySlides course_container ' . $this->e($class) . '" id="' . $this->e($no) . '" data-page="' . $this->e($current_page) . '" style="display:' . $display . ';">
                <div class="course_data">
                    <div class="course_name">
                        <h4>' . $this->e($res['name'] ?? '') . '</h4>
                    </div>
                    <div class="course_desc">
                        <p>' . $this->e($res['level'] ?? '') . '</p>
                    </div>
                </div>

                <a class="course_link" href="javascript:void(0)" id="' . $this->e($res['id'] ?? '') . '" onclick="WIIndex.class(`' . $this->e($res['href'] ?? '') . '`,`' . $this->e($res['id'] ?? '') . '`)">
                    <div class="course_img">
                        <figure class="post-video">
                            <img src="WIAdmin/WIMedia/Img/classes/' . $this->e($res['pic'] ?? '') . '" style="height: 265px;">
                        </figure>
                    </div>
                </a>
            </div>';

            if ($counter === $item_per_page) {
                $current_page++;
                $counter = 0;
            }
        }
    }

    public function SliderButtons(): void
    {
        echo '<div class="sliderButs">
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>';
    }

    public function SliderPaginationButtons($ele, $pagin, $class, $item_per_page, $current_page, $total_records, $total_pages): void
    {
        if ($this->Pagin && method_exists($this->Pagin, 'SlidePagination')) {
            $pag = $this->Pagin->SlidePagination($ele, $pagin, $class, $item_per_page, $current_page, $total_records, $total_pages);
            echo '<div class="' . $this->e($ele) . '">';
            echo $pag;
            echo '</div>';
        }
    }

    public function SliderDots(): void
    {
        echo '<div style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>';
    }
}
?>