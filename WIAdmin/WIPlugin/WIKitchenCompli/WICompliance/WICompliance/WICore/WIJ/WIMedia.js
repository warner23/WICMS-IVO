$(document).ready(function(){

  $("img").click(function() {      
    $(this).toggleClass("hover");
    var id = $(".hover").attr("id");

    WIMedia.change(id);
    

  });

  var obj = $("#dragandrophandler");
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
$("#modal-userpost-media-details").removeClass('hide').addClass('show');
}

WIMedia.changeicshowPic = function(){
   $("#modal-favicshow-edit").removeClass("show");
    $("#modal-favicshow-edit").addClass("hide");
     $("#modal-favicshow-media").removeClass("hide");
    $("#modal-favicshow-media").addClass("show");
}


  WIMedia.changePic = function(){

  	     $("#modal-header-edit").removeClass("hide");
    $("#modal-header-edit").addClass("show");
  }

WIMedia.changefavicshowPic = function(){

         $("#modal-favicshow-edit").removeClass("hide");
    $("#modal-favicshow-edit").addClass("show");
  }



  WIMedia.closeEdit = function(){

  	 $("#modal-header-edit").removeClass("show");
    $("#modal-header-edit").addClass("hide");
  }

    WIMedia.closeFEdit = function(){

     $("#modal-favicshow-edit").removeClass("show");
    $("#modal-favicshow-edit").addClass("hide");
  }

  WIMedia.closeMedia = function(){
    $("#modal-header-media").removeClass("show");
    $("#modal-header-media").addClass("hide");
  }

    WIMedia.closeFMedia = function(){
    $("#modal-favicshow-media").removeClass("show");
    $("#modal-favicshow-media").addClass("hide");
  }

  WIMedia.closeUpload = function(){
    $("#modal-header-upload").removeClass("show");
    $("#modal-header-upload").addClass("hide");
  }

    WIMedia.closeFUpload = function(){
    $("#modal-favicshow-upload").removeClass("show");
    $("#modal-favicshow-upload").addClass("hide");
  }

  WIMedia.change = function(img){

  	  	$("#modal-header-media").removeClass("show");
    $("#modal-header-media").addClass("hide");
    $(".cp").attr("src", "WIMedia/Img/header/"+img);
    $(".cp").attr("id", img);
  }



  WIMedia.savePic = function(){

  	var img = $(".cp").attr("id");
  	//alert(img);

  	    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            actishow : "changePic",
            img    : img
                    },
        success: function(result)
        {
            var res = JSshow.parse(result);
            if (res.status === "successful") {
             $("#results").append(res.msg).fadeOut("slow");
            
        }
    }
    });
  }

    WIMedia.savefavicshowPic = function(){

    var img = $(".cp").attr("id");
    //alert(img);

        $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            actishow : "changefavicshowPic",
            img    : img
                    },
        success: function(result)
        {
            var res = JSshow.parse(result);
            if (res.status === "successful") {
             $("#results").append(res.msg).fadeOut("slow");
            
        }
    }
    });
  }


  WIMedia.upload = function(){
$("#modal-userpost-upload-details").removeClass('hide').addClass('show');
$("#modal-userpost-edit-details").removeClass('show').addClass('hide');
  }

    WIMedia.favicshowupload = function(){
             $("#modal-favicshow-edit").removeClass("show");
    $("#modal-favicshow-edit").addClass("hide");
        $("#modal-favicshow-upload").removeClass("hide");
    $("#modal-favicshow-upload").addClass("show");

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
            actishow : "folder",
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
            actishow : "back",
                    },
        success: function(result)
        {
          $("#images").html(result);
            
        }
    });
  }
// WIMedia.sendFileToServer = function(formData,status){

//       var uploadURL ="WICore/WIClass/ImageUpload.php"; //Upload URL
//     var extraData ={}; //Extra Data.
//     var jqXHR=$.ajax({
//             xhr: function() {
//             var xhrobj = $.ajaxSettings.xhr();
//             if (xhrobj.upload) {
//                     xhrobj.upload.addEventListener('progress', function(event) {
//                         var percent = 0;
//                         var positishow = event.loaded || event.positishow;
//                         var total = event.total;
//                         if (event.lengthComputable) {
//                             percent = Math.ceil(positishow / total * 100);
//                         }
//                         //Set progress
//                         WIMedia.handleFileUpload.status.setProgress(percent);
//                     }, false);
//                 }
//             return xhrobj;
//         },
//     url: uploadURL,
//     type: "POST",
//     cshowtentType:false,
//     processData: false,
//         cache: false,
//         data: formData,
//         success: function(data){
//             status.setProgress(100);
 
//             $("#status1").append("File upload Dshowe<br>");         
//         }
//     }); 
 
//     status.setAbort(jqXHR);
// }



// var rowCount=0;
// WIMedia.createStatusbar = function(obj){
//      rowCount++;
//      var row="odd";
//      if(rowCount %2 ==0) row ="even";
//      this.statusbar = $("<div class='statusbar "+row+"'></div>");
//      this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
//      this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
//      this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
//      this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
     
//      obj.after(this.statusbar);

 
// this.setFileNameSize = function(name,size){
//         var sizeStr="";
//         var sizeKB = size/1024;
//         if(parseInt(sizeKB) > 1024)
//         {
//             var sizeMB = sizeKB/1024;
//             sizeStr = sizeMB.toFixed(2)+" MB";
//         }
//         else
//         {
//             sizeStr = sizeKB.toFixed(2)+" KB";
//         }
 
//         this.filename.html(name);
//         this.size.html(sizeStr);
// }

// this.setProgress = function(progress){       
//         var progressBarWidth =progress*this.progressBar.width()/ 100;  
//         this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
//         if(parseInt(progress) >= 100)
//         {
//             this.abort.hide();
//             this.statusbar.hide();
//         }
// }
  
// this.setAbort = function(jqxhr){
//         var sb = WIMedia.statusbar;
//         WIMedia.abort.click(function()
//         {
//             jqxhr.abort();
//             sb.hide();
//         });
//     }
//   }



// WIMedia.handleFileUpload = function(files,obj){
//    for (var i = 0; i < files.length; i++) 
//    {
//         var fd = new FormData();
//         fd.append('file', files[i]);
 
//         var status = WIMedia.createStatusbar(obj); //Using this we can set progress.
//         WIMedia.setFileNameSize(files[i].name,files[i].size);
//        // $("#uploads").append(status);
//         WIMedia.sendFileToServer(fd,status);
 
//    }
// }

// WIMedia.ManualhandleFileUpload = function(files,obj){
//    for (var i = 0; i < files.length; i++) 
//    {
//         var fd = new FormData();
//         fd.append('file', files[i]);
 
//         var status =  WIMedia.createStatusbar(obj); //Using this we can set progress.
//         status.setFileNameSize(files[i].name,files[i].size);
//        // $("#uploads").append(status);
//         WIMedia.sendFileToServer(fd,status);
 
//    }
// }