"use strict";

var RecordId = '';
var $ = jQuery.noConflict();

$(function () {
	"use strict";
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
    onListPanel();
    resetForm("DataEntry_formId");
	onActiveStatus();
	onCountry();
	
    $('#submit-form').click(function () {
        $("#DataEntry_formId").submit();
    });

	onLoadClientData();
	
    $("#search_txt").on("input", function(){
		onLoadClientData();
    });

    $("#FileName").change(function() {
		upload_Form();
    });
	
	$('.toggle-password').on('click', function() {
		$(this).toggleClass('fa-eye-slash');
			let input = $($(this).attr('toggle'));
		if (input.attr('type') == 'password') {
			input.attr('type', 'text');
		}else {
			input.attr('type', 'password');
		}
	});
	
});

function resetForm(id) {
    $('#' + id).each(function () {
        this.reset();
    });
}

function onListPanel() {
	$('.parsley-error-list').hide();
    $('#list-panel, .btn-form').show();
    $('#form-panel, .btn-list').hide();
	$('.search').show();
}

function onFormPanel() {
	var passtype = $('#password').attr('type');
	if(passtype == 'text'){
		$(".toggle-password").removeClass("fa-eye-slash");
		$(".toggle-password").addClass("fa-eye");
		$('#password').attr('type', 'password');
	}	
    resetForm("DataEntry_formId");
	RecordId = '';
	$("#file-uploader").html('<img src="'+public_path+'/assets/images/default.png">');
	$("#photo").val('');
    $('#list-panel, .btn-form').hide();
    $('#form-panel, .btn-list').show();
    $('.search').hide();
}

function onEditPanel() {
    $('#list-panel, .btn-form').hide();
    $('#form-panel, .btn-list').show();
	$('.search').hide();
}

function onActiveStatus() {
    $.ajax({
		type : 'POST',
		url: base_url + '/backend/getUserActivesList',
		success: function (response) {
			var datalist = response;
			var html = '';
			$.each(datalist, function (key, obj) {
				html += '<option value="' + obj.id + '">' + obj.active + '</option>';
			});
			
			$("#active_id").html(html);
			$("#active_id").chosen();
			$("#active_id").trigger("chosen:updated");
        }
    });
}

function onCountry() {
    $.ajax({
		type : 'POST',
		url: base_url + '/backend/getCountryList',
		success: function (response) {
			var datalist = response;
			var html = '';
			$.each(datalist, function (key, obj) {
				html += '<option value="' + obj.id + '">' + obj.country_name + '</option>';
			});
			
			$("#country_id").html(html);
			$("#country_id").chosen();
			$("#country_id").trigger("chosen:updated");
        }
    });
}

function onLoadClientData() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/getClientData',
		data: 'search='+$("#search_txt").val(),
		success: function (response) {
			var data = response;
			var html = '';
			if(data.length>0){
				$.each(data, function (key, obj) {
					
					if(obj.photo != null){
						var photo = '<img src="'+public_path+'/media/'+obj.photo+'">';
					}else{
						var photo = '<img src="'+public_path+'/assets/images/default.png">';
					}
					
					if(obj.name != null){
						var name = obj.name;
					}else{
						var name = '';
					}
					
					if(obj.country_name != null){
						var country_name = obj.country_name;
					}else{
						var country_name = '';
					}
					
					if(obj.active_id == 1){
						var active_id = '<a onclick="onActiveInactive('+obj.id+', 2);" href="javascript:void(0);"><span title="'+TEXT['Active']+'" class="check-staff check-bg-green"><i class="fa fa-check"></i></span></a>';
					}else if(obj.active_id == 2){
						var active_id = '<a onclick="onActiveInactive('+obj.id+', 1);" href="javascript:void(0);"><span title="'+TEXT['Inactive']+'" class="check-staff check-bg-red"><i class="fa fa-times"></i></span></a>';
					}else{
						var active_id = '';
					}
					
					html += '<div class="col-md-4 col-lg-3 col-xl-3 mb-30">'
							+'<div class="tw_box">'
								+'<div class="tw_img_circle">'
									+'<a onclick="onViewData('+obj.id+');" href="javascript:void(0);">'+photo+'</a>'
								+'</div>'
								+'<div class="tw_info">'
									+'<h2><a onclick="onViewData('+obj.id+');" href="javascript:void(0);">'+name+'</a></h2>'
									+'<p>'+country_name+'</p>'
								+'</div>'
								+'<div class="tw_control">'
									+'<ul>'
										+'<li><a onclick="onViewData('+obj.id+');" href="javascript:void(0);" title="'+TEXT['View']+'"><i class="fa fa-eye"></i></a></li>'
										+'<li><a onclick="onEditData('+obj.id+');" href="javascript:void(0);" title="'+TEXT['Edit']+'"><i class="fa fa-pencil"></i></a></li>'
										+'<li><a onclick="onDelete('+obj.id+');" href="javascript:void(0)" title="'+TEXT['Delete']+'"><i class="fa fa-trash-o"></i></a></li>'
									+'</ul>'+active_id+'</div>'
							+'</div>'
						+'</div>';
				});
			}else{
				html = '<div class="col-lg-12"><div class="alert alert-warning" role="alert">'+TEXT['No data available']+'</div></div>';
			}
			
			$("#tw-loader").hide();
			$(".datalist").html(html);
        }
    });
}

