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
	
	$(".mark-as-sold").click(function() {
		if(confirm('Are you sure you want to mark as sold??'))
		{
			var stockid = $(this).attr("stock-id");
			$.post(base_url() + 'inventory/mark-sold', {
            id: stockid
			}, function(response) {
				$.fancybox({
					modal: false,
					content: response
				});

			});
		}
		
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
	
	$("input[type='radio'][name='hypothecation']").click(function (e) {
		var selected = $("input[type='radio'][name='hypothecation']:checked");
		if(selected.val() > 0)
		{
			$(".financier_name").attr("style","");
		}
		else
		{
			$(".financier_name").attr("style","display:none");
		}
	});
	
	$("input[type='radio'][name='hpa']").click(function (e) {
		var selected = $("input[type='radio'][name='hpa']:checked");
		
		if(selected.val() == 'Yes')
		{
			$(".buyer_financiers_name").attr("style","");
		}
		else
		{
			$(".buyer_financiers_name").attr("style","display:none");
		}
	});
	
	
	$(".hypothecationradio").click(function (e) {
		var selected = $("input[type='radio'][name='hypothecation']:checked");
		if(selected.val() > 0)
		{
			$(".financiersname").attr("style","");
		}
		else
		{
			$(".financiersname").attr("style","display:none");
		}
	});
	
	$(".hparadio").click(function (e) {
		var selected = $("input[type='radio'][name='hpa']:checked");
		if(selected.val() > 0)
		{
			$(".buyerfinanciersname").attr("style","");
		}
		else
		{
			$(".buyerfinanciersname").attr("style","display:none");
		}
	});
	
	$("#sellermobile").click(function (e) {
		var customer_mobile = $("#seller_mobile").val();
		if(customer_mobile == "")
		{
			alert("Please enter seller mobile.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "customer_mobile=" +customer_mobile,
				url: base_url() + "customer/get-customer",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#seller_id").val(b.cust_id);
						if(b.cust_id != 0)
						{
								$("#seller_number").val(b.customer_id);
								$("#seller_name").val(b.customer_name);
						}
						
					});
				}	
					
			});
	
	});
	
	$("#buyermobile").click(function (e) {
		var customer_mobile = $("#buyer_mobile").val();
		if(customer_mobile == "")
		{
			alert("Please enter buyer mobile.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "customer_mobile=" +customer_mobile,
				url: base_url() + "customer/get-customer",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#buyer_id").val(b.cust_id);
						if(b.cust_id != 0)
						{
								$("#buyer_number").val(b.customer_id);
								$("#buyer_name").val(b.customer_name);
						}
						
					});
				}	
					
			});
	
	});
	
	$("#buyer_financiers_name").change(function() {
		if($("#buyer_financiers_name").val() == 'other')
		{
			$("#txt_buyer_financiers_name").attr("style","")			
		}
		else
		{
			$("#txt_buyer_financiers_name").attr("style","display:none")			
		}
	});
	$(".vehiclelist").click(function (e) {
		var reg_no = $("#reg_no").val();
		
		if(reg_no == "")
		{
			alert("Please enter Reg No.");
			return false;
		}
		$.post(base_url() + 'rctransfer/get-vehicle-list', {
		reg_no: "'"+reg_no+"'"
		}, function(response) {
			
			$.fancybox({
				modal: false,
				content: response
			});

		});
	});
	
	
	
	$("#rct_id").change(function (e) {
		var rct_id = $("#rct_id").val();
		if(rct_id == "")
		{
			alert("Please enter RC Transfer ID.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "rct_id=" +rct_id,
				url: base_url() + "rctransfer/get-rc-detail",
				async: false,
				dataType: "html",
				success: function(response) {
						
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#reg_no").val(b.registration_no);
						$("#make_name").val(b.make_name);
						$("#model_name").val(b.model_name);
						$("#variant_name").val(b.variant_name);
						$("#make_year").val(b.make_year);
						$("#make_month").val(b.make_month);
						$("#fuel_type").val(b.fuel_type);
						$("input[name=hypothecation][value='"+b.hypothecation+"']").prop("checked",true);
						
						$("#financier_name").val(b.seller_financiers_name);
						$("#purchase_date").val(b.purchase_date);
						$("#delivery_date").val(b.delivery_date);
						$("#delivered_by").val(b.delivered_by);
						$("#stock_id").val(b.stock_id);
						$("#seller_id").val(b.seller_id);
						$("#seller_number").val(b.seller_number);
						$("#seller_mobile").val(b.seller_mobile);
						$("#seller_name").val(b.seller_name);
						$("#buyer_id").val(b.buyer_id);
						$("#buyer_number").val(b.buyer_number);
						$("#buyer_mobile").val(b.buyer_mobile);
						$("#buyer_name").val(b.buyer_name);
						$("#buyer_address").val(b.buyer_address);
						$("#buyer_city").val(b.buyer_city);
						$("#buyer_state").val(b.buyer_state);
						$("#buyer_pin").val(b.buyer_pin);
						$("#agent_id").val(b.agent_id);
						$("#agent_number").val(b.agent_number);
						$("#agent_mobile").val(b.agent_mobile);
						$("#agent_name").val(b.agent_name);
						if(b.original_rc_file != "")
						{
							$('#original_rc_file').parent().removeClass( "btn-red" );
							$('#original_rc_file').parent().addClass( "btn-green" );
							$(".btn_original_rc_file").attr("style","");
							$(".btn_original_rc_file").attr("file-name",b.original_rc_file);
						}
						$("#original_rc_file_status").val(b.original_rc_file_status);
						
						var rc_date = b.original_rc_date.split('-');
						$("#original_rc_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.insurance_file != "")
						{
							$('#insurance_file').parent().removeClass( "btn-red" );
							$('#insurance_file').parent().addClass( "btn-green" );
							$(".btn_insurance_file").attr("style","");
							$(".btn_insurance_file").attr("file-name",b.insurance_file);
						}
						$("#insurance_file_status").val(b.insurance_file_status);
						
						var rc_date = b.insurance_date.split('-');
						$("#insurance_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.tto_set_file != "")
						{
							$('#tto_set_file').parent().removeClass( "btn-red" );
							$('#tto_set_file').parent().addClass( "btn-green" );
							$(".btn_tto_set_file").attr("style","");
							$(".btn_tto_set_file").attr("file-name",b.tto_set_file);
						}
						$("#tto_set_file_status").val(b.tto_set_file_status);
						
						var rc_date = b.tto_set_date.split('-');
						$("#tto_set_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.form35_file != "")
						{
							$('#form35_file').parent().removeClass( "btn-red" );
							$('#form35_file').parent().addClass( "btn-green" );
							$(".btn_form35_file").attr("style","");
							$(".btn_form35_file").attr("file-name",b.form35_file);
						}
						$("#form35_file_status").val(b.form35_file_status);

						var rc_date = b.form35_date.split('-');
						$("#form35_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.affidavit_file != "")
						{
							$('#affidavit_file').parent().removeClass( "btn-red" );
							$('#affidavit_file').parent().addClass( "btn-green" );
							$(".btn_affidavit_file").attr("style","");
							$(".btn_affidavit_file").attr("file-name",b.affidavit_file);
						}
						$("#affidavit_file_status").val(b.affidavit_file_status);
					
						var rc_date = b.affidavit_date.split('-');
						$("#affidavit_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.letter_head_file != "")
						{
							$('#letter_head_file').parent().removeClass( "btn-red" );
							$('#letter_head_file').parent().addClass( "btn-green" );
							$(".btn_letter_head_file").attr("style","");
							$(".btn_letter_head_file").attr("file-name",b.letter_head_file);
						}
						$("#letter_head_file_status").val(b.letter_head_file_status);
						
						var rc_date = b.letter_head_date.split('-');
						$("#letter_head_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.id_proof_1_file != "")
						{
							$('#id_proof_1_file').parent().removeClass( "btn-red" );
							$('#id_proof_1_file').parent().addClass( "btn-green" );
							$(".btn_id_proof_1_file").attr("style","");
							$(".btn_id_proof_1_file").attr("file-name",b.id_proof_1_file);
						}
						$("#id_proof_1_file_status").val(b.id_proof_1_file_status);
						
						var rc_date = b.id_proof_1_date.split('-');
						$("#id_proof_1_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.id_proof_2_file != "")
						{
							$('#id_proof_2_file').parent().removeClass( "btn-red" );
							$('#id_proof_2_file').parent().addClass( "btn-green" );
							$(".btn_id_proof_2_file").attr("style","");
							$(".btn_id_proof_2_file").attr("file-name",b.id_proof_2_file);
						}
						$("#id_proof_2_file_status").val(b.id_proof_2_file_status);
						
						var rc_date = b.id_proof_2_date.split('-');
						$("#id_proof_2_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.address_proof_1_file != "")
						{
							$('#address_proof_1_file').parent().removeClass( "btn-red" );
							$('#address_proof_1_file').parent().addClass( "btn-green" );
							$(".btn_address_proof_1_file").attr("style","");
							$(".btn_address_proof_1_file").attr("file-name",b.address_proof_1_file);
						}
						$("#address_proof_1_file_status").val(b.address_proof_1_file_status);
						
						var rc_date = b.address_proof_1_date.split('-');
						$("#address_proof_1_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.address_proof_2_file != "")
						{
							$('#address_proof_2_file').parent().removeClass( "btn-red" );
							$('#address_proof_2_file').parent().addClass( "btn-green" );
							$(".btn_address_proof_2_file").attr("style","");
							$(".btn_address_proof_2_file").attr("file-name",b.address_proof_2_file);
						}
						$("#address_proof_2_file_status").val(b.address_proof_2_file_status);
					
						var rc_date = b.address_proof_2_date.split('-');
						$("#address_proof_2_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.buyer_id_proof_1_file != "")
						{
							$('#buyer_id_proof_1_file').parent().removeClass( "btn-red" );
							$('#buyer_id_proof_1_file').parent().addClass( "btn-green" );
							$(".btn_buyer_id_proof_1_file").attr("style","");
							$(".btn_buyer_id_proof_1_file").attr("file-name",b.buyer_id_proof_1_file);
						}
						$("#buyer_id_proof_1_file_status").val(b.buyer_id_proof_1_file_status);
						
						var rc_date = b.buyer_id_proof_1_date.split('-');
						$("#buyer_id_proof_1_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.buyer_id_proof_2_file != "")
						{
							$('#buyer_id_proof_2_file').parent().removeClass( "btn-red" );
							$('#buyer_id_proof_2_file').parent().addClass( "btn-green" );
							$(".btn_buyer_id_proof_2_file").attr("style","");
							$(".btn_buyer_id_proof_2_file").attr("file-name",b.buyer_id_proof_2_file);
						}
						$("#buyer_id_proof_2_file_status").val(b.buyer_id_proof_2_file_status);
						
						var rc_date = b.buyer_id_proof_2_date.split('-');
						$("#buyer_id_proof_2_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.buyer_address_proof_1_file != "")
						{
							$('#buyer_address_proof_1_file').parent().removeClass( "btn-red" );
							$('#buyer_address_proof_1_file').parent().addClass( "btn-green" );
							$(".btn_buyer_address_proof_1_file").attr("style","");
							$(".btn_buyer_address_proof_1_file").attr("file-name",b.buyer_address_proof_1_file);
						}
						$("#buyer_address_proof_1_file_status").val(b.buyer_address_proof_1_file_status);
						
						var rc_date = b.buyer_address_proof_1_date.split('-');
						$("#buyer_address_proof_1_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.buyer_address_proof_2_file != "")
						{
							$('#buyer_address_proof_2_file').parent().removeClass( "btn-red" );
							$('#buyer_address_proof_2_file').parent().addClass( "btn-green" );
							$(".btn_buyer_address_proof_2_file").attr("style","");
							$(".btn_buyer_address_proof_2_file").attr("file-name",b.buyer_address_proof_2_file);
						}
						$("#buyer_address_proof_2_file_status").val(b.buyer_address_proof_2_file_status);
						
						var rc_date = b.buyer_address_proof_2_date.split('-');
						$("#buyer_address_proof_2_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						if(b.puc_file != "")
						{
							$('#puc_file').parent().removeClass( "btn-red" );
							$('#puc_file').parent().addClass( "btn-green" );
							$(".btn_puc_file").attr("style","");
							$(".btn_puc_file").attr("file-name",b.puc_file);
						}
						$("#puc_file_status").val(b.puc_file_status);
						
						var rc_date = b.puc_date.split('-');
						$("#puc_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						$("#transfer_by").val(b.transfer_by);
						$("#transfer_type").val(b.transfer_type);
						$("#buyer_financiers_name").val(b.buyer_financiers_name);
						$("#transfer_from").val(b.transfer_from);
						$("#transfer_to").val(b.transfer_to);
						$("#cng_vendor").val(b.cng_vendor);
						$("#file_status").val(b.file_status);
						$("#login_date").val(b.login_date);
						$("#login_remarks").val(b.login_remarks);
						$("#transfer_status").val(b.transfer_status);
						$("#transfer_date").val(b.transfer_date);
						$("#discrepency_details").val(b.discrepency_details);
						$("#resubmission_date").val(b.resubmission_date);
						$("#resubmission_remarks").val(b.resubmission_remarks);
					});
				}	
					
			});
	
	});
	$("#con_report").click(function (e) {
		e.preventDefault();
		$("#frmsearch").attr("action", base_url() + 'rctransfer/export');	
		$("#frmsearch").submit();
	});
	$(".btn_view").click(function (e) {
		 $(this).attr('href',parent_base_url() + "images/rctransfer/"+$(this).attr("file-name"));
	});
	
	$("#vehicledetail").click(function (e) {
		var reg_no = $("#reg_no").val();
		if(reg_no == "")
		{
			alert("Please enter Reg No.");
			return false;
		}
		
		$.post(base_url() + 'rctransfer/get-inventory-list', {
		reg_no: "'"+reg_no+"'"
		}, function(response) {
			
			$.fancybox({
				modal: false,
				content: response
			});

		});
		/**$.ajax({
				type: "post",
				data: "reg_no=" +reg_no,
				url: base_url() + "inventory/get-vehicle-detail",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#make_name").val(b.make_name);
						$("#model_name").val(b.model_name);
						$("#variant_name").val(b.variant_name);
						$("#make_year").val(b.make_year);
						$("#make_month").val(b.make_month);
						$("#fuel_type").val(b.fuel_type);
						$("input[name=hypothecation][value='"+b.hypothecation+"']").prop("checked",true);
						if(b.hypothecation == 0)
						{
							$(".financier_name").attr("style","display:none");
						}
						$("#financier_name").val(b.seller_financiers_name);
						if ( $("#financier_name option[value='"+b.seller_financiers_name+"']").length == 0 ){
							$("#txt_financier_name").val(b.seller_financiers_name);
							$("#financier_name").val('other');
						}
						else
						{
							$("#financier_name").val(b.seller_financiers_name);
						}
						if($("#financier_name").val() == 'other')
						{
							$("#txt_financier_name").attr("style","")			
						}
						else
						{
							$("#txt_financier_name").attr("style","display:none")			
						}
						if(b.hpa == 1)	b.hpa = 'Yes'; else b.hpa='No';
						$("input[name=hpa][value='"+b.hpa+"']").prop("checked",true);
						if(b.hpa == 'No')
						{
							$(".buyer_financiers_name").attr("style","display:none");
						}
						if ( $("#buyer_financiers_name option[value='"+b.buyer_financiers_name+"']").length == 0 ){
							$("#txt_buyer_financiers_name").val(b.buyer_financiers_name);
							$("#buyer_financiers_name").val('other');
						}
						else
						{
							$("#buyer_financiers_name").val(b.buyer_financiers_name);
						}
						if($("#buyer_financiers_name").val() == 'other')
						{
							$("#txt_buyer_financiers_name").attr("style","")			
						}
						else
						{
							$("#txt_buyer_financiers_name").attr("style","display:none")			
						}
						
						if(b.purchase_date != '0000-00-00')
						{
							var rc_date = b.purchase_date.split('-');
							$("#purchase_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						}
						
						if(b.delivery_date != '0000-00-00')
						{
							var rc_date = b.delivery_date.split('-');
							$("#delivery_date").val(rc_date[2]+"-"+rc_date[1]+"-"+rc_date[0]);
						}
						$("#delivered_by").val(b.delivered_by);
						$("#stock_id").val(b.stock_id);
						$("#seller_id").val(b.seller_id);
						$("#seller_number").val(b.seller_number);
						$("#seller_mobile").val(b.seller_mobile);
						$("#seller_name").val(b.seller_name);
						$("#buyer_id").val(b.buyer_id);
						$("#buyer_number").val(b.buyer_number);
						$("#buyer_mobile").val(b.buyer_mobile);
						$("#buyer_name").val(b.buyer_name);
						$("#buyer_address").val(b.buyer_address);
						$("#buyer_city").val(b.buyer_city);
						$("#buyer_state").val(b.buyer_state);
						$("#buyer_pin").val(b.buyer_pin);
					});
				}	
					
			});**/
	
	});
	
	$("#file_status").change(function(e) {
		if($("#file_status").val()  == 'Logged-in')
		{
			$("#login_date").prop("disabled",false);
			$("#transfer_status").prop("disabled",false);
		}
		else
		{
			$("#login_date").val('');
			$("#transfer_status").val('');
		}
		
	});
	
	$("#transfer_status").change(function(e) {
		if($("#transfer_status").val()  == 'Transferred')
		{
			$("#transfer_date").prop("disabled",false);
		}
		else if($("#transfer_date").val()  == '')
		{
			$("#transfer_date").prop("disabled",true);
		}
		
	});
	
	$("#transfer_status").change(function(e) {
		if($("#transfer_status").val()  == 'Resubmitted')
		{
			$("#resubmission_date").prop("disabled",false);
		}
		else if($("#resubmission_date").val() == '')
		{
			$("#resubmission_date").prop("disabled",true);
		}
		
	});
	
	$("#agentnumber").click(function (e) {
		var agent_number = $("#agent_number").val();
		if(agent_number == "")
		{
			alert("Please enter agent ID.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "agent_number=" +agent_number,
				url: base_url() + "rctransfer/get-agent",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#agent_id").val(b.agent_id);
						if(b.cust_id != 0)
						{
								$("#agent_mobile").val(b.agent_mobile);
								$("#agent_name").val(b.agent_name);
						}
						
					});
				}	
					
			});
	
	});
	
	$("#agentmobile").click(function (e) {
		var agent_mobile = $("#agent_mobile").val();
		if(agent_mobile == "")
		{
			alert("Please enter agent mobile.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "agent_mobile=" +agent_mobile,
				url: base_url() + "rctransfer/get-agent",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#agent_id").val(b.agent_id);
						if(b.cust_id != 0)
						{
								$("#agent_number").val(b.agent_number);
								$("#agent_name").val(b.agent_name);
						}
						
					});
				}	
					
			});
	
	});
	
	$('.upload').change(function(event) { 
		$( this).parent().removeClass( "btn-red" );
		$( this).parent().addClass( "btn-green" );
	});
	
	
	$("#sellernumber").click(function (e) {
		var seller_number = $("#seller_number").val();
		if(seller_number == "")
		{
			alert("Please enter seller ID.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "customer_id=" +seller_number,
				url: base_url() + "customer/get-customer",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#seller_id").val(b.cust_id);
						if(b.cust_id != 0)
						{
								$("#seller_mobile").val(b.customer_mobile);
								$("#seller_name").val(b.customer_name);
						}
						
					});
				}	
					
			});
	
	});
	
	$("#buyernumber").click(function (e) {
		var buyer_number = $("#buyer_number").val();
		if(buyer_number == "")
		{
			alert("Please enter Buyer ID.");
			return false;
		}
		$.ajax({
				type: "post",
				data: "customer_id=" +buyer_number,
				url: base_url() + "customer/get-customer",
				async: false,
				dataType: "html",
				success: function(response) {			
					var result = $.parseJSON(response);
					$.each(result ,function(a,b){
						$("#buyer_id").val(b.cust_id);
						if(b.cust_id != 0)
						{
								$("#buyer_mobile").val(b.customer_mobile);
								$("#buyer_name").val(b.customer_name);
						}
						
					});
				}	
					
			});
	
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
	
	$("#financiers_name").change(function() {
		if($("#financiers_name").val() == 'other')
		{
			$("#txt_financiers_name").attr("style","");		
		}
		else
		{
			$("#txt_financiers_name").attr("style","display:none");			
		}
	});
	
	$("#financier_name").change(function() {
		if($("#financier_name").val() == 'other')
		{
			$("#txt_financier_name").attr("style","");			
		}
		else
		{
			$("#txt_financier_name").attr("style","display:none");			
		}
	});
	$("#buyer_financiers_name").change(function() {
		if($("#buyer_financiers_name").val() == 'other')
		{
			$("#txt_buyer_financiers_name").attr("style","");			
		}
		else
		{
			$("#txt_buyer_financiers_name").attr("style","display:none");			
		}
	});
	
	
	$("#agent_state").change(function(){
		$("option",$("#agent_city")).remove();
		
		$.ajax({
            type: "post",
            data: "state=" +$(this).val(),
            url: base_url() + "showroom/get-cities",
            async: false,
            dataType: "html",
            success: function(response) {
				
				var result = $.parseJSON(response);
				$("<option value=''>Select</option>").text("Select").appendTo($("#agent_city"));
				$.each(result ,function(a,b){
					//alert(b);
					$("<option value='"+b.city_name+"'></option>").text(b.city_name).appendTo($("#agent_city"));
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

parent_base_url = function() {
    var base_url = "http://directcars.in/";
    return base_url;
};
validate_stock=function()
{
	var month = $("#month").val();
	var err =  "";
	if(month == "")
	{
		err += "Please select  month  \n";
	}
	var year = $("#year").val();
	if(year == "")
	{
		err += "Please select  year  \n";
	}
	var make = $("#make").val();
	if(make == "")
	{
		err += "Please select  make  \n";
	}
	var model = $("#model").val();
	if(model == "")
	{
		err += "Please select  model  \n";
	}
	var variant = $("#variant").val();
	if(variant == "")
	{
		err += "Please select  variant  \n";
	}
	if(err != "")
	{
		alert(err); return false;
	}
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