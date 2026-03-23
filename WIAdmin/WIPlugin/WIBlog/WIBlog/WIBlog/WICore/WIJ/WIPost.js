$(document).ready(function(){

  var id = sessionStorage.getItem("page_id");

  WIPost.loadPost(id);

});

var WIPost = {};

WIPost.loadPost = function(id){


  $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "loadPost",
            id   : id
        },
        success: function(result)
        {
         $("#posts").html(result);
        }
       
        
    });
}


WIPost.thumbsUp = function(id){

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        dataType: "json",
        data: {
            action  : "thumbup",
            id      : id
        },
        success: function (result) {
            if(result.status == "success"){
                
                if(result.tustatus == "true"){
                    $("#tus"+id).attr("src", result.src);
                }else{
                     $("#tus"+id).attr("src", result.src);
                }
                
            }else {
                window.location = "index.php";
            }
        }
    });

}

WIPost.thumbsDown = function(id){
    
    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        dataType: "json",
        data: {
            action  : "thumbdown",
            id      : id
        },
        success: function (result) {
             //console.log(result );
            if(result.status == "success"){
                //console.log(result.tdstatus );
                if(result.tdstatus == "true"){
                     $("#tds"+id).attr("src", result.src);
                }else{
                     $("#tds"+id).attr("src", result.src);
                }
                
            }else {
                window.location = "index.php";
            }
        }
    });

}

WIPost.Love = function(id){
    
    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        dataType: "json",
        data: {
            action  : "love",
            id      : id
        },
        success: function (result) {
             //console.log(result );
            if(result.status == "success"){
                //console.log(result.tdstatus );
                if(result.tdstatus == "true"){
                     $("#love"+id).attr("src", result.src);
                }else{
                     $("#love"+id).attr("src", result.src);
                }
                
            }else {
                window.location = "index.php";
            }
        }
    });

}
