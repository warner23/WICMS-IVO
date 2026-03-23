 $(document).ready(function(){


  
});  

var deliveryChecks = {};

deliveryChecks.save = function(){

        var drydelivery =  [];
        var ffdelivery =  [];
        var fdelivery =  [];


    if($(".dfproduce").hasClass("checked")){
        
             type           = $("#dryfoods").val(),
             dfsupplier     = $("#dfsupplier").val(),
             dfdate         = $("#dfdate").val(),
             dftime         = $("#dftime").val(),
             dftemp         = $("#dftemp").val(),
             dfcorrective   = $("#dfcorrective").val(),
             dfpackaging   = $("input[name='dfpackaging_ok']:checked").val(),
             dfproduce   = $("input[name='dfproduce_ok']:checked").val(),
             dfrejected   = $("input[name='dfrejected']:checked").val();
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
        }
            


    
   if($(".ffproduce").hasClass("checked")){
        
             type           = $("#frozenfoods").val(),
             ffsupplier     = $("#ffsupplier").val(),
             ffdate         = $("#ffdate").val(),
             fftime         = $("#fftime").val(),
             fftemp         = $("#fftemp").val(),
             ffcorrective   = $("#ffcorrective").val(),
             ffpackaging   = $("input[name='ffpackaging_ok']:checked").val(),
             ffproduce   = $("input[name='ffproduce_ok']:checked").val(),
             ffrejected   = $("input[name='ffrejected']:checked").val();

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
        }

        

        if($(".fproduce").hasClass("checked")){
        
             type           = $("#freshfoods").val(),
             fsupplier     = $("#fsupplier").val(),
             fdate         = $("#fdate").val(),
             ftime         = $("#ftime").val(),
             ftemp         = $("#ftemp").val(),
             fcorrective   = $("#fcorrective").val(),
             fpackaging   = $("input[name='fpackaging_ok:checked").val(),
             fproduce   = $("input[name='fproduce_ok:checked").val(),
             frejected   = $("input[name='frejected:checked").val();

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
        }

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

