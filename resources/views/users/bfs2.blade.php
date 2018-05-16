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
            {{ Form::submit(trans('app.search'), ['class' => 'btn btn-default']) }}
            {{ link_to_route('users.bfs', trans('app.relationship_calculator'), $user->id, 'Reset', [], ['class' => 'btn btn-default']) }}
        </span>
</div>
<h2>
    <small class="pull-right">{!! trans('app.user_relation') !!}</small>
</h2>
{{ Form::close() }}
@endsection