function onActiveInactive(client_id, active_id) {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/userActive',
		data: 'id='+client_id+ '&active_id='+active_id,
		success: function (response) {
            var msgType = response.msgType;
            var msg = response.msg;
			
            if (msgType == "success") {
				onLoadClientData();
				onSuccessMsg(msg);
            } else {
                onErrorMsg(msg);
            }
        }
    });
}

function onConfirmWhenAddEdit() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/saveClientData',
		data: $('#DataEntry_formId').serialize(),
		success: function (response) {
            var msgType = response.msgType;
            var msg = response.msg;
			
            if (msgType == "success") {
				onLoadClientData();
				onSuccessMsg(msg);
                onListPanel();
            } else {
                onErrorMsg(msg);
            }
        }
    });
}

function onEditData(id) {
	RecordId = id;
	var msg = TEXT["Do you really want to edit this record"];
	onCustomModal(msg, "onLoadEditData");	
}

function onLoadEditData() {
	
    $.ajax({
		type : 'POST',
		url: base_url + '/backend/getClientById',
		data: 'id='+RecordId,
		success: function (response) {		
			var data = response;

			var passtype = $('#password').attr('type');
			if(passtype == 'text'){
				$(".toggle-password").removeClass("fa-eye-slash");
				$(".toggle-password").addClass("fa-eye");
				$('#password').attr('type', 'password');
			}

			$("#RecordId").val(data.id);
			$("#name").val(data.name);
			$("#email").val(data.email);
			$("#password").val(data.bactive);
			$("#phone").val(data.phone);
			$("#skype_id").val(data.skype_id);
			$("#facebook_id").val(data.facebook_id);
			$("#url").val(data.url);
			$("#city").val(data.city);
			$("#state").val(data.state);
			$("#zip_code").val(data.zip_code);
			$("#country_id").val(data.country_id).trigger("chosen:updated");
			$("#address").val(data.address);
			$("#active_id").val(data.active_id).trigger("chosen:updated");
			
			if(data.photo != null){
				$("#file-uploader").html('<img src="'+public_path+'/media/'+data.photo+'">');
				$("#photo").val(data.photo);
			}else{
				$("#file-uploader").html('<img src="'+public_path+'/assets/images/default.png">');
				$("#photo").val('');
			}
			
			onEditPanel();
        }
    });
}

