$(document).ready(function() {
	$('.fancybox').fancybox();
  
  $("#showroom-map").click(function(){
		$("#inline1").attr("style","");
	});
	
	
	$("#make").change(function(){
		$("#frmsearch").submit();
	});
	$("#body_style").change(function(){
		$("#frmsearch").submit();
	});
	$("#fuel_type").change(function(){
		$("#frmsearch").submit();
	});
	$("#sort_by").change(function(){
		$("#frmsearch").submit();
	});
	$("#budget").change(function(){
		$("#frmsearch").submit();
	});
	
	$(".btn-sell-submit").click(function(){	
		
		if($("#customer_mobile").val() == "" && $("#customer_email").val() == "")
		{
			alert("Please enter valid email or mobile no");
			return false;
		}
		
		var a = $("#customer_email").val();
		var filter =  /\S+@\S+\.\S+/;
		if (a != "" && !filter.test(a)) {
			alert("Please enter valid email");
			return false;
		}
		
		var a = $("#customer_mobile").val();
		var filter = /^[0-9]{1,10}$/;
		if (a != "" && !filter.test(a)) {
			alert("Please enter valid mobile no");
			return false;
		}
		
		$("#sell-form").submit();
		
	});
	
	$(".btn-search-reset").click(function(){
		$("#make").val('');
		$("#body_style").val('');
		$("#body_style").val('');
		$("#budget").val('');
		$("#sort_by").val('');
		$("#frmsearch").submit();
	});
	$(".btn-by-phone").click(function(){
		var a = $("#customer_mobile").val();
		var filter = /^[0-9]{1,10}$/;
		if (filter.test(a)) {
			$("#frm-enquiry").submit();
		}
		else {
			alert("invalid phone number");
			return false;
		}
	});
	$(".btn-by-email").click(function(){
		var a = $("#customer_email").val();
		var filter =  /\S+@\S+\.\S+/;
		if (filter.test(a)) {
			$("#frm-enquiry").submit();
		}
		else {
			alert("invalid email");
			return false;
		}
	});
	
	$(".select-make").change(function(){
		var year = $(this).val();
		$("option",$("#car_make")).remove();
		$("option",$("#car_model")).remove();
		$("<option value=''></option>").text("Select Model").appendTo($("#car_model"));
         $.ajax({
            type: "post",
            data: "year=" +year,
            url: base_url() + "used-cars/get-make-by-year",
            async: false,
            dataType: "html",
            success: function(response) {
				var result = $.parseJSON(response);
				$("<option value=''></option>").text("Select Make").appendTo($("#car_make"));
				$.each(result ,function(a,b){
				//{
					//alert(b.make_id);
					$("<option value='"+b.make_id+"'></option>").text(b.make_name).appendTo($("#car_make"));
				});
			   
            }
        });
        
        
    });

	$("#car_make").change(function(){
		var make_id = $(this).val();
		var year = $(".select-make").val();
		$("option",$("#car_model")).remove();
         $.ajax({
            type: "post",
            data: "make_id=" +make_id+"&year=" +year,
            url: base_url() + "used-cars/get-model-by-make",
            async: false,
            dataType: "html",
            success: function(response) {
				var result = $.parseJSON(response);
				$("<option value=''></option>").text("Select Model").appendTo($("#car_model"));
				$.each(result ,function(a,b){
				//{
					//alert(b.make_id);
					$("<option value='"+b.model_id+"'></option>").text(b.model_name).appendTo($("#car_model"));
				});
			   
            }
        });
        
        
    });
	
});

/**
 * obtains base url from hidden
 * input stored at start of every page.
 */
base_url = function() {
    var base_url = window.location.origin + "/";
    return base_url;
};