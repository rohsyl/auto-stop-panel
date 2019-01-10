@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="page-header">{{ __('Flaged plate') }}</h1>
        <hr />
        <table class="table">
            <tr>
                <th>{{ __('Plate Nr.') }}</th>
                <th>{{ __('Unflag plate') }}</th>
            </tr>

            @foreach($flagedPlates as $id => $plate)
                <tr>

                    <td>
                        {{$id}}
                    </td>
                    <td>
                        <a href="{{ route('flag.unflaged', ['id' =>  $id]) }}" class="btn btn-sm btn-success">{{ __('Unflag') }}</a>
                    </td>
                </tr>
            @endforeach
        </table>
        <br />
        <br />
    </div>
@endsection