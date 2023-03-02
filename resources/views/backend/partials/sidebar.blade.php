
<div class="left-sidebar">
	<div class="logo">
		<a href="{{ url('/') }}"><img src="{{ $gtext['logo'] ? asset('public/media/'.$gtext['logo']) : asset('public/assets/images/logo.png') }}"></a>
	</div>
	<ul class="left-main-menu">
		@if(Auth::user()->role_id ==1)
		<li><a href="{{ route('backend.dashboard') }}"><i class="fa fa-tachometer"></i><span>{{ __('Dashboard') }}</span></a></li>
		<li id="is_project"><a href="{{ route('backend.project') }}"><i class="fa fa-list"></i><span>{{ __('Projects') }}</span></a></li>
		<li><a href="{{ route('backend.client') }}"><i class="fa fa-users"></i><span>{{ __('Client') }}</span></a></li>
		<li><a href="{{ route('backend.staff') }}"><i class="fa fa-users"></i><span>{{ __('Staff') }}</span></a></li>
		<li id="zoom-meeting"><a href="{{ route('backend.upcoming-meeting') }}"><i class="fa fa-video-camera"></i><span>{{ __('Zoom Meeting') }}</span></a></li>
		<li id="languages-nav"><a href="{{ route('backend.languages') }}"><i class="fa fa-language"></i><span>{{ __('Languages') }}</span></a></li>
		<li><a href="{{ route('backend.chat') }}"><i class="fa fa-comments-o"></i><span>{{ __('Chat') }}</span></a></li>
		<li><a href="{{ route('backend.settings') }}"><i class="fa fa-cogs"></i><span>{{ __('Settings') }}</span></a></li>
		@else
		<li><a href="{{ route('backend.dashboard') }}"><i class="fa fa-tachometer"></i><span>{{ __('Dashboard') }}</span></a></li>
		<li><a href="{{ route('backend.project') }}"><i class="fa fa-list"></i><span>{{ __('Projects') }}</span></a></li>
		<li><a href="{{ route('backend.chat') }}"><i class="fa fa-comments-o"></i><span>{{ __('Chat') }}</span></a></li>
		@endif
	</ul>
</div>
