	<nav class="pcoded-navbar menupos-fixed menu-light brand-blue ">
		<div class="navbar-wrapper ">
			<div class="navbar-brand header-logo">
				<a href="index.html" class="b-brand">
					<img src="{{ asset('assets/images/logo.svg') }}" alt="" class="logo images">
					<img src="{{ asset('assets/images/logo-icon.svg') }}" alt="" class="logo-thumb images">
				</a>
				<a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
			</div>
			<div class="navbar-content scroll-div">
				<ul class="nav pcoded-inner-navbar">
					<li class="nav-item pcoded-menu-caption">
						<label>Navigation</label>
					</li>
					<li class="nav-item">
						<a href="{{ route('dashboard') }}" class="nav-link"><span class="pcoded-micon"><i
									class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
					</li>
					<li class="nav-item pcoded-menu-caption">
						<label>Menu</label>
					</li>
					<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link"><span class="pcoded-micon"><i
									class="feather icon-box"></i></span><span class="pcoded-mtext">Rest API Cartrack</span></a>
						<ul class="pcoded-submenu">
							<li class=""><a href="{{ route('vehicles.index') }}" class="">Vehicle List</a></li>
							<li class=""><a href="{{ route('cartrack.activity.index') }}" class="">Vehicle Activities</a></li>
							<li class=""><a href="{{ route('fuels.index') }}" class="">Vehicle Fuel Consumed</a></li>
							<li class=""><a href="{{ route('fuel-fills.index') }}" class="">Vehicle Fill Fuel</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link"><span class="pcoded-micon"><i
									class="feather icon-box"></i></span><span class="pcoded-mtext">Reports</span></a>
						<ul class="pcoded-submenu">
							<li class=""><a href="bc_button.html" class="">Sinkronisasi Data</a></li>
							<li class=""><a href="bc_badges.html" class="">Laporan</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>