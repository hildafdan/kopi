@extends('layouts.app')

@section('content')
<h2 class="page-header">
    {{ trans('app.relationship_calculator') }}
    @include('users.partials.action-buttons', ['user' => $user])
</h2>

{{ Form::open(['method' => 'get','class' => '']) }}
<div class="input-group">
        {{ Form::text('head', request('head'), ['class' => 'form-control', 'placeholder' => trans('app.relationship_calculator_node1')]) }}
        {{ Form::text('tail', request('tail'), ['class' => 'form-control', 'placeholder' => trans('app.relationship_calculator_node2')]) }}
        <span class="input-group-btn">
            {{ Form::submit(trans('app.calculate'), ['class' => 'btn btn-default']) }}
            {{ link_to_route('users.bfs', trans('app.relationship_calculator'), $user->id, 'Reset', [], ['class' => 'btn btn-default']) }}
        </span>
</div>

{{ Form::close() }}
@if (request('head') and request('tail'))
	@if (!$u1 or !$u2 or !$checkRoot)
		<h2>
		    <small class="pull-right">{!! trans('app.user_relation') !!}</small>
		</h2>
	@else
	<h2>
		    <small class="pull-right">{!! trans('app.time_process', ['path' => count($trackResult)-1, 'waktu' => $totaltime_format]) !!}</small>
	</h2>
	@endif
	<br>
	<div id="wrapper">
		@for($i=0; $i<count($trackResult); $i++)
			<div class="entry {{$trackResult[$i]->name}}">
				<span class="label" >
					@if($i==count($trackResult)-1)
						<font color="black" ><i>{{$trackResult[$i]->name}}</i></font>
					@else
						<font color="grey">{{$trackResult[$i]->name}}</font>
					@endif
				</span>
				@if($i!=count($trackResult)-1)
					<div class="branch" >
						<font color="grey" size="2px">{{$trackResult[$i]->status}}</font>
					</div>
				@endif
			</div>
		@endfor
	</div>
@endif

<link rel="stylesheet" href="{{ asset('css/tree.css') }}">
@endsection
