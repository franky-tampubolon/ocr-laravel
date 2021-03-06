<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Excel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container">
         <a class="navbar-brand" href="{{url('/')}}">Rekap Excel</a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse" id="navbarSupportedContent">
           <ul class="navbar-nav ml-auto">
             <li class="nav-item active">
               <a class="nav-link" href="{{route('upstream')}}">Upstream</a>
             </li>
             <li class="nav-item active">
               <a class="nav-link" href="{{route('downstream')}}">Downstream</a>
             </li>
           </ul>
         </div>
        </div>
     </nav>
    <div class="container mt-5">
        <div class="row" >
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 style="text-align:center">Cara menggunakan : </h4>
                        <ol>
                            <li>Pilih jenis rekap yang akan anda upload (kebun atau cpo)</li>
                            <li>Upload file robot dalam bentuk excel</li>
                            <li>Klik Upload. Tunggu sebentar, sistem akan mendownload hasil dalam bentuk excel</li>
                        </ol>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form id="form" action="{{route('downstream.import')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="jenis_rekap" class="jenis_rekap">Pilih Jenis rekap</label>
                                <select class="form-control" id="jenis_rekap" name="jenis_rekap">
                                    <option value="kebun">Rekap Kebun</option>
                                    <option value="cpo">Rekap CPO</option>
                                    <option value="vendor">Rekap Vendor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="file">Upload Excel</label>
                                <input type="file" class="form-control" name="excel" id="excel">
                            </div>
                            <button type="submit" id="submit" class="btn btn-sm btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

</body>
</html>
