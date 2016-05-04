$(document).ready(function() {

var global_mmv_string = Array();
$(".select-make").change(function(){
		var year = $(this).val();
		$("option",$("#make")).remove();
		$("option",$("#model")).remove();
		$("option",$("#variant")).remove();
         $.ajax({
            type: "post",
            data: "year=" +year,
            url: base_url() + "mmv/get-make-by-year",
            async: false,
            dataType: "html",
            success: function(response) {
				var result = $.parseJSON(response);
				$("<option value=''></option>").text("Select").appendTo($("#make"));
				$.each(result ,function(a,b){
				//{
					//alert(b.make_id);
					$("<option value='"+b.make_id+"'></option>").text(b.make_name).appendTo($("#make"));
				});
			   
            }
        });
        
        
    });
	
	$("#make").change(function(){
		var make_id = $(this).val();
		
		var year = $(".select-make").val();
		$("option",$("#model")).remove();
		$("option",$("#variant")).remove();
         $.ajax({
            type: "post",
            data: "make_id=" +make_id+"&year=" +year,
            url: base_url() + "mmv/get-model-by-make",
            async: false,
            dataType: "html",
            success: function(response) {
				var result = $.parseJSON(response);
				$("<option value=''></option>").text("Select").appendTo($("#model"));
				$.each(result ,function(a,b){
				//{
					//alert(b.make_id);
					$("<option value='"+b.model_id+"'></option>").text(b.model_name).appendTo($("#model"));
				});
			   
            }
        });
        
        
    });
	
	$("#search_make").change(function(){
		var make_id = $(this).val();
		
	
		$("option",$("#model")).remove();
		
         $.ajax({
            type: "post",
            data: "make_id=" +make_id,
            url: base_url() + "inventory/get-stock-model-by-make",
            async: false,
            dataType: "html",
            success: function(response) {
				var result = $.parseJSON(response);
				$("<option value=''></option>").text("Select").appendTo($("#model"));
				$.each(result ,function(a,b){
				//{
					//alert(b.make_id);
					$("<option value='"+b.model_id+"'></option>").text(b.model_name).appendTo($("#model"));
				});
			   
            }
        });
        
        
    });
	
	$("#model").change(function(){
		var model_id = $(this).val();
		var year = $(".select-make").val();
		$("option",$("#variant")).remove();
         $.ajax({
            type: "post",
            data: "model_id=" +model_id+"&year=" +year,
            url: base_url() + "mmv/get-variant-by-model",
            async: false,
            dataType: "html",
            success: function(response) {
				var result = $.parseJSON(response);
				
				$("<option value=''></option>").text("Select").appendTo($("#variant"));
				$.each(result ,function(a,b){
				//{
					//alert(b.variant_id);
					global_mmv_string[b.variant_id] = b;
					$("<option value='"+b.variant_id+"'></option>").text(b.variant_name).appendTo($("#variant"));
				});
			   
            }
        });
        
        
    });
	
	$("#save-seller").submit(function(){
		if($("#customer_id").val() == "")
		{
			alert("please check customer exist or not");
			return false;
		}
		else
		{
			return true;
		}
	});
	
	$("#get-customer").click(function(){
		var customer_email = $("#email").val();
		var customer_mobile = $("#mobile").val();
		
		if(customer_email == "" && customer_mobile == "")
		{
			alert("please Enter email or mobile");
			
		}
		else
		{
			 $.ajax({
				type: "post",
				data: "customer_email=" +customer_email+"&customer_mobile=" +customer_mobile,
				url: base_url() + "enquiry/get-customer",
				async: false,
				dataType: "html",
				success: function(response) {
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
					//{
						
						$("#customer_id").val(b.customer_id);
						if(b.customer_id != 0)
						{
							$("#email").val(b.email);
							$("#mobile").val(b.mobile);
							$("#name").val(b.name);
							$("#customer_address").val(b.customer_address);
							$("#customer_state").val(b.customer_state);
							$.ajax({
								type: "post",
								data: "state=" +$("#customer_state").val(),
								url: base_url() + "showroom/get-cities",
								async: false,
								dataType: "html",
								success: function(response) {
									
									var result = $.parseJSON(response);
									$("<option value=''>Select</option>").text("Select").appendTo($("#customer_city"));
									$.each(result ,function(a,b){
										//alert(b);
										$("<option value='"+b.city_name+"'></option>").text(b.city_name).appendTo($("#customer_city"));
									});
								   
								}
							});
							$("#customer_city").val(b.customer_city);
							$("#customer_pin").val(b.customer_pin);
							$("#gender").val(b.gender);
							$("#profession").val(b.profession);
							$("#annual_income").val(b.annual_income);
						}
						
					});
				}	
					
			});
		}
	});
	
	$("#variant").change(function(){
		$("#fuel").val(global_mmv_string[$(this).val()].fuel_type);
		$("#tranmission").val(global_mmv_string[$(this).val()].transmission_type);
		$("#mmv_id").val(global_mmv_string[$(this).val()].mmv_id);
	});
	
	$(".set-profile-img").click(function(){
		var image_id = $( "input:checked" ).val();
		window.location = base_url()+"inventory/set-profile-img/"+image_id;
	});
	
	
	$(".btn-save-tag").click(function() {
		if (confirm("Are you sure you want to save image tags!") == true) {
			$("#frmImageList").attr("action",base_url()+"inventory/image-tag");
			$("#frmImageList").submit();
			return true;
		} else {
			return false;
		}
	});
	
	$(".btn-showroom-save-tag").click(function() {
		if (confirm("Are you sure you want to save image tags!") == true) {
			$("#frmImageList").attr("action",base_url()+"showroom/image-tag");
			$("#frmImageList").submit();
			return true;
		} else {
			return false;
		}
	});
	
	$(".btn-delete-selected").click(function() {
		if (confirm("Are you sure you want to delete selected images!") == true) {
			$("#frmImageList").attr("action",base_url()+"inventory/delete-multi-images");
			$("#frmImageList").submit();
			return true;
		} else {
			return false;
		}
	});
	
	$(".btn-showroom-delete-selected").click(function() {
		if (confirm("Are you sure you want to delete selected images!") == true) {
			$("#frmImageList").attr("action",base_url()+"showroom/delete-multi-images");
			$("#frmImageList").submit();
			return true;
		} else {
			return false;
		}
	});
	
	$("#sort_by").change(function() {
		$("#frmsearch").submit();
	});
	
	$(".mark-as-hot-deal").click(function() {
		var stockid = $(this).attr("stock-id");
		$.post(base_url() + 'inventory/mark-hoat-deal', {
            id: stockid
        }, function(response) {
            $.fancybox({
                modal: false,
                content: response
            });

        });
	});
	
	$(".send-sms").click(function() {
		var count_checked = $("[name='cs[]']:checked").length;
		if(count_checked == 0)
		{
			alert("Please select atleast one car")
		}
		else
		{
			var stock_id_checked = [];
			var i = 0;
			$("[name='cs[]']:checked").each(function(){
				stock_id_checked[i] = $(this).val();
				i++;
			});
			
			$.post(base_url() + 'inventory/notify-user', {
            arrId: stock_id_checked
				}, function(response) {
					  $.fancybox({
						modal: false,
						content: response
					});

				});
		}
	});
	
	
	$("#chk_all_post").click(function() {
		var checkboxes = new Array();
        checkboxes = document.getElementsByTagName('input');
		if($("#chk_all_post").val() == 'false')
		{
			$("#chk_all_post").val(true);
			checktoggle = true;
		}
		else
		{
			$("#chk_all_post").val(false);
			checktoggle = false;
		}
		
		for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type === 'checkbox') {
                checkboxes[i].checked = checktoggle;
            }
        }
	});
	
	$(".insuranceradio").click(function (e) {
		var selected = $("input[type='radio'][name='insurance']:checked");
		if(selected.val() > 0)
		{
			$(".insurance-expiry").attr("style","");
		}
		else
		{
			$(".insurance-expiry").attr("style","display:none");
		}
	});
	
	$("#kmdriven").keypress(function (e) {
		 //if the letter is not digit then display error and don't type anything
		 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			//display error message
			$(".kmerror").text("Digits Only").show().fadeOut("slow");
				   return false;
		}
	});
	
	$("#pricegaadi").keypress(function (e) {
		 //if the letter is not digit then display error and don't type anything
		 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			//display error message
			$(".pricegaadierror").text("Digits Only").show().fadeOut("slow");
				   return false;
		}
	});
	
	
	$(".export-buyer").click(function(e) {
		e.preventDefault();
		$("#frmsearch").attr("action", base_url() + 'enquiry/export-buyer');	
		$("#frmsearch").submit();
	});
	
	$(".export-general").click(function(e) {
		e.preventDefault();
		$("#frmsearch").attr("action", base_url() + 'enquiry/export-general');	
		$("#frmsearch").submit();
	});
	
	$(".export-seller").click(function(e) {
		e.preventDefault();
		$("#frmsearch").attr("action", base_url() + 'enquiry/export-seller');	
		$("#frmsearch").submit();
	});
	
	$(".export-stock").click(function(e) {
		var actionid = $("#actionid").val();
		if(actionid == "")	actionid = 0;
		e.preventDefault();
		$("#frmsearch").attr("action", base_url() + 'inventory/export-stock/'+actionid);	
		$("#frmsearch").submit();
	});
	
	$("#regcity").change(function(){
		if($(this).val() == 'Other')
		{
			$("#otherregcitydiv").attr("style","display:block");
		}
		else
		{
			$("#otherregcitydiv").attr("style","display:none");
		}
	});
	
	$("#state").change(function(){
		$("option",$("#city")).remove();
		
		$.ajax({
            type: "post",
            data: "state=" +$(this).val(),
            url: base_url() + "showroom/get-cities",
            async: false,
            dataType: "html",
            success: function(response) {
				
				var result = $.parseJSON(response);
				$("<option value=''>Select</option>").text("Select").appendTo($("#city"));
				$.each(result ,function(a,b){
					//alert(b);
					$("<option value='"+b.city_name+"'></option>").text(b.city_name).appendTo($("#city"));
				});
			   
            }
        });
	});

	$("#customer_state").change(function(){
		$("option",$("#customer_city")).remove();
		
		$.ajax({
            type: "post",
            data: "state=" +$(this).val(),
            url: base_url() + "showroom/get-cities",
            async: false,
            dataType: "html",
            success: function(response) {
				
				var result = $.parseJSON(response);
				$("<option value=''>Select</option>").text("Select").appendTo($("#customer_city"));
				$.each(result ,function(a,b){
					//alert(b);
					$("<option value='"+b.city_name+"'></option>").text(b.city_name).appendTo($("#customer_city"));
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
validate_stock=function()
{
	var selected = $("input[type='radio'][name='insurance']:checked");
	if(selected.val() > 0)
	{
		var currentYear = (new Date).getFullYear();
		var currentMonth = (new Date).getMonth() + 1;
		if(currentYear == $("#jiyear").val()){
			if($("#jimonth").val() < currentMonth)
			{
				alert("Please select valid insurance expiry!");
				return false;
			}
		}
	}
	return true;
}