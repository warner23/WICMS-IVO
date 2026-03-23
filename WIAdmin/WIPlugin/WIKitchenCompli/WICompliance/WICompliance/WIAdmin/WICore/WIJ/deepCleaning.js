 $(document).ready(function(){


  
});  

var deepCleaning = {};

deepCleaning.save = function(){

    var Cleaning =  [];
    $(".deepClean").each(function(){
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

    console.log(Cleaning);
    deepCleaning.send(Cleaning);
}

deepCleaning.send = function(data){
 
 $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "deepCleaning",
            User   : data
        },
        success: function(result)
        {
            $('#cleaningmessageBox').html(result).delay(5000);
            //window.location = "checklists.php";
        }
    });
}

