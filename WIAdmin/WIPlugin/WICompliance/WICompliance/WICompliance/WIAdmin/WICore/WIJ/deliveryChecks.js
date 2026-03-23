 $(document).ready(function(){


  
});  

var deliveryChecks = {};

deliveryChecks.save = function(){

        var drydelivery =  [];
        var ffdelivery =  [];
        var fdelivery =  [];

        
             type           = $("#dryfoods").val(),
             dfsupplier     = $("#dfsupplier").val(),
             dfdate         = $("#dfdate").val(),
             dftime         = $("#dftime").val(),
             dftemp         = $("#dftemp").val(),
             dfcorrective   = $("#dfcorrective").val(),
             dfpackaging   = $("#dfpackaging_ok").val(),
             dfproduce   = $("#dfproduce_ok").val(),
             dfrejected   = $("#dfrejected").val();
            // console.log(dfpackaging)
        var dry =
             {
                UserData:
                {
                    type             : type,
                    dfsupplier       : dfsupplier,
                    dfdate           : dfdate,
                    dftime           : dftime,
                    dftemp           : dftemp,
                    dfcorrective     : dfcorrective,
                    dfpackaging      : dfpackaging,
                    dfproduce        : dfproduce,
                    dfrejected       : dfrejected
                },
                FieldId:
                {
                    type            : "dryfoods",
                    dfsupplier      : "dfsupplier",
                    dfdate          : "dfdate",
                    dftime          : "dftime",
                    dftemp          : "dftemp",
                    dfcorrective    : "dfcorrective",
                    dfpackaging     : "dfpackaging",
                    dfproduce       : "dfproduce",
                    dfrejected      : "dfrejected"

                }
             };

        
        drydelivery.push({
        'dry' :dry

       });

            
        
             type           = $("#frozenfoods").val(),
             ffsupplier     = $("#ffsupplier").val(),
             ffdate         = $("#ffdate").val(),
             fftime         = $("#fftime").val(),
             fftemp         = $("#fftemp").val(),
             ffcorrective   = $("#ffcorrective").val(),
             ffpackaging   = $("#fpackaging_ok").val(),
             ffproduce   = $("#ffproduce_ok").val(),
             ffrejected   = $("#ffrejected").val();

        var frozen =
             {
                UserData:
                {
                    type             : type,
                    ffsupplier       : ffsupplier,
                    ffdate           : ffdate,
                    fftime           : fftime,
                    fftemp           : fftemp,
                    ffcorrective     : ffcorrective,
                    ffpackaging      : ffpackaging,
                    ffproduce        : ffproduce,
                    ffrejected       : ffrejected
                },
                FieldId:
                {
                    type            : "frozenfoods",
                    ffsupplier      : "ffsupplier",
                    ffdate          : "ffdate",
                    fftime          : "fftime",
                    fftemp          : "fftemp",
                    ffcorrective    : "ffcorrective",
                    ffpackaging     : "ffpackaging",
                    ffproduce       : "ffproduce",
                    ffrejected      : "ffrejected"

                }
             };

        
        ffdelivery.push({
        'frozen' :frozen

       });
        

        


        
             type           = $("#freshfoods").val(),
             fsupplier     = $("#fsupplier").val(),
             fdate         = $("#fdate").val(),
             ftime         = $("#ftime").val(),
             ftemp         = $("#ftemp").val(),
             fcorrective   = $("#fcorrective").val(),
             fpackaging   = $("#fpackaging_ok").val(),
             fproduce   = $("#fproduce_ok").val(),
             frejected   = $("#frejected").val();

        var fresh =
             {
                UserData:
                {
                    type             : type,
                    fsupplier       : dfsupplier,
                    fdate           : dfdate,
                    ftime           : dftime,
                    ftemp           : dftemp,
                    fcorrective     : dfcorrective,
                    fpackaging      : dfpackaging,
                    fproduce        : dfproduce,
                    frejected       : dfrejected
                },
                FieldId:
                {
                    type            : "freshfoods",
                    fsupplier      : "fsupplier",
                    fdate          : "fdate",
                    ftime          : "ftime",
                    ftemp          : "ftemp",
                    fcorrective    : "fcorrective",
                    fpackaging     : "fpackaging",
                    fproduce       : "fproduce",
                    frejected      : "frejected"

                }
             };

        
        fdelivery.push({
        'fresh' :fresh

       });
        

    var delivery =  [];

    delivery.push({
                "dry"    : drydelivery,
                "frozen" : ffdelivery,
                "fresh"  : fdelivery
            }) 
   deliveryChecks.send(delivery);
}

deliveryChecks.send = function(data){
 
 $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action : "deliveryChecks",
            User   : data
        },
        success: function(result)
        {
            //console.log(result);
            $("#dcmessageBox").html(result).delay(5000);
            Window.location="checklists.php";
        }
    });
}

