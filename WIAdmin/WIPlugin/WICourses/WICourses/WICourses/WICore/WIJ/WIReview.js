$(document).ready(function(){

});

var WIReview = {};

WIReview.reviewSave = function(){
	event.preventDefault();
	var review = $("#new-review").val();
	var new_rating = WIReview.showRate();
	var product_id = $("#product").attr('product');
	
	 $.ajax({
            url: "WICore/WIClass/WIAjax.php",
            type: "POST",
            data: { 
                action : "saveReview",
                review  : review,
                new_rating  : new_rating,
                product_id  : product_id
            },
            error: function(xhr, status, error) {
      var err = eval("(" + xhr.responseText + ")");
      alert(err.Message);
    },
            success : function(result){
            $("#reviews").html(result);
    }

    });
}

WIReview.showRating = function(){
  console.log(rating);
}

WIReview.showRate = function(){
   return rating;
}

WIReview.starMark = function(item){
	console.log(item);
	var count = item.id[0];
	rating = count;
	var subid = item.id.substring(1);
	console.log(subid);
	for (var i=0;i<5;i++)
	{
		if(i<count)
		{	
		 document.getElementById((i+1)+subid).style.color = "orange";
		}else{
		document.getElementById((i+1)+subid).style.color = "black";
		}
	}
}




