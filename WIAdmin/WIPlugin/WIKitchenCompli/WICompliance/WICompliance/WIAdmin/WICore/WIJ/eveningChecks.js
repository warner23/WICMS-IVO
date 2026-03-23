 $(document).ready(function(){


  
});  

var eveningChecks = {};

eveningChecks.save = function(){

    signedFridges   = $('#fridgeTemps').val();
    signedFreezera  = $('#freezerTemps').val();
    signedClosing   = $('#ClosingChecks').val();
    signedFChecks   = $('#fridgeChecks').val();
    signedDishes    = $('#dishwasherChecks').val();
    
    var questiondata =  [];
    var fridgedata =  [];
    var freezerdata =  [];
    var batchdata =  [];
    var cookeddata =  [];

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


    $(".cooked").each(function(){

        id = $(this).attr('id');

             product        = $("#cooked-"+id).val(),
             temp           = $("#CTemp-"+id).val(),
             method         = $(".cr-"+id+":checked").val(),
             initials       = $("#cinitials-"+id).val();

        var cooked =
             {
                UserData:
                {
                    product          : product,
                    temp             : temp,
                    method           : method,
                    initials         : initials
                },
                FieldId:
                {
                    product        : "cooked-"+id,
                    temp           : "CTemp-"+id,
                    method         : "cr-"+id+":checked",
                    initials       : "cinitials-"+id
                }
             };

        
        cookeddata.push({
        'cooked' :cooked

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
        'questiondata'  : questiondata,
        'batch'         : batchdata,
        'cooked'        : cookeddata,
        'signedFridges' : signedFridges,
        'signedFreezera': signedFreezera,
        'signedFChecks' : signedFChecks,
        'signedClosing' : signedClosing,
        'signedDishes'  : signedDishes,
        'fridgeCheck'   : fridgeCheck,
        'dishTemp'      : dishTemp,
        'unitClean'     : unitClean,
        'TimeTaken'     : TimeTaken

    })

    console.log(fridgedata);
    console.log(freezerdata);
    console.log(questiondata);
    console.log(batchdata);
    console.log(cookeddata);
    console.log(signedFridges);
    console.log(signedFreezera);
     console.log(signedFChecks);
     console.log(signedDishes);
     console.log(signedClosing);
     console.log(fridgeCheck);
     console.log(data);
     eveningChecks.send(data);
}

eveningChecks.send = function(data){
 
 $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        async: true, 
        data: {
            action : "eveningChecks",
            User   : data
        },
        success: function(result)
        {
            msg = "You have successfully saved todays mornign checks, thank you."
            $('#ecmessageBox').html(result).delay(5000);
            //window.location = "checklists.php";
        }
    });
}