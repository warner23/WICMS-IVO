$(document).ready(function()
{
  WIClasses.getCourses();
	
});

var WIClasses ={};

WIClasses.NextSlider = function(ele, pagin, clas, item_per_page, page, total_records, total_pages){

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

WIClasses.class = function(href,id){
    var id = sessionStorage.setItem("class_id", id);
    window.location = "WIClasses/"+href;
}

WIClasses.getCourses = function(){
     var windowWidth = $(window).width();
        console.log(windowWidth);

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "getCourses",
            windowWidth: windowWidth
        },
        success: function (result) {
            console.log(result);
            $("#classDiv").html(result);
        }
    });
}

WIClasses.OpenInterestModal = function(name, id){
 $('#modal-interest-add-details').removeClass('hide').addClass('show');
 $('#express').attr('onclick', 'WIClasses.ExpressInterest(`'+name+'`,`'+id+'`);')
}

WIClasses.closed = function(ele){
    $('#modal-'+ele+'-details').removeClass('show').addClass('hide');
}

WIClasses.ExpressInterest = function(className, id){
var name = $('#fullname').val();
    email  = $('#email').val();
    phone  = $('#mobile').val();

     $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "expressInterest",
            name: name,
            email: email,
            phone: phone,
            className : className,
            id   : id
        },
        success: function (result) {
            console.log(result);
            $("#maMessage").html(result).delay(5000);
            $('#modal-interest-add-details').removeClass('show').addClass('hide');
            WICore.Refresh();
        }
    });
}