@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Recent Reports') }}</h1>
        <hr />
        <table class="table">
            <tr>
                <th>{{ __('Plate Nr.') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr>

            @foreach($newReports as $id => $report)
                <tr>
                    <td>{{ $report['plateNumber'] }}</td>

                    <td>{{ \Carbon\Carbon::createFromTimestampMs($report['timestamp'])->format('d M Y H:i:s') }}</td>
                    <td>
                        <a href="{{ route('reports.details', ['id' => $id]) }}" class="btn btn-sm btn-dark">{{ __('See details') }}</a>
                        <a href="{{ route('reports.flaged', ['id' =>  $report['plateNumber']]) }}" class="btn btn-sm btn-danger">{{ __('Flag the plate') }}</a>
                    </td>
                </tr>
            @endforeach
        </table>

        <br />
        <br />

        <h1 class="page-header">{{ __('Old Report') }}</h1>
        <hr />
        <table class="table">
            <tr>
                <th>{{ __('Plate Nr.') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr>

            @foreach($readedReports as $id => $report)
                <tr>
                    <td>{{ $report['plateNumber'] }}</td>
                    <td>{{ \Carbon\Carbon::createFromTimestampMs($report['timestamp'])->format('d M Y H:i:s') }}</td>
                    <td>
                        <a href="{{ route('reports.details', ['id' => $id]) }}" class="btn btn-sm btn-dark">{{ __('See details') }}</a>
                        <a href="{{ route('reports.flaged', ['id' =>  $report['plateNumber']]) }}" class="btn btn-sm btn-danger">{{ __('Flag the plate') }}</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection