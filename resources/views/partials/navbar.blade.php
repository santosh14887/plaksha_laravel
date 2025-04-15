<nav class="navbar">
				<a href="#" class="sidebar-toggler">
					<i data-feather="menu"></i>
				</a>
				<div class="navbar-content">
					<!-- <form class="search-form">
						<div class="input-group">
              <div class="input-group-text">
                <i data-feather="search"></i>
              </div>
							<input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
						</div>
					</form> -->
					<ul class="navbar-nav">
						
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img class="wd-30 ht-30 rounded-circle" src="{{ asset('images/faces/face8.jpg') }}" alt="profile">
							</a>
							<div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
								<div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
									<div class="mb-3">
										<img class="wd-80 ht-80 rounded-circle" src="{{ asset('images/faces/face8.jpg') }}" alt="">
									</div>
									<div class="text-center">
										<p class="tx-16 fw-bolder">{{ Auth::user()->name }}</p>
										<!-- <p class="tx-12 text-muted">amiahburton@gmail.com</p> -->
									</div>
								</div>
                <ul class="list-unstyled p-1">
                  <a id="change_password_a" href="javascript:void(0)" data-toggle="modal" data-target="#change_pwd_model" class="text-body ms-0">
				  <li class="dropdown-item py-2">
                      <i class="me-2 icon-md" data-feather="edit"></i>
                      <span>Edit Profile</span>
                  </li></a>
					<a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="text-body ms-0"><li class="dropdown-item py-2">
                      <i class="me-2 icon-md" data-feather="log-out"></i>
                      <span>Log Out</span>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                  </li></a>
				  
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                  <span class="mdi mdi-menu"></span>
              </button>
							</div>
						</li>
					</ul>
				</div>
			</nav>
      <!-- Modal -->
	<div id="change_pwd_model" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			
			<h4 class="modal-title">Change Password</h4>
			<button type="button" class="close change_pwd_close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
				<div class="form-group row">
					<div class="col-md-12 ">
						<div class="form-group">
						{!! Form::label('Name', 'New Password', ['class' => 'form-label']) !!}
						<input id="password" type="password" class="form-control" name="password">
						</div>
					</div>
					<div class="col-md-12 ">
						<div class="form-group">
						{!! Form::label('Name', 'Confirm Password', ['class' => 'form-label']) !!}
						<input id="confirm_password" type="password" class="form-control" name="confirm_password">
						</div>
					</div>
				</div>
				<p class="col-md-12 change_password_error alert" id="change_password_error"></p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary change_pwd_save" id="change_pwd_save">Save changes</button>
		  </div>
		</div>

	  </div>
	</div>