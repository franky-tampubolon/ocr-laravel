<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
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
    <div class="container mt-5">

        <div class="row" >
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 style="text-align:center">Cara menggunakan : </h4>
                        <ol>
                            <li>Upload deal slip dalam bentuk gambar atau pdf (pdf/png/jpg/jpeg)</li>
                            <li>Masukkan nominal total bayar ke bagian input total</li>
                            <li>Klik Upload untuk membuat pdf deal slip dengan tambahan tulisan UM</li>
                            <li>Setelah itu, kamu akan dibawa ke halaman baru.</li>
                            <li>Print halaman baru dalam bentuk pdf</li>
                            <li><strong>File PDF yang anda upload otomasi terhapus dari sistem</strong></li>
                        </ol>
                    </div>
                </div>
                <div class="card">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{session('error')}}
                        </div>
                    @endif
                    <div class="card-body">
                        <form id="form" action="{{route('ocr.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Upload gambar</label>
                                <input type="file" class="form-control" name="file" id="file">
                            </div>
                            <div class="form-group">
                                <label for="total">Total</label>
                                <input type="text" name="total" id="total" class="form-control">
                            </div>
                            <button type="submit" id="submit" class="btn btn-sm btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- start of modal --}}

    <div class="modal fade" id="data" tabindex="-1" aria-labelledby="dataLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataLabel">Konfirmasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">

                        <div class="alert alert-info">
                            <h3>Aturan pengisian harga dan kuantiti</h3>
                            <ul>
                                <li>Format <strong>harga dan kuantiti</strong> tanpa desimal : 12345</li>
                                <li>Format harga dengan desimal : 12345.6</li>
                            </ul>
                        </div>
                         <form id="form-modal" action="{{route('dealslip.tampil')}}" method="POST">
                        @csrf
                            <div class="row">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border" role="status" id="loading" style="display:none">
                                    <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <img src="" alt="" class="img-fluid"  id="img">
                                <input type="file" name="image" id="image" style="display: none">
                                <input type="hidden" name="images" id="images">

                                <button type="button" id="crop" class="btn btn-sm btn-outline-danger">Crop Image</button>
                                <button type="button" id="save-crop" class="btn btn-sm btn-outline-success">Save Image</button>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="harga">Harga <strong>(Rp)</strong></label>
                                        <input type="text" class="form-control" name="harga" id="harga">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="qty">Kuantiti <strong>(kg)</strong></label>
                                        <input type="text" class="form-control" name="qty" id="qty">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="persen">Persentase Uang Muka <strong>(%)</strong></label>
                                        <input type="text" class="form-control" name="persen" id="persen">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="btn-submit" class="btn btn-primary">Confirm</button>
                    </div>
                        </form>
            </div>
        </div>
    </div>

    {{-- end of modal --}}


    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>

    <script type="text/javascript">
    let gambar;
    $(document).ready(function (e) {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('form#form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ route('dealslip.save')}}",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                beforeSend : function(){
                    $('#submit').attr('disabled', true);
                    $('#submit').text('Loading ....');

                },
                success: (data) => {
                    gambar = data.img;
                    let img = $('#img').attr('src', "{{asset('storage/image')}}"+'/'+data.img);
                    $('#img').show();
                    // img.src = "{{asset('public/storage/image')}}"+'/'+data.img+'.png';
                    let harga = $('#harga').val(data.price);
                    let persen = $('#persen').val(data.payment);
                    let images = $('#images').val(data.img);
                    let qty = $('#qty').val(data.qty);
                    $('.modal').modal('show');
                },
                error: function(data){
                    console.log(data);
                }
            });
        });
    });

    let image_crop = null;
    let file = null;
    $('#crop').on('click', function(e){
        e.preventDefault();
        let img = document.querySelector('img');
        const cropper = new Cropper(img, {
                  movable: false,
                  zoomable: false,
                  crop(event) {
                  },
                });
        image_crop = cropper;
    })

    $('#save-crop').on('click', function(){
        if(image_crop ==='undefined' || image_crop === null){
            return false;
        }else{
            image_crop.getCroppedCanvas({height:400,width:600}).toBlob(function(blob){
                file = blob;
                $("#img").attr('src',''+URL.createObjectURL(blob));
            });
            if(file !== null){
                let formData = new FormData();
                formData.append('file', file, gambar);
                $.ajax({
                url:"{{route('dealslip.crop')}}",
                method: 'POST',
                processData: false,
                contentType: false,
                dataType: "JSON",
                data: formData,
                beforeSend: function(){
                    $('#loading').show();
                },
                success: function(data){
                    alert('Gambar berhasil disimpan');
                    if(data.status === true){
                        $('#img').attr('src', URL.createObjectURL(file));
                    }
                }
            })
            }


        }
    })

    </script>
</body>
</html>
