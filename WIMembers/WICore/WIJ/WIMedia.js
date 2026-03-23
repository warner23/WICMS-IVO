$(document).ready(function(){

  $("img").click(function() { 
      console.log('clicked'); 
    $(this).toggleClass("hover");
    var id = $(".hover").attr("id");
    console.log('clicked');
    if($(this).hasClass('favicon')){
      WIMedia.changefavicon(id);
    }else{
         // alert(id);
    WIMedia.change(id);
    }

  });

  var obj = $("#dragandrophandler");
  var media = $("#mediadragandrophandler");
  var dir = $("#supload").attr("value");
obj.on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '2px solid #0B85A1');
});
obj.on('dragover', function (e) 
{
     e.stopPropagation();
     e.preventDefault();
});
obj.on('drop', function (e) 
{
 
     $(this).css('border', '2px dotted #0B85A1');
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
 
     //We need to send dropped files to Server
     console.log(files,obj, dir);
     handleFileUpload(files,obj,dir);
});

media.on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '2px solid #0B85A1');
});
media.on('dragover', function (e) 
{
     e.stopPropagation();
     e.preventDefault();
});
media.on('drop', function (e) 
{
 
     $(this).css('border', '2px dotted #0B85A1');
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
 
     //We need to send dropped files to Server
     console.log(files,media, dir);
     handleFileUpload(files,media,dir);
});
$(document).on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});
$(document).on('dragover', function (e) 
{
  e.stopPropagation();
  e.preventDefault();
  obj.css('border', '2px dotted #0B85A1');
});
$(document).on('drop', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});

});

var WIMedia = {};


WIMedia.media = function(){
   $("#modal-header-edit-details").removeClass("hide").addClass("show");
    $("#modal-header-media-details").removeClass("hide").addClass("show");
}


WIMedia.avatarMedia = function(){
   $("#modal-change-avatar-details").removeClass("show").addClass("hide");
    $("#modal-change-media-details").removeClass("hide").addClass("show");
}

WIMedia.avatarUpload = function(){
   $("#modal-change-avatar-details").removeClass("show").addClass("hide");
    $("#modal-media-upload-details").removeClass("hide").addClass("show");
}



WIMedia.pagemediagallery = function(){
   $("#modal-media-edit-details").removeClass("hide").addClass("show");
     $("#modal-media-media-details").removeClass("hide").addClass("show");
}



WIMedia.changePic = function(ele){

         $("#modal-"+ele+"-details").removeClass("hide").addClass("show");
  }

WIMedia.UploadAvatarPics = function(){
    //event.preventDefault();
    $('.ajax-loading').removeClass('hide').addClass('show');

    var photo  = $("#avatarPic").attr('value');
    //console.log(photo);

    $.ajax({
    url: "WICore/WIClass/WIAjax.php", // Url to which the request is send
    type: "POST",             // Type of request to be send, called as method
    data: {
               action: "uploadUserPhoto",
               photo : photo
           },
    success: function(result)   // A function to be called if request succeeds
    { 
      var res = JSON.parse(result);
    if(res.status == "successful")
        {

          $('.ajax-loading').removeClass('show').addClass('hide');
          WIProfile.showpic(res.userId);
          $("#modal-media-upload-details").removeClass('show').addClass('hide');
          window.location.reload();
        }
        else if(result === "error")
        {
          $("#upload-preview").append(res.msg);
          $("#modal-media-upload-details").removeClass('show').addClass('hide');
        
                
        }
            
     }

});
}


  WIMedia.closeEdit = function(){

     $("#modal-header-edit-details").removeClass("show").addClass("hide");
  }

  WIMedia.closed = function(ele){

     $("#modal-"+ele+"-details").removeClass("show").addClass("hide");
  }


  WIMedia.closeMedia = function(){
    $("#modal-header-media-details").removeClass("show").addClass("hide");
  }

  WIMedia.closeUpload = function(){
    $("#modal-header-upload-details").removeClass("show").addClass("hide");
  }


  WIMedia.change = function(img){
    $('.ajax-loading').removeClass('hide').addClass('show');

    $.ajax({
    url: "WICore/WIClass/WIAjax.php", // Url to which the request is send
    type: "POST",             // Type of request to be send, called as method
    data: {
               action: "uploadUserPhoto",
               photo : img
           },
    success: function(result)   // A function to be called if request succeeds
    { 
      var res = JSON.parse(result);
    if(res.status == "successful")
        {

          $('.ajax-loading').removeClass('show').addClass('hide');
          WIProfile.showpic(res.userId);
          $("#modal-change-media-details").removeClass('show').addClass('hide');
          window.location.reload();
        }
        else if(result === "error")
        {
          $("#upload-preview").append(res.msg);
          $("#modal-change-media-details").removeClass('show').addClass('hide');
        
                
        }
            
     }

  });
}




  WIMedia.savePic = function(){

    var img = $(".cp").attr("value");
    //alert(img);

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "changePic",
            img    : img
                    },
        success: function(result)
        {
            var res = JSON.parse(result);
            if (res.status === "successful") {
             $("#hresults").append(res.msg).fadeOut("slow");
            
        }
    }
    });
  }


  WIMedia.upload = function(){
             $("#modal-header-edit-details").removeClass("show").addClass("hide");
        $("#modal-header-upload-details").removeClass("hide").addClass("show");
  }


      WIMedia.PageMediaUploadPics = function(){
        $("#modal-media-edit-details").removeClass("show").addClass("hide");
        $("#modal-media-upload-details").removeClass("hide").addClass("show");;
  }

  WIMedia.ImageMedia = function(){
   // e.preventDefault();
  var files = files;
  var obj = $("#manmed");
 
     //We need to send dropped files to Server
     handleFileUpload(files,obj);


  }

  WIMedia.Folder = function(folder){
    
     $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "folder",
            folder : folder
                    },
        success: function(result)
        {
          $("#images").html(result);
            
        }
    });
  }

  WIMedia.goback= function(){

         $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "back",
                    },
        success: function(result)
        {
          $("#images").html(result);
            
        }
    });
  }
