
$(document).ready(function () {

   // WICart.addTotals();
    $("body").delegate(".qty", "keyup", function(){
        var pid =  $(".qty").attr("pid");
        alert(pid);
    var qty = $("#qty_"+pid).attr('value');
    alert(qty);
    var price = $("#price_"+pid).html();
    alert(price);
        var total = qty* price;
        $(".total_"+pid).html(total);
        });

    $(document).on('input', '.qty', function(){
        var pid =  $(".qty").attr("pid");
        alert(pid);
    var qty = $("#qty_"+pid).val();
    alert(qty);
    var price = $("#price_"+pid).text();
    alert(price);
        var total = qty* price;
        $(".total_"+pid).html(total);
})

       



        $("body").delegate(".update", "click", function(){
            event.preventDefault();
            var pid = $(this).attr("update_id");

            $.ajax({
            url      : "action.php",
            method   : "POST",
            data     : {
                updateCart: 1
            },
            success  : function(data){
                //alert(data);
                $("#cart_checkout").html(data);
            }
        })

        });

                $("body").delegate(".delete", "click", function(){
            event.preventDefault();
            var pid = $(this).attr("delete_id");

            $.ajax({
            url      : "action.php",
            method   : "POST",
            data     : {
                deleteItem: 1,
                delete_id: pid
            },
            success  : function(data){
                //alert(data);
                $("#cart_checkout").html(data);
            }
        })
        }); 


        WICart.addTotals();




});




var WICart = {};

WICart.Cart = function(userId){
     event.preventDefault();
        //alert(0);

        $.ajax({
            url      : "WICore/WIClass/WIAjax.php",
            method   : "POST",
            data     : {
                action : "cart",
                getCart: 1,
                userId : userId
            },
            success  : function(data){
                //alert(data);
                $("#cart_product").html(data);
            }
    });
}

WICart.addTotals = function(){
    console.log("triggered");
   var subtotal =  $(".subtotal").val();
   console.log(subtotal);
    $(subtotal).each(function(index){
        console.log(index);
    });
    var totalPrice = 0;

    $('#cart .subtotal').each(function()
{
totalPrice += parseFloat($(this).html());
  //alert($(this).html());
});

    //add VAT
var VAT = totalPrice * 20/100;
$("#vat").text("VAT : " + VAT);

//Total Price with VAT
var total = VAT;
    $("#total").text("Total : " +totalPrice);
}