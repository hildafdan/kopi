<div class="pull-right btn-group" role="group">
    @can ('edit', $user)
    {{ link_to_route('users.edit', trans('app.edit'), [$user->id], ['class' => 'btn btn-warning']) }}
    @endcan
    {{ link_to_route('users.show', trans('app.show_profile'), [$user->id], ['class' => Request::segment(3) == null ? 'btn btn-default active' : 'btn btn-default']) }}
    {{ link_to_route('users.tree', trans('app.show_family_tree'), [$user->id], ['class' => Request::segment(3) == 'tree' ? 'btn btn-default active' : 'btn btn-default']) }}
    {{ link_to_route('users.chart', trans('app.show_family_chart'), [$user->id], ['class' => Request::segment(3) == 'chart' ? 'btn btn-default active' : 'btn btn-default']) }}
    {{ link_to_route('users.bfs', trans('app.relationship_calculator'), [$user->id], ['class' => Request::segment(3) == 'bfs' ? 'btn btn-default active' : 'btn btn-default']) }}
    {{ link_to_route('users.search', trans('app.search_your_family'), [$user->id], ['class' => Request::segment(3) == 'search' ? 'btn btn-default active' : 'btn btn-default']) }}
</div>