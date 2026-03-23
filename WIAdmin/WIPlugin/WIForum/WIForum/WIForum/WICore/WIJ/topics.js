$(document).ready(function () {
  var topic_id = $.cookie("topic_id");


$.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: { 
            action : "postInfo",
            id  : topic_id
        },
        
        success : function(result){
           // console.log(result);
        $("#posts").html(result);   
    //var jsonData = JSON.parse(result);
    //console.log(jsonData);
}


});

});


var topic = {};

