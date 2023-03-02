@extends('layouts.backend')

@section('title', __('Dashboard'))

@section('content')
<!-- main Section -->
<div class="main-body">
	<div class="container-fluid">
		@php $vipc = vipc(); @endphp
		@if($vipc['bkey'] == 0) 
		@include('backend.partials.vipc')
		@else	
		<div class="row">
			<div class="col">
				<div class="dash-heading mb-10">
					<div class="row">
						<div class="col-md-12">
							<h2>{{ __('Dashboard') }}</h2>
						</div>
					</div>
				</div>
				<div id="tw-loader" class="tw-loader">
					<div class="tw-ellipsis">
						<div></div><div></div><div></div><div></div>
					</div>						
				</div>
			</div>
		</div>
		
		<div class="row mt-10">
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-industry"></i>
					</div>
					<div class="tw-content">
						<h3 id="TotalProjects"></h3>
						<p>{{ __('Total Projects') }}</p>
					</div>
				</div>
			</div>
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-paper-plane-o"></i>
					</div>
					<div class="tw-content">
						<h3 id="InprogressProjects"></h3>
						<p>{{ __('Inprogress Projects') }}</p>
					</div>
				</div>
			</div>
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-bullhorn"></i>
					</div>
					<div class="tw-content">
						<h3 id="TimeOut"></h3>
						<p>{{ __('Timeout Projects') }}</p>
					</div>
				</div>
			</div>
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-thumbs-o-up"></i>
					</div>
					<div class="tw-content">
						<h3 id="CompletedProjects"></h3>
						<p>{{ __('Completed Projects') }}</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-rocket"></i>
					</div>
					<div class="tw-content">
						<h3 id="TotalTasks"></h3>
						<p>{{ __('Total Tasks') }}</p>
					</div>
				</div>
			</div>
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-line-chart"></i>
					</div>
					<div class="tw-content">
						<h3 id="DoingTasks"></h3>
						<p>{{ __('Doing Tasks') }}</p>
					</div>
				</div>
			</div>
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-thumbs-o-down"></i>
					</div>
					<div class="tw-content">
						<h3 id="TimeoutTasks"></h3>
						<p>{{ __('Timeout Tasks') }}</p>
					</div>
				</div>
			</div>
			<div class="col-lg-3 mb-30">
				<div class="tw-card">
					<div class="tw-icon">
						<i class="fa fa-thumbs-o-up"></i>
					</div>
					<div class="tw-content">
						<h3 id="CompletedTasks"></h3>
						<p>{{ __('Completed Tasks') }}</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-6 mb-30">
				<div class="tw-card">
					<div class="tw-card-header">{{ __('Projects Status') }}</div>
					<canvas id="pie_chart_projects"></canvas>
				</div>
			</div>
			<div class="col-lg-6 mb-30">
				<div class="tw-card">
					<div class="tw-card-header">{{ __('Staffs Status') }}</div>
					<canvas id="pie_chart_staffs"></canvas>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 mb-30">
				<div class="tw-card">
					<div class="tw-card-header mb-10">{{ __('Projects List') }}</div>
					<div class="tw-card-body">
						<div class="table-responsive">
							<table id="DataTableIdProject" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th width="25%">{{ __('Project Name') }}</th>
										<th width="12%">{{ __('Created By') }}</th>
										<th width="18%">{{ __('Staff') }}</th>
										<th width="5%">{{ __('Client') }}</th>
										<th width="10%">{{ __('Start Date') }}</th>
										<th width="10%">{{ __('End Date') }}</th>
										<th width="10%">{{ __('Status') }}</th>
										<th width="10%">{{ __('Milestones') }}</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row dnone" id="ClientGridHideShow">
			<div class="col-lg-12">
				<div class="tw-card">
					<div class="tw-card-header mb-10">{{ __('Client List') }}</div>
					<div class="tw-card-body">
						<div class="table-responsive">
							<table id="DataTableIdClient" class="table table-striped table-bordered">
								<thead>
								  <tr>
									<th width="31%">{{ __('Client/Country') }}</th>
									<th width="31%">{{ __('E-mail/Phone') }}</th>
									<th width="31%">{{ __('Social Media') }}</th>
									<th width="7%">{{ __('Photos') }}</th>
								  </tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
<!-- /main Section -->
@endsection
@push('scripts')
<!-- Chart js -->
<script src="{{asset('public/assets/js/Chart.min.js')}}"></script>
<!-- datatables css/js -->
<link rel="stylesheet" href="{{asset('public/assets/datatables/dataTables.bootstrap4.min.css')}}">
<script src="{{asset('public/assets/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/assets/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script type="text/javascript">
var base_url = "{{ url('/') }}";
var public_path = "{{ asset('public') }}";
var userid = "{{ Auth::user()->id }}";
var roleid = "{{ Auth::user()->role_id }}";
var TEXT = [];
	TEXT['View'] = "{{ __('View') }}";
	TEXT['Milestones View'] = "{{ __('Milestones View') }}";
</script>
<script src="{{asset('public/pages/dashboard.js')}}"></script>
@endpush