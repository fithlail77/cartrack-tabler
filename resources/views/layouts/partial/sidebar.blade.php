	<nav class="pcoded-navbar menupos-fixed menu-light brand-blue ">
		<div class="navbar-wrapper ">
			<div class="navbar-brand header-logo">
				<a href="{{ route('dashboard') }}" class="b-brand gum-brand">
					<div class="gum-logo-wrapper">
						<img src="{{ asset('assets/images/gum.png') }}" alt="GUM Logo" class="gum-logo-img">
						<div class="gum-brand-text">
							<span class="gum-title">Cartrack API</span>
							<span class="gum-subtitle">— GUM —</span>
						</div>
					</div>
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
					@role('admin')
				<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link">
							<span class="pcoded-micon"><i class="feather icon-settings"></i></span>
							<span class="pcoded-mtext">Settings</span>
						</a>
						<ul class="pcoded-submenu">
							<li><a href="{{ route('admin.users.index') }}" class="">User Management</a></li>
							<li><a href="{{ route('admin.login-logs.index') }}" class="">Login Log</a></li>
						</ul>
					</li>
				@endrole
					<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link"><span class="pcoded-micon"><i
									class="feather icon-box"></i></span><span class="pcoded-mtext">Rest API Cartrack</span></a>
						<ul class="pcoded-submenu">
							<li class=""><a href="{{ route('vehicles.index') }}" class="">Vehicle List</a></li>
							<li class=""><a href="{{ route('cartrack.activity.index') }}" class="">Vehicle Activities</a></li>
							<li class=""><a href="{{ route('fuels.index') }}" class="">Vehicle Fuel Consumed</a></li>
							<li class=""><a href="{{ route('fuel-fills.index') }}" class="">Vehicle Fill Fuel</a></li>
							<li class=""><a href="{{ route('trips.sync.index') }}" class="">Vehicle Trips</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link"><span class="pcoded-micon"><i
									class="feather icon-box"></i></span><span class="pcoded-mtext">Reports</span></a>
						<ul class="pcoded-submenu">
							<li class=""><a href="{{ route('reports.activity.index') }}" class="">Vehicle Activities</a></li>
							<li class=""><a href="{{ route('reports.fuel.index') }}" class="">Vehicle Fuel Consumed</a></li>
							<li class=""><a href="{{ route('reports.fuel-fill.index') }}" class="">Vehicle Fuel Fills</a></li>
							<li class=""><a href="{{ route('reports.trips.index') }}" class="">Vehicle Trips</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>