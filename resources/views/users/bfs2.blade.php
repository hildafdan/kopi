@extends('layouts.app')

@section('content')
<h2 class="page-header">
    {{ trans('app.relationship_calculator') }}
    <small class="pull-right">{!! trans('app.user_relation') !!}</small>
</h2>

{{ Form::open(['method' => 'get','class' => '']) }}
<div class="input-group">
        {{ Form::text('head', request('head'), ['class' => 'form-control', 'placeholder' => trans('app.relationship_calculator_node1')]) }}
        {{ Form::text('tail', request('tail'), ['class' => 'form-control', 'placeholder' => trans('app.relationship_calculator_node2')]) }}
        <span class="input-group-btn">
            {{ Form::submit(trans('app.search'), ['class' => 'btn btn-default']) }}
        </span>
</div>

{{ Form::close() }}

<!-- @if (request('q'))
<br>
{{ $users->appends(Request::except('page'))->render() }}
@foreach ($users->chunk(4) as $chunkedUser)
<div class="row">
    @foreach ($chunkedUser as $user)
    $user->nickname
    @endforeach
</div>
@endforeach

{{ $users->appends(Request::except('page'))->render() }}
@endif -->
@endsection

