$(document).ready(function () {
  var cat_id = $.cookie("cat_id");


$.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: { 
            action : "topicInfo",
            id  : cat_id
        },
        
        success : function(result){
           // console.log(result);
        $("#topic").html(result);   
    //var jsonData = JSON.parse(result);
    //console.log(jsonData);
}


});

    $("body").delegate("a.topic_id", "click", function(event){
            var topic_id     = this.id;
            //alert(product_id);
            //$.cookie.set("product_id", "product_id");

             var date = new Date();
 var minutes = 30;
 date.setTime(date.getTime() + (minutes * 60 * 1000));
            $.cookie("topic_id", topic_id, {expires: date});
            
        });

});


var topic = {};

topic.new_topic= function(){
      var topic_title = $("#topic_Title").val();

      console.log(cat_id);
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "add_new_topic",
            topic_title    : topic_title,
            cat_id       : cat_id
        },
        success: function (result) {
            //return button to normal state
            WICore.removeLoadingButton(btn);

            console.log(result);
            
             var res = JSON.parse(result);
            
            if(res.status === "completed") {
             WICore.displaySuccessMessage($("#result"), res.msg);
             window.location = "topic.php";
                }
            else {
                
            }
        }
    }); 
}