function onViewData(id) {
	RecordId = id;
    $.ajax({
		type : 'POST',
		url: base_url + '/backend/getClientById',
		data: 'id='+RecordId,
		success: function (response) {			
			var data = response;
			
			$("#profile_name").text(data.name);
			$("#profile_desig").text(data.country_name);
			if(data.photo != null){
				var photo = public_path+"/media/"+data.photo;
				$("#profile_head").css("background-image", "url(" + photo + ")");
				$("#profile_image").html('<img src="'+public_path+'/media/'+data.photo+'">');
			}else{
				$("#profile_head").css("background-image", "url("+public_path+"/assets/images/default.png)");
				$("#profile_image").html('<img src="'+public_path+'/assets/images/default.png">');
			}
			
			var profile_info = '';
			var city_state_zip_code = '';
			if(data.email != null){
				profile_info += '<li><span class="con-icon"><i class="fa fa-envelope"></i></span><div class="con-desc"><a href="mailto:'+data.email+'">'+data.email+'</a></div></li>';
			}else{
				profile_info += '';
			}
			
			if(data.phone != null){
				profile_info += '<li><span class="con-icon"><i class="fa fa-phone"></i></span><div class="con-desc">'+data.phone+'</div></li>';
			}else{
				profile_info += '';
			}
			
			if(data.skype_id != null){
				profile_info += '<li><span class="con-icon"><i class="fa fa-skype"></i></span><div class="con-desc">'+data.skype_id+'</div></li>';
			}else{
				profile_info += '';
			}
			
			if(data.facebook_id != null){
				profile_info += '<li><span class="con-icon"><i class="fa fa-facebook"></i></span><div class="con-desc">'+data.facebook_id+'</div></li>';
			}else{
				profile_info += '';
			}

			if(data.city != null){
				city_state_zip_code += '<strong>City: </strong>'+data.city+'<br>';
			}else{
				city_state_zip_code += '';
			}
			
			if(data.state != null){
				city_state_zip_code += '<strong>State: </strong>'+data.state+'<br>';
			}else{
				city_state_zip_code += '';
			}
			
			if(data.zip_code != null){
				city_state_zip_code += '<strong>Zip Code: </strong>'+data.zip_code;
			}else{
				city_state_zip_code += '';
			}
			
			if(city_state_zip_code !=''){
				profile_info += '<li><span class="con-icon"><i class="fa fa-university"></i></span><div class="con-desc">'+city_state_zip_code+'</div></li>';
			}else{
				profile_info += '';
			}
			
			if(data.address != null){
				profile_info += '<li><span class="con-icon"><i class="fa fa-map-marker"></i></span><div class="con-desc">'+data.address+'</div></li>';
			}else{
				profile_info += '';
			}
			
			if(data.url != null){
				profile_info += '<li><span class="con-icon"><i class="fa fa-link"></i></span><div class="con-desc"><a target="_blank" href="'+data.url+'">'+data.url+'</a></div></li>';
			}else{
				profile_info += '';
			}
			
			$("#profile_info").html(profile_info);
			$('#View_Id').modal('show');
        }
    });
}

function onDelete(id) {
	RecordId = id;
	var msg = TEXT["Do you really want to delete this record"];
	onCustomModal(msg, "onConfirmWhenDelete");	
}

function onConfirmWhenDelete() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/deleteClient',
		data: 'id='+RecordId,
		success: function (response) {

            var msgType = response.msgType;
            var msg = response.msg;
			
            if (msgType == "success") {
				onLoadClientData();
				onSuccessMsg(msg);
				onListPanel();
            } else {
                onErrorMsg(msg);
            }
        }
    });
}

function showPerslyError() {
    $('.parsley-error-list').show();
}

jQuery('#DataEntry_formId').parsley({
    listeners: {
        onFieldValidate: function (elem) {
            if (!$(elem).is(':visible')) {
                return true;
            }else {
                showPerslyError();
                return false;
            }
        },
        onFormSubmit: function (isFormValid, event) {
            if (isFormValid) {
                onConfirmWhenAddEdit();
                return false;
            }
        }
    }
});

function upload_Form() {

	var data = new FormData();
		data.append('FileName', $('#FileName')[0].files[0]);
	var ReaderObj = new FileReader();
	var imgname  =  $('input[type=file]').val();
	var size  =  $('#FileName')[0].files[0].size;

	var ext =  imgname.substr((imgname.lastIndexOf('.') +1));
	if(ext=='jpg' || ext=='jpeg' || ext=='png' || ext=='gif' || ext=='PNG' || ext=='JPG' || ext=='JPEG'){
	 
		if(size<=1000000){
			$.ajax({
				url: base_url + '/backend/FileUpload',
				type: "POST",
				dataType : "json",
				data:  data,
				contentType: false,
				processData:false,
				enctype: 'multipart/form-data',
				mimeType:"multipart/form-data",
				success: function(response){

					var dataList = response;
					var msgType = dataList.msgType;
					var msg = dataList.msg;
					var FileName = dataList.FileName;

					if (msgType == 'success') {
						$("#file-uploader").html('<img src="'+public_path+'/media/'+FileName+'">');
						$("#photo").val(FileName);
						$(".errorMgs").hide();
						$(".errorMgs").html('');
						
					} else {
						$("#file-uploader").html('<img src="'+public_path+'/assets/images/default.png">');
						$("#photo").val('');
						$(".errorMgs").show();
						$(".errorMgs").html(msg);
					}
				},
				error: function(){
					return false;
				}				
			});
		}else{
			$("#file-uploader").html('<img src="'+public_path+'/assets/images/default.png">');
			$("#photo").val('');
			$(".errorMgs").show();
			$(".errorMgs").html(TEXT['Sorry file size exceeding from 1 Mb']);
		}
	}else{
		$("#file-uploader").html('<img src="'+public_path+'/assets/images/default.png">');
		$("#photo").val('');
		$(".errorMgs").show();
		$(".errorMgs").html(TEXT['Sorry only you can upload jpg, png and gif file type']);
	}
}

