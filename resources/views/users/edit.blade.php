@extends('layouts.app')

@section('content')
@if (request('action') == 'delete' && $user)
   @can('delete', $user)
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3 class="panel-title">{{ __('user.delete') }} : {{ $user->name }}</h3></div>
                    <div class="panel-body">
                            {!! FormField::delete(
                                ['route' => ['users.destroy', $user]],
                                __('user.delete_confirm_button'),
                                ['class' => 'btn btn-danger'],
                                ['user_id' => $user->id]
                            ) !!}
                            {{ link_to_route('users.edit', __('app.cancel'), [$user], ['class' => 'btn btn-default']) }}
                    </div>
                </div>
            </div>
        </div>
    @endcan
@else
    <h2 class="page-header">
        <div class="pull-right">
            {{ link_to_route('users.show', trans('app.show_profile').' '.$user->name, [$user->id], ['class' => 'btn btn-default']) }}
        </div>
        {{ trans('user.edit') }} {{ $user->profileLink() }}
    </h2>
    {{ Form::model($user, ['route' => ['users.update', $user->id], 'method' =>'patch', 'autocomplete' => 'off']) }}
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('user.edit') }}</h3></div>
                <div class="panel-body">
                    {!! FormField::text('name', ['label' => trans('user.name')]) !!}
                    {!! FormField::text('nickname', ['label' => trans('user.nickname')]) !!}
                    <div class="row">
                        <div class="col-md-6">{!! FormField::radios('gender_id', [1 => trans('app.male_code'), trans('app.female_code')], ['label' => trans('user.gender')]) !!}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">{!! FormField::text('dob', ['label' => trans('user.dob'), 'placeholder' => trans('app.example').' 1959-07-20']) !!}</div>
                        <div class="col-md-6">{!! FormField::text('dod', ['label' => trans('user.dod'), 'placeholder' => trans('app.example').' 2003-10-17']) !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('app.address') }} & {{ trans('app.contact') }}</h3></div>
                <div class="panel-body">
                    {!! FormField::textarea('address', ['label' => trans('app.address')]) !!}
                    {!! FormField::text('phone', ['label' => trans('app.phone'), 'placeholder' => trans('app.example').' 081234567890']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('app.login_account') }}</h3></div>
                <div class="panel-body">
                    {!! FormField::email('email', ['label' => trans('auth.email'), 'placeholder' => trans('app.example').' nama@mail.com']) !!}
                    {!! FormField::password('password', ['label' => trans('auth.password'), 'placeholder' => '******', 'value' => '']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="pull-right">
        {{ Form::submit(trans('app.update'), ['class' => 'btn btn-primary']) }}
        {{ link_to_route('users.show', trans('app.cancel'), [$user->id], ['class' => 'btn btn-default']) }}
    </div>
    {{ Form::close() }}
    @can('delete', $user)
        {{ link_to_route('users.edit', __('user.delete'), [$user, 'action' => 'delete'], ['class' => 'btn btn-danger pull-right', 'id' => 'del-user-'.$user->id]) }}
    @endcan
    @endif
@endsection
