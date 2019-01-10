@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Report details') }}</h1>
        <hr />

        <a href="{{ route('reports.readed', ['id' => $id]) }}" class="btn btn-warning">{{ __('Mark as read') }}</a>

        <hr />

        <div class="row">
            <div class="col">    <h3>Trip details</h3>
                <dl class="dl-horizontal">
                    <dt>Trip uid</dt>
                    <dd>{{ $report['tripUid'] }}</dd>

                    <dt>Plate Number</dt>
                    <dd>{{ $report['plateNumber'] }}</dd>

                    <dt>Message</dt>
                    <dd>{{ $report['message']}}</dd>


                    <dt>Trip status</dt>
                    <dd>{{ $report['trip']['status'] }}</dd>

                </dl>
            </div>
            <div class="col"> <h3>Owner details</h3>
                <dl>
                    <dt>Owner uid</dt>
                    <dd>{{ $report['trip']['ownerUid'] }}</dd>

                    <dt>Fullname</dt>
                    <dd>{{ $report['trip']['owner']['sex'] == 0 ? 'Mr.' : 'Ms.' }} {{ $report['trip']['owner']['fullname'] }}</dd>

                    <dt>E-mail</dt>
                    <dd>{{ $report['trip']['owner']['email'] }}</dd>

                    <dt>Emergency E-mail</dt>
                    <dd>{{ $report['trip']['owner']['emergencyEmail'] }}</dd>

                    <dt>Emergency Phone</dt>
                    <dd>{{ $report['trip']['owner']['emergencyPhone'] }}</dd>

                </dl>
            </div>
        </div>


    </div>
@endsection