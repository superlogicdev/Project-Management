
<header class="be-header">
	<a id="sidebarCollapse" class="btn-sh" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
	<ul class="top-navbar">
		<li>
			<a title="{{ __('Chat') }}" href="{{ route('backend.chat') }}">
				<span class="top-search-icon"><i class="fa fa-comments-o"></i></span>
			</a>
			@php echo msgCount(Auth::user()->id); @endphp
		</li>
		<li>
			<span class="user_info">{{ Auth::user()->name }}<br>{{ Auth::user()->email }}</span>
			<div class="profile-img"><img src="{{ Auth::user()->photo ? asset('public/media/'.Auth::user()->photo) : asset('public/assets/images/default.png') }}"></div>
			<ul class="sub-navbar">
				<li><a href="{{ route('backend.profile') }}">{{ __('Edit Profile') }}</a></li>
				<li>
					<a href="{{ route('logout') }}"
					   onclick="event.preventDefault();
									 document.getElementById('logout-form').submit();">
						{{ __('Logout') }}
					</a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
						@csrf
					</form>
				</li>
			</ul>
		</li>
	</ul>
</header>
