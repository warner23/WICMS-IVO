 $(document).ready(function(){

  
});  

var WICompliance = {};

WICompliance.faultFridge = function(id){
    console.log("faulty");
    console.log($("#fridge-"+id));
    $("#fridge-"+id).attr('type', 'text');
    $("#fridge-"+id).val( 'faulty');
}


WICompliance.faultFreezer = function(id){
    $("#"+id).attr('type', 'text');
    $("#"+id).val( 'faulty');
}

WICompliance.defrostingFreezer = function(id){
    $("#"+id).attr('type', 'text');
    $("#"+id).val( 'defrosting');
}

WICompliance.addFridgeTemps = function(number){
    last_id = $("ul#tempfridges>li:last").attr('id');
    console.log(last_id);
    for($i = 0; $i < number;$i++){
        last_id++;
        $('<li id="'+  last_id  +'"><div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 fridgeTemps">'+
                    '<label>'+  last_id  +'</label>'+
                    '</div>'+
                    '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">'+
                    '<input type="number" class="col-lg-8 col-xs-12 fridges" id="fridge-'+ last_id +'">C'+


                     '<a class="btn btn-fridges dropdown-toggle" data-toggle="dropdown" href="#">Options'+
                    '<span class=""></span>'+
                                  '</a>'+
                                  '<ul class="dropdown-menu">'+
                                      '<li>'+
        '<a href="javascript:void(0);" onclick="WICompliance.faultFridge(`'+ last_id +'`);">'+
'<i class="icon-edit glyphicon glyphicon-edit"></i>Fridge Faultly'+
                                              
                                          '</a>'+
                                      '</li>'+
                                  '</ul>'+
'</div></li>').insertAfter('ul#tempfridges>li:last');
    }
    
}

WICompliance.addFreezerTemps = function(number){
    
    
    for($i = 0; $i < number;$i++){
    last_id = $("ul#tempfreezers>li:last").data('id');
    next_id = WICompliance.nextCharacter(last_id);        
        $('<li id="Freezer-'+  next_id  +'" data-id="'+  next_id  +'"><div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 freezerTemps">'+
                    '<label>'+  next_id  +'</label>'+
                    '</div>'+
                    '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">'+
                    '<input type="number" class="col-lg-8 col-xs-12 freezers" id="'+ last_id +'">C'+


                     '<a class="btn btn-fridges dropdown-toggle" data-toggle="dropdown" href="#">Options'+
                    '<span class=""></span>'+
                                  '</a>'+
                                  '<ul class="dropdown-menu">'+
                                      '<li>'+
        '<a href="javascript:void(0);" onclick="WICompliance.faultFreezer(`'+ last_id +'`);">'+
'<i class="icon-edit glyphicon glyphicon-edit"></i>Freezer Faultly'+
                                              
                                          '</a>'+
                                      '</li>'+

                                      '<li>'+
            '<a href="javascript:void(0);" onclick="WICompliance.defrostingFreezer(`'+ last_id +'`);">'+
                        '<i class="icon-pencil glyphicon glyphicon-pencil"></i>Freezer switched off defrosting'+
                                          '</a>'+
                                      '</li>'+
                                  '</ul>'+
'</div></li>').insertAfter('ul#tempfreezers>li:last');
    }
    
}

WICompliance.nextCharacter = function(letter){
    return String.fromCharCode(letter.charCodeAt(0) + 1);
}

WICompliance.addBatch = function(){

    last_id = $("ul#batch>li:last").attr('id');
    console.log(last_id);

    for($i = 0; $i < number;$i++){
        last_id++;
    $('<li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 batch" id="batch-'+ last_id +'"  data-id="'+ last_id +'">'+
                '<label>Product</label>'+
                '<input type="text" class="batchCook" id="product-'+ last_id +'" name="product" placeholder="Chicken">'+
                '</div>'+
                '<div>'+
                '<label>End of Cooking Time</label>'+
                '<input type="text" class="batchCook" id="eofct-'+ last_id +'" placeholder="10:40am" name="end_cook_time">'+
                '</div>'+
                '<div>'+
                '<label>End of cooking Temp</label>'+
                '<input type="text" class="batchCook" id="eoct-'+ last_id +'" placeholder="82 C" name="end_cook_temp">'+
                '</div>'+
                '<div>'+
                '<label>Cooking method</label>'+
                '<input type="text" class="batchCook" id="method-'+ last_id +'" placeholder="Oven" name="method">'+
                '</div>'+
                '<div>'+
                '<label>End of cooling Time</label>'+
                '<input type="text" class="batchCook" id="eoctime-'+ last_id +'" placeholder="11:40 am" name="end_cool_time">'+
                '</div>'+
                '<div>'+
                '<label>End of cooling Temp</label>'+
                '<input type="text" class="batchCook" id="eoctemp-'+ last_id +'" placeholder="2 C" name="end_cool_temp">'+
                '</div>'+
                '<div>'+
                '<label>Total Cooling time</label>'+
                '<input type="text" class="batchCook" id="totalCool-'+ last_id +'" placeholder="60 mins" name="total_cool_temp">'+
                '</div>'+
                '<div>'+
                '<label>Time in fridge</label>'+
                '<input type="text" class="batchCook" id="time_in_fridge-'+ last_id +'" placeholder="11:45 am" name="time_in_fridge">'+
                '</div>'+
                '<div>'+
                '<label>initials</label>'+
                '<input type="text" class="batchCook" id="initials-'+ last_id +'" placeholder="BO" name="initials">'+
                '</div>'+
             '</li>').insertAfter('ul#batch>li:last');
          }
}


WICompliance.addCooked = function(number){

    last_id = $("ul#cooked>li:last").attr('id');
    console.log(last_id);

    for($i = 0; $i < number;$i++){
        last_id++;
    $('<li id='+ last_id +'" class="cooked">'+
        '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">'+
                '<label>Food Item</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<input type="text" class="col-lg-8 col-xs-12" placeholder="Burgers" id="cooked-'+ last_id +'">'+
                '</div>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<label>cooked or reheated</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<label for="radio">cooked</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<input type="radio" class="col-lg-8 col-xs-12 cr-'+ last_id +'" value="cooked" name="radio">'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<label for="radio">reheated</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<input type="radio" class="col-lg-8 col-xs-12 cr-'+ last_id +'" value="reheated" name="radio">'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<label for="radio">Hot Held</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<input type="radio" class="col-lg-8 col-xs-12 cr-'+ last_id +'" value="hot held" name="radio">'+
                '</div>'+
                '</div>'+
                '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">'+
                '<label>Temp</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<input type="text" class="col-lg-8 col-xs-12 cr" placeholder="78c" id="CTemp-'+ last_id +'">'+
                '</div>'+
                '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<label>initials</label>'+
                '</div>'+
                '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">'+
                '<input type="text" class="col-lg-8 col-xs-12 cr" placeholder="BO" id="cinitials-'+ last_id +'">'+
                '</div>'+
                '</div>'+
            '</div>'+
            '</li>').insertAfter('ul#cooked>li:last');
          }
}