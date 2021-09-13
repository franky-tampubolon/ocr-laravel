<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <style>
        .konten{
            position: relative;
            text-align: center;
        }
        .text{
            position : absolute;
            margin-top : 50px;
            text-align: center;
            font-family : "times-new-roman";
            font-weight : 700;
            font-size : 16pt;
            top : 0%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

    </style>
</head>
<body>
    <div class="konten">
        <img src="{{asset('storage/image/'.$img)}}" alt="" width="1000px", height="700px">        
        <div class="text" width="1080px">
            UM = {{ number_format($qty/100*$payment, 0, ',', '.') }} KG x Rp {{ number_format($price, 0, ',', '.') }} = Rp {{ number_format($qty * $payment * $price, 0, ',', '.') }}
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
</body>
</html>