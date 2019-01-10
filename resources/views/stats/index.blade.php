@extends('layouts.app')

@section('content')

    <style type="text/css">

        .potostop-stats-wrapper
        {
            margin: 60px 30px 120px;
            text-align: center;
        }

        .satisfaction-rate
        {
            font-size: 1.4em;
        }

        .satisfaction-rate span
        {
            display: inline-block;
            margin: 0 40px;
            line-height: 1.2;
        }

        .satisfaction-rate strong
        {
            font-size: 1.6em;
            font-weight: bold;
        }

        .satisfaction-rate .good
        {
            color: #0a5;
        }

        .satisfaction-rate .bad
        {
            color: #d22;
        }

        table.potostop-stats
        {
            margin: 2em auto;
            border-collapse: collapse;
        }

        table.potostop-stats tr:nth-child(odd)
        {
            background: #f5f5f5;
        }

        table.potostop-stats tr th:first-child,
        table.potostop-stats tr td:first-child
        {
            padding-left: 60px;
        }
        table.potostop-stats tr th:last-child,
        table.potostop-stats tr td:last-child
        {
            padding-right: 60px;
        }

        table.potostop-stats td,
        table.potostop-stats th
        {
            padding: 14px;
        }

        table.potostop-stats th
        {
            font-size: 1.4rem;
            font-weight: normal;
            text-align: left;
        }

        table.potostop-stats td
        {
            font-weight: bold;
            font-size: 2rem;
            text-align: right;
            color: #555;
        }

        table.potostop-stats td small
        {
            display: block;
            font-size: 1.1rem;
            color: #aaa;
        }

    </style>

    <div class="potostop-stats-wrapper">

        <h1>Potostop usage statistics</h1>

        <canvas id="satisfaction_piechart" width="300" height="300"></canvas>

        <div class="satisfaction-rate">
            <span class="bad">
                <strong>
                    @php echo 100*(1-$satisfaction_rate)  @endphp %
                </strong>
                <br>
                bad trips
            </span>
            <span class="good">
                <strong>
                    @php echo 100*$satisfaction_rate  @endphp %
                </strong>
                <br>
                good trips
            </span>
        </div>

        <script type="text/javascript">
            var c = document.getElementById('satisfaction_piechart');
            var satisfaction_rate = {{$satisfaction_rate}}; // Value set dynamically by PHP

            if (c)
            {
                var ctx = c.getContext('2d');

                var x = c.width/2;
                var y = c.height/2;
                var radius = 0.9 * (c.width/2);


                ctx.beginPath();

                ctx.arc(x, y, radius, -Math.PI/2, -Math.PI/2 + satisfaction_rate * (2*Math.PI), false);
                ctx.lineTo(x, y);
                ctx.closePath();
                ctx.fillStyle = '#0a5';
                ctx.fill();

                ctx.beginPath();

                ctx.arc(x, y, radius, -Math.PI/2, -Math.PI/2 + satisfaction_rate * (2*Math.PI), true);
                ctx.lineTo(x, y);
                ctx.closePath();
                ctx.fillStyle = '#d22';
                ctx.fill();
            }

        </script>



        <table class="potostop-stats">
            <tr>

                <td>
                    {{$total_trips}}
                </td>
                <th>voyages</th>
            </tr>
            <tr>
                <td>{{$total_persons}}</td>
                <th>passagers</th>
            </tr>
            <tr>
                <td>{{$total_plates}}</td>
                <th>conducteurs</th>
            </tr>
            <tr>
                <td>{{$avg_trips_per_person}}</td>
                <th>voyages par passager (en moyenne)</th>
            </tr>
            <tr>
                <td>{{$avg_trips_per_plate}}</td>
                <th>voyages par conducteur (en moyenne)</th>
            </tr>
            <tr>
                <td>{{$total_person_km}} km</td>
                <th>parcourus par chaque passager</th>
            </tr>
        </table>
    </div>
@endsection