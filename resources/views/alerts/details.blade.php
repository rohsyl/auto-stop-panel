@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Alert details') }}</h1>
        <hr />

        <a href="{{ route('alerts.readed', ['id' => $id]) }}" class="btn btn-warning">{{ __('Mark as read') }}</a>
        <a href="{{ route('trip.map', ['id' => $alert['tripUid']]) }}" class="btn btn-success">{{ __('See trip path') }}</a>
        <a href="{{ route('alerts.flaged', ['id' => $alert['tripUid']]) }}" class="btn btn-danger">{{ __('Flag de plate') }}</a>

        <hr />

        <div class="row">
            <div class="col">    <h3>Alert and Trip details</h3>
                <dl class="dl-horizontal">

                    <dt>Alert position</dt>
                    <dd><a href="http://maps.google.com/maps?q={{ $alert['lastPosition']['latitude'] }},{{ $alert['lastPosition']['longitude'] }}" target="_blank">Show in google maps</a></dd>

                    <dt>Date</dt>
                    <dd>{{ \Carbon\Carbon::createFromTimestampMs($alert['timestamp'])->format('d M Y H:i:s') }}</dd>

                    <dt>Trip uid</dt>
                    <dd>{{ $alert['tripUid'] }}</dd>

                    <dt>Trip destination</dt>
                    <dd><a href="http://maps.google.com/maps?q={{ $alert['trip']['destination']['latitude'] }},{{ $alert['trip']['destination']['longitude'] }}" target="_blank">{{ $alert['trip']['destination']['name'] }}</a></dd>

                    <dt>Trip status</dt>
                    <dd>{{ $alert['trip']['status'] }}</dd>

                </dl>
            </div>
            <div class="col"> <h3>Owner details</h3>
                <dl>
                    <dt>Owner uid</dt>
                    <dd>{{ $alert['trip']['ownerUid'] }}</dd>

                    <dt>Fullname</dt>
                    <dd>{{ $alert['trip']['owner']['sex'] == 0 ? 'Mr.' : 'Ms.' }} {{ $alert['trip']['owner']['fullname'] }}</dd>

                    <dt>E-mail</dt>
                    <dd>{{ $alert['trip']['owner']['email'] }}</dd>

                    <dt>Emergency E-mail</dt>
                    <dd>{{ $alert['trip']['owner']['emergencyEmail'] }}</dd>

                    <dt>Emergency Phone</dt>
                    <dd>{{ $alert['trip']['owner']['emergencyPhone'] }}</dd>

                </dl>
            </div>
        </div>


    </div>
@endsection