<!-- Sidebar -->
<div class="dark-overlay"></div>
	<div class="sidebar style-2">
		<a href="javascript:void(0)" class="side-menu-logo bg-light">
			<img src="<?=base_url("assets/app/images/logo_text.png")?>" alt="logo"  style="width:75%;">
		</a>
		<ul class="nav navbar-nav" data-simplebar>	
			<li class="nav-label"><?=$this->userName?></li>
			<li>
				<a class="nav-link" href="<?=base_url("dealer_app/dashboard")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/dashboard')?'active':''?>">
					<span class="dz-icon">
						<i class="fa-solid fa-house"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Home</span>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?=base_url("dealer_app/executiveTarget")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/executiveTarget')?'active':''?>">
					<span class="dz-icon">
						<i class="fa-solid fa-bullseye"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Target</span>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?=base_url("dealer_app/order")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/order')?'active':''?>">
					<span class="dz-icon">
						<i class="fa-solid fa-cart-shopping"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Order</span>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?=base_url('dealer_app/login/logout')?>">
					<span class="dz-icon">
						<i class="fas fa-sign-out-alt"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Logout</span>
				</a>
			</li>
            
		</ul>
		
    </div>
    <!-- Sidebar End -->