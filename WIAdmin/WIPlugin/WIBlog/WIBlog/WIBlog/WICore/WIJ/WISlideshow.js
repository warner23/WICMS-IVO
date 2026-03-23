 $(document).ready(function () {
  //catch form submit

});


var WISlideshow = {};

WISlideshow.plusSlides = function(n){
  WISlideshow.showSlides(slideIndex += n);
}

WISlideshow.currentSlide = function(n){
  WISlideshow.showSlides(slideIndex = n);
}

WISlideshow.showSlides = function(n, id){

  var slides = $(".mySlides-"+id);
      dots  = $(".dot-"+id);

      console.log(n);
      if (n > slides.length) {
        slideIndex = 1
      }    
      if (n < 1) {
        slideIndex = slides.length
      }

      for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none"; 
     }

      for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
      }

      console.log(n);
      console.log(slides );
      console.log(slides[slideIndex]);
      console.log(slideIndex);
      slides[slideIndex-1].style.display = "block";  
      dots[slideIndex-1].className += " active";
}


WISlideshow.showSlidesTest = function(n){
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
  slides[slideIndex].style.display = "block";  
  dots[slideIndex].className += " active";
}