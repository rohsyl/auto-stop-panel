@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Report details') }}</h1>
        <hr />

        <a href="{{ route('reports.readed', ['id' => $id]) }}" class="btn btn-warning">{{ __('Mark as read') }}</a>

        <hr />

<pre>
{{ print_r($report) }}
</pre>
    </div>
@endsection