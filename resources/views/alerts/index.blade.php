@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Recent Alerts') }}</h1>
        <hr />
        <table class="table">
            <tr>
                <th>{{ __('Position') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr>

            @foreach($newAlerts as $id => $alert)
                <tr>
                    <td><a href="http://maps.google.com/maps?q={{ $alert['lastPosition']['latitude'] }},{{ $alert['lastPosition']['longitude'] }}" target="_blank">{{ $alert['lastPosition']['latitude'] }}, {{ $alert['lastPosition']['longitude'] }}</a></td>

                    <td>{{ \Carbon\Carbon::createFromTimestamp($alert['timestamp'])->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <a href="{{ route('alerts.details', ['id' => $id]) }}" class="btn btn-sm btn-dark">{{ __('See details') }}</a>
                    </td>
                </tr>
            @endforeach
        </table>

        <br />
        <br />

        <h1 class="page-header">{{ __('Old Alerts') }}</h1>
        <hr />
        <table class="table">
            <tr>
                <th>{{ __('Position') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr>

            @foreach($readedAlerts as $id => $alert)
                <tr>
                    <td><a href="http://maps.google.com/maps?q={{ $alert['lastPosition']['latitude'] }},{{ $alert['lastPosition']['longitude'] }}" target="_blank">{{ $alert['lastPosition']['latitude'] }}, {{ $alert['lastPosition']['longitude'] }}</a></td>

                    <td>{{ \Carbon\Carbon::createFromTimestamp($alert['timestamp'])->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <a href="{{ route('alerts.details', ['id' => $id]) }}" class="btn btn-sm btn-dark">{{ __('See details') }}</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection