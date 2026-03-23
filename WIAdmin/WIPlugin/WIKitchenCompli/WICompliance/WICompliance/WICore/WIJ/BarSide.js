 $(document).ready(function(){


  
});  

var WIBarSide = {};


WIBarSide.randomise = function(){
    

    	$.ajax({
    	url: "WICore/WIClass/WIAjax.php",
    	type: "GET",
    	data: {
    		action : "sosrandomise"
    	},
    	success: function(result)
    	{
    		$("#sortable").html(result);
            $("#sostext").text("Drag and drop the bottles into their correct order for the SOS Section.")
    	}
    });
}

