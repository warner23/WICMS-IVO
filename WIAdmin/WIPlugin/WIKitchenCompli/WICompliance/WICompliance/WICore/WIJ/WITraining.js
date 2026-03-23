$(document).ready(function()
{

    $(".portfolio_single").owlCarousel({
    slideSpeed : 600,
    paginationSpeed: 1000, 
    autoPlay: true,
    items : 1,
    itemsDesktop : [1170,1],
    itemsDesktopSmall : [960,1],
    itemsTablet: [768,1],
    itemsMobile : [480,1],
    itemsMobileSmall: [360, 1],
    navigation:false,
    pagination:true,
    navigationText : false
});	

    $(".related_work").owlCarousel({
    slideSpeed : 600,
    paginationSpeed: 1000, 
    autoPlay: false,
    items : 4,
    itemsDesktop : [1170,4],
    itemsDesktopSmall : [960,3],
    itemsTablet: [768,3],
    itemsMobile : [480,1],
    itemsMobileSmall: [360, 1],
    navigation:true,
    pagination:false,
    navigationText : false
});

});





var WITraining ={};


WITraining.class = function(href,id){

    var id = sessionStorage.setItem("id", id);
    window.location = "WIClasses/"+href;
}

