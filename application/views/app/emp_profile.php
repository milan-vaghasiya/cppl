<?php $this->load->view('app/includes/header'); ?>
	
	<!-- Header -->
	<header class="header">
		<div class="main-bar">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="menu-toggler me-2">
    						<!-- <i class="fa-solid fa-bars font-16"></i> -->
    						<svg class="text-dark" xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 0 24 24" width="30px" fill="#000000"><path d="M13 14v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1zm-9 7h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1zM3 4v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1zm12.95-1.6L11.7 6.64c-.39.39-.39 1.02 0 1.41l4.25 4.25c.39.39 1.02.39 1.41 0l4.25-4.25c.39-.39.39-1.02 0-1.41L17.37 2.4c-.39-.39-1.03-.39-1.42 0z"></path></svg>
    					</a>
						<h5 class="title mb-0 text-nowrap">Profile</h5>
					</div>
					<div class="mid-content">
					</div>
					<div class="right-content">
					    <a type="button" class="text-danger"  data-form_title="Add Change Password" datatip="Add change Password" data-bs-toggle="offcanvas" data-bs-target="#change-psw" aria-controls="offcanvasBottom"><i class="fa fa-key fs-22"></i></a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    <?php
    $profile_pic = 'male_user.png';
    if(!empty($empData->emp_profile)):
        $profile_pic = $empData->emp_profile;
    else:
        if(!empty($empData->emp_gender) and $empData->emp_gender=="Female"):
            $profile_pic = 'female_user.png';
        endif;
    endif;
    ?>
    <!-- Page Content -->
    <div class="page-content bottom-content">
        <div class="container">
			<div class="driver-profile">
				<div class="media media-100 mb-2">
					<img class="rounded-circle" src="<?= base_url('assets/uploads/emp_profile/'.$profile_pic) ?>" alt="driver-image">
				</div>
				<div class="profile-detail">
					<h6 class="name mb-0 font-18"><?=$empData->emp_name?></h6>
					<span class="text-center d-block"><?=$empData->designation_name?></span>
				</div>
			</div>
			
			<div class="dz-list">
				<ul>
					<li>
						<a href="javascript:void(0);" class="item-content">
							<div class="dz-icon">
								<i class="fa fa-user"></i>
							</div>
							<div class="dz-inner">
								<span class="title"><?=$empData->emp_name?></span>
							</div>
						</a>
					</li>
					<li>
						<a href="javascript:void(0);" class="item-content">
							<div class="dz-icon">
								<i class="fa fa-at"></i>
							</div>
							<div class="dz-inner">
								<span class="title"><?=$empData->emp_email?></span>
							</div>
						</a>
					</li>
					<li>
						<a href="javascript:void(0);" class="item-content">
							<div class="dz-icon">
								<i class="fa-solid fa-phone"></i>
							</div>
							<div class="dz-inner">
								<span class="title"><?=$empData->emp_contact?></span>
							</div>
						</a>
					</li>
					
				</ul>
			</div>
		</div>
    </div>    
    <!-- Page Content End-->
<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>
<?php $this->load->view('app/change_password'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<script>
    function store(){
        var formId = 'empProfile';
       
        var form = $('#'+formId)[0];
        var fd = new FormData(form);

        $.ajax({
            url: base_url + controller+'/save',
            data:fd,
            type: "POST",
            processData:false,
            contentType:false,
            dataType:"json",
        }).done(function(data){
            if(data.status==1){
                $('#'+formId)[0].reset(); 
                Swal.fire({
                    title: "Success",
                    text: data.message,
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok!"
                    }).then((result) => {
                        window.location.reload();
                    });
            }else{
                if(typeof data.message === "object"){
                    $(".error").html("");
                    $.each( data.message, function( key, value ) {$("."+key).html(value);});
                }else{
                    Swal.fire( 'Sorry...!', data.message, 'error' );

                    
                }			
            }				
        });
    }
    
    $(document).ready(function(){
    	setPlaceHolder();
    	$(document).on('click','.changePsw',function(){ 
    		var formId = "changePSW";
    		var form = $('#'+formId)[0];
    		var fd = new FormData(form);
    
    		$.ajax({
    			url: base_url + 'hr/employees/changePassword',
    			data:fd,
    			type: "POST",
    			global:false,
    			processData:false,
    			contentType:false,
    			dataType:"json",
    		}).done(function(response){
    			if(response.status==1)
    			{
    				$("#changePSW")[0].reset();
    				$('#change-psw').offcanvas('hide');
    				Swal.fire({ icon: 'success', title: response.message});
    			}
    			else{$(".error").html("");$.each( response.message, function( key, value ) {$("."+key).html(value);});}
    			window.scrollTo(0, document.body.scrollHeight);
    		});
    	});
    	
    	$(document).on('click','.pswHideShow',function(){
    		var type = $('.pswType').attr('type');
    		if(type == "password"){
    			$(".pswType").attr('type','text');
    			$(this).html('<i class="fa fa-eye-slash"></i>');
    		}else{
    			$(".pswType").attr('type','password');
    			$(this).html('<i class="fa fa-eye"></i>');
    		}
    	});

    });
</script>