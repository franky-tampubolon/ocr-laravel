<div class="title">
    <h5>{{$jenis_rekap}}</h5>
    <p>{{date('d/m/Y')}}</p>
</div>
<table>
    <thead>
        <tr>
            <th rowspan="2">NO BTD</th>
            <th rowspan="2">AMOUNT</th>
            <th rowspan="2">DUE DATE</th>
            <th rowspan="2">ADOBE PRINT</th>
            <th colspan="2">SIGNATURE GROUP A</th>
            <th colspan="3">SIGNATURE GROUP B</th>
            <th rowspan="2">Diterima I2P Jam</th>
            <th rowspan="2"> Paraf PIC I2P</th>
        </tr>
        <tr>
            <th>IN</th>
            <th>OUT</th>
            <th>IN QA jam MSIG</th>
            <th>OUT QA SML jam</th>
            <th>Received QA MSIG jam</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            @for($x=0; $x<11; $x++)
            <td height="10px"></td>
            @endfor
        </tr>
        @foreach ($data as $key => $row)
            @if($row['btd_number'] !=='')
                <tr>
                    <td height="25px">{{$row['btd_number']}}</td>
                    <td height="25px">{{number_format($row['amount'], 0, ',', '.')}}</td>
                    <td height="25px">{{$row['due_date']}}</td>
                    @if($key === 0)
                        @for($m=0; $m<8; $m++)
                        <td rowspan="{{$jumlah_data}}"></td>
                        @endfor
                    @endif
                </tr>
            @endif
        @endforeach
    </tbody>

    @for($y=0; $y<2; $y++)
        <tr>
            @for($x=0; $x<11; $x++)
            <td height="25px"></td>
            @endfor
        </tr>
    @endfor

</table>
