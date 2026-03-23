$(document).ready(function () {
 

    var api = jQuery('.fullwidthbanner').revolution({
			delay:9000,
			startwidth:1170,
			startheight:670,
			onHoverStop:"off",						// Stop Banner Timet at Hover on Slide on/off
			thumbWidth:20,							// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
			thumbHeight:20,
			thumbAmount:10,
			hideThumbs:10,
			navigationType:"none",				// bullet, thumb, none
			navigationArrows:"solo",				// nexttobullets, solo (old name verticalcentered), none
			navigationStyle:"round",				// round,square,navbar,round-old,square-old,navbar-old, or any from the list in the docu (choose between 50+ different item), custom
			navigationHAlign:"center",				// Vertical Align top,center,bottom
			navigationVAlign:"bottom",					// Horizontal Align left,center,right
			navigationHOffset:0,
			navigationVOffset: 0,
			soloArrowLeftHalign:"left",
			soloArrowLeftValign:"center",
			soloArrowLeftHOffset:0,
			soloArrowLeftVOffset:0,
			soloArrowRightHalign:"right",
			soloArrowRightValign:"center",
			soloArrowRightHOffset:0,
			soloArrowRightVOffset:0,
			touchenabled:"on",						// Enable Swipe Function : on/off
			stopAtSlide:-1,							// Stop Timer if Slide "x" has been Reached. If stopAfterLoops set to 0, then it stops already in the first Loop at slide X which defined. -1 means do not stop at any slide. stopAfterLoops has no sinn in this case.
			stopAfterLoops:-1,						// Stop Timer if All slides has been played "x" times. IT will stop at THe slide which is defined via stopAtSlide:x, if set to -1 slide never stop automatic
			hideCaptionAtLimit:320,					// It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
			hideAllCaptionAtLilmit:480,				// Hide all The Captions if Width of Browser is less then this value
			hideSliderAtLimit:0,					// Hide the whole slider, and stop also functions if Width of Browser is less than this value
			fullWidth:"on",
			forceFullWidth: "on",
			lazyLoad:"on",
			shadow:0								//0 = no Shadow, 1,2,3 = 3 Different Art of Shadows -  (No Shadow in Fullwidth Version !)
		});



});

var WIIndex ={};



WIIndex.NextSlider = function(ele, pagin, clas, item_per_page, page, total_records, total_pages){

  $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
              action : "nextSlider",
              ele   : ele,
              pagin :pagin,
              clas : clas,
              item_per_page : item_per_page,
              current_page   : page,
              total_records : total_records,
              total_pages : total_pages
            },
        success: function(result)
        {
            console.log($(ele))
            $('.'+ele).html(result);

        }
    });
}


