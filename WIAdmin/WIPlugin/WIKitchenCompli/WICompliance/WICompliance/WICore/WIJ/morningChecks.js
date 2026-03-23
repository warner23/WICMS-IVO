 $(document).ready(function(){


  
});  

var morningChecks = {};

morningChecks.save = function(){
     console.log("clicked");

    duty            = $('#dutyChef').val();
    position        = $('#dutyChefPosition').val();
    signedFridges   = $('#fridgeTemps').val();
    signedFreezera  = $('#freezerTemps').val();
    signedOpening   = $('#OpeningChecks').val();
    signedFChecks   = $('#fridgeChecks').val();
    signedDishes    = $('#dishwasherChecks').val();
    
    var questiondata =  [];
    var fridgedata =  [];
    var freezerdata =  [];
    var batchdata =  [];

    var data =  [];

    $(".fridges:input").each(function(){
        if($(this).val() === ""){
        
        }else{
        fridgedata.push({
    'fridge': $(this).val(),
    'id'    : $(this).attr('id')
        });
    }
      });


    $(".freezers:input").each(function(){
        if($(this).val() === ""){

        }else{
        freezerdata.push({
    'freezer': $(this).val(),
    'id'    : $(this).attr('id')
       });
     }
      });

    $(".batch").each(function(){

        id = $(this).data('id');

             product        = $("#product-"+id).val(),
             eofct          = $("#eofct-"+id).val(),
             eoct           = $("#eoct-"+id).val(),
             method         = $("#method-"+id).val(),
             eoctime        = $("#eoctime-"+id).val(),
             eoctemp        = $("#eoctemp-"+id).val(),
             totalCool      = $("#totalCool-"+id).val(),
             time_in_fridge = $("#time_in_fridge-"+id).val(),
             initials       = $("#initials-"+id).val();

        var batch =
             {
                UserData:
                {
                    product          : product,
                    eofct            : eofct,
                    eoct             : eoct,
                    method           : method,
                    eoctime          : eoctime,
                    eoctemp          : eoctemp,
                    totalCool        : totalCool,
                    time_in_fridge   : time_in_fridge,
                    initials         : initials
                },
                FieldId:
                {
                    product         : "product-"+id,
                    eofct           : "eofct-"+id,
                    eoct            : "eoct-"+id,
                    method          : "method-"+id,
                    eoctime         : "eoctime-"+id,
                    eoctemp         : "eoctemp-"+id,
                    totalCool       : "totalCool-"+id,
                    time_in_fridge  : "time_in_fridge-"+id,
                    initials        : "initials-"+id
                }
             };

        
        batchdata.push({
        'batch' :batch

       });
     });
      


    $('.questions:checked').each(function() {
        var id = $(this).data('id');
        var value = $(this).val();
        console.log(id);
        console.log(value);

        if(value === "no"){
            var problem = $("textarea#problem-"+id).val();
            action = $("textarea#takeAction-"+id).val();
            signed = $("#actionSigned-"+id).val();
            date = $("#completeDate-"+id).val();

            questiondata.push({
    'answer'   : $(this).val(),
    'question' : $(this).data('id'),
    'problem'  : problem,
    'action'   : action,
    'signed'   : signed,
    'date'     : date

    });
        }else{
            questiondata.push({
    'answer': $(this).val(),
    'question' : $(this).data('id')
    });
        }
        
    });

    fridgeCheck = $(".fcheck:checked").val();

    if(fridgeCheck === "yes"){
        $fcactionTaken = $("#fridgeCAction").val();
    }

    dishTemp = $("#dishTemp").val();
    unitClean = $(".unitClean:checked").val();
    TimeTaken = $("#dishTime").val();

    data.push({
        'fridgedata'    : fridgedata,
        'freezerdata'   : freezerdata,
        'dutyChef'      : duty,
        'questiondata'  : questiondata,
        'position'      : position,
        'batch'         : batchdata,
        'signedFridges' : signedFridges,
        'signedFreezera': signedFreezera,
        'signedFChecks' : signedFChecks,
        'signedOpening' : signedOpening,
        'signedDishes'  : signedDishes,
        'fridgeCheck'   : fridgeCheck,
        'dishTemp'      : dishTemp,
        'unitClean'     : unitClean,
        'TimeTaken'     : TimeTaken

    })

    console.log(fridgedata);
    console.log(freezerdata);
    console.log(duty);
    console.log(questiondata);
    console.log(batchdata);
    console.log(position);
    console.log(signedFridges);
    console.log(signedFreezera);
     console.log(signedFChecks);
     console.log(signedDishes);
     console.log(signedOpening);
     console.log(fridgeCheck);
     console.log(data);
     morningChecks.send(data);
}

morningChecks.send = function(data){
 
 $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        async: true, 
        data: {
            action : "morningChecks",
            User   : data
        },
        success: function(result)
        {
            msg = "You have successfully saved todays mornign checks, thank you."
            $('#mcmessageBox').html(result).delay(5000);
            window.location ="checklists.php"

        }
    });
}