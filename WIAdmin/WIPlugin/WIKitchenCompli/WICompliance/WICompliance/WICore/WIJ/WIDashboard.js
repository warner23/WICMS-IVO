 $(document).ready(function(){

  
});  


var WIDashboard = {};

WIDashboard.Compedit = function(id, name){
    console.log(id);
    if(name === "Morning Checks"){
       WIDashboard.MorningChecks(id, name);
    }else if(name === "Evening Checks"){

    }else{

    }
    $('#modal-editComp-details').removeClass('hide').addClass('show');
}

WIDashboard.MorningChecks = function(id, name){
   
   $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "GetMorningChecks",
            User   : id
        },
        success: function(result)
        {
            //console.log(result);
            $("#editComp").html(result);
            //Window.location="checklists.php";
        }
    });
}