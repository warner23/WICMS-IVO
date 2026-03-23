

function showSlidesx(id) {
  var i;
  var slides = document.getElementsByClassName("mySlides-"+id);
  var dots = document.getElementsByClassName("dot-"+id);
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
  setTimeout(showSlidesx(id), 4000); // Change image every 4 seconds
}