 $(document).ready(function(){

  
});  

var WIPackageBuilder = {};

WIPackageBuilder.build = function(tabname){
    var serves = $("#"+tabname).val();
    console.log(serves);

    $("."+tabname+"-buffqty").each(function(){
        var oldServe = $(this).text();
         console.log(oldServe);
         if(oldServe > 5){
            singleP = oldServe / 10 ;
         }else{
            singleP = oldServe / 2 ;
         }
         
        newServe = serves * singleP;
        console.log(newServe);
        $(this).text(newServe);
    })
}