 $(document).ready(function(){


  
});  

var dailyCleaning = {};

dailyCleaning.save = function(){

    var Cleaning =  [];
    $(".dailyClean").each(function(){
        id = $(this).data('id');
            console.log(id);

        if($("#cleaningdate-"+id).val() === ""){

        }else{

            product   = $('#product-'+id).val();
            initials  = $('#cleaninginitials-'+id).val();
            date      = $('#cleaningdate-'+id).val();

            var clean =
             {
                UserData:
                {
                    product          : product,
                    initials         : initials,
                    date             : date
                },
                FieldId:
                {
                    product         : "product-"+id,
                    initials        : "cleaninginitials-"+id,
                    date            : "cleaningdate-"+id
                }
             };

        
            Cleaning.push({
             'clean' : clean
            });
            
        }
    })

    //console.log(Cleaning);
    dailyCleaning.send(Cleaning);
}

dailyCleaning.send = function(data){
 
 $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "dailyCleaning",
            User   : data
        },
        success: function(result)
        {
            $('#ecmessageBox').html(result).delay(5000);
            window.location = "checklists.php";
        }
    });
}

