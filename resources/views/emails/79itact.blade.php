<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sub }}</title>
    <style>
        /* Style your email here */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 600px;
        }
        h2 {
            color: #333333;
        }
        p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    {{-- Container with styling --}}
    <div class="container">

        {{-- Display dynamic data --}}
        <p><strong>Sub:</strong> {{ $sub }}</p>
        <p><strong>Salutation:</strong> {{ $salutation }}</p>
        <p>{!! nl2br(e($compiledContent)) !!}</p>
    </div>
</body>
</html>
