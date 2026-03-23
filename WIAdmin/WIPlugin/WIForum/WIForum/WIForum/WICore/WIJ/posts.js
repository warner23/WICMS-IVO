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


var posts = {};

posts.new_post = function(){

      var post = $("#editor").html();
       contenteditable = document.querySelector('[contenteditable]'),
    text = contenteditable.textContent;
     topic_id = $.cookie("topic_id");
     cat_id = $.cookie("cat_id");
      title = $("#title").val();
    btn = $("#new_post");
    console.log(text);
        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action  : "add_new_post",
            post    : post,
            topic_id   : topic_id,
            cat_id   : cat_id,
            title    : title
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