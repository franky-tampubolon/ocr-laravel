<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
    <style>

    @media print{
        table{

            width: 100%;
            border-collapse: collapse;
        }
        table thead tr th{
            table-layout: fixed;
            min-width:110px;
            max-width: 110px;
        }

        table tbody tr td{
            font-size:14pt;
        }
    }
    table{
        border-collapse: collapse;
        margin: 0;
        padding: 0;

    }
    table thead tr th{
        border-width: 2px !important;
        border-style: solid !important;
        border-color: black !important;
        border-right: solid 2px black;
        border-left: solid 2px black;
        vertical-align : middle;
        text-align:center;
        width: 110px;
    }
    table tbody tr td{
        border-width: 2px !important;
        border-style: solid !important;
        border-color: black !important;
        border-right: solid 2px black;
        border-left: solid 2px black;
        vertical-align : middle;
        text-align:center;
        font-size:14pt;
        height: 40px;
        padding:10px;
        width: 110px;
    }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container">
         <a class="navbar-brand" href="#">Deal Slip</a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse" id="navbarSupportedContent">
           <ul class="navbar-nav ml-auto">
             <li class="nav-item active">
               <a class="nav-link" href="{{route('dealslip.index')}}">Deal Slip</a>
             </li>
             <li class="nav-item active">
               <a class="nav-link" href="{{route('excel.index')}}">Rekap</a>
             </li>
           </ul>
         </div>
        </div>
     </nav>
    <div class="container-fluid mt-5">
        <div class="row" >
            <div class="col" style="margin: 20px">
                    <div class="title">
                        <h5>UM/PELUNASAN/PPN CPO</h5>
                        <p>{{date('d/m/Y')}}</p>
                    </div>
                    <table id="table" class="" width="100%" style="font-size: 14pt; font-family:'Times New Roman', Times, serif">
                        <thead>
                            <tr style="vertical-align: middle; text-align:center;">
                                <th rowspan="2">NO BTD</th>
                                <th rowspan="2">AMOUNT</th>
                                <th rowspan="2">DUE DATE</th>
                                <th rowspan="2" width="110px">ADOBE PRINT</th>
                                <th colspan="2" style="font-size: 12pt">SIGNATURE <div>GROUP A</div></th>
                                <th colspan="3">SIGNATURE <div>GROUP B</div></th>
                                <th rowspan="2" width="110px" style="font-size: 12pt">Diterima I2P Jam</th>
                                <th rowspan="2" width="110px"> Paraf PIC <div>I2P</div></th>
                            </tr>
                            <tr>
                                <th width="100px" class="heading">IN</th>
                                <th width="100px" class="heading">OUT</th>
                                <th width="110px" class="heading">IN QA jam MSIG</th>
                                <th width="120px" class="heading"><div width="120px" style="margin:2px; padding:5px; font-size: 12pt">OUT QA</div><div width="120px" style="margin:0px; padding:0px; font-size: 12pt"> SML jam</div></th>
                                <th width="110px" class="heading"><div width="110px" style="margin:0px; padding:0px; font-size: 12pt">Received</div><div width="110px" style="margin:0px; padding:0px; font-size: 12pt">QA MSIG</div><div>jam</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style=" margin:0px; padding:0px;">
                                @for($x=0; $x<11; $x++)
                                <td style="border: solid 2px black; vertical-align : middle;text-align:center; margin:0px; padding:0px; height:10px"></td>
                                @endfor
                            </tr>
                            @foreach ($data as $key => $row)
                                @if($row['btd_number'] !=='')
                                    <tr style="border: solid 2px black;">
                                        <td style="border: solid 2px black;" width="110px">{{$row['btd_number']}}</td>
                                        <td style="border: solid 2px black;" width="110px">{{number_format($row['amount'], 0, ',', '.')}}</td>
                                        <td style="border: solid 2px black;" width="110px">{{$row['due_date']}}</td>
                                        @if($key === 0)
                                            @for($m=0; $m<8; $m++)
                                            <td rowspan="{{count($data)}}" style="border: solid 2px black; vertical-align : middle;text-align:center;" width="110px"></td>
                                            @endfor
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                        @for($y=0; $y<2; $y++)
                            <tr height="60px" style="border: solid 2px black;">
                                @for($x=0; $x<11; $x++)
                                <td style="border: solid 2px black; vertical-align : middle;text-align:center;"></td>
                                @endfor
                            </tr>
                        @endfor

                    </table>
            </div>
        </div>
    </div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

<script src="{{asset('js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<script>
    $(document).ready(function() {
    $('#table').DataTable();
} );
</script>
</body>
</html>
