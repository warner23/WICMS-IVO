 $(document).ready(function(){

  
});  

var WIDashboard = {};

WIDashboard.Compedit = function(id, name){
    console.log(id);
    if(name === "Morning Checks"){
       WIDashboard.MorningChecks(id, name);
    }else if(name === "Evening Checks"){
        WIDashboard.EveningChecks(id, name);
    }else if(name === "Daily Cleaning"){
        WIDashboard.DailyCleaning(id, name);
    }else if(name === "Deep Cleaning"){
        WIDashboard.DeepCleaning(id, name);
    }else if(name === "Delivery Checks"){
        WIDashboard.DeliveryChecks(id, name);
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

WIDashboard.EveningChecks = function(id, name){
   
   $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "GetEveningChecks",
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

WIDashboard.DailyCleaning = function(id, name){
   
   $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "GetDailyCleaning",
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

WIDashboard.DeepCleaning = function(id, name){
   
   $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "GetDeepCleaning",
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

WIDashboard.DeliveryChecks = function(id, name){
   
   $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "GetDeliveryChecks",
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
