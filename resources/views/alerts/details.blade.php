@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Alert details') }}</h1>
        <hr />

        <a href="{{ route('alerts.readed', ['id' => $id]) }}" class="btn btn-warning">{{ __('Mark as read') }}</a>

        <hr />

<pre>
{{ print_r($alert) }}
</pre>
    </div>
@endsection