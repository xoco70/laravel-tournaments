<!DOCTYPE html>
<html>
<head>
    <title>Laravel Tournaments</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


</head>
<body>
<div class="container">
    <div class="content">
        <h1 align="center">{{ $tournament->name }}</h1>
        <div class="row">
            <div class="col-md-6">
                @include('laravel-tournaments::partials.settings')
            </div>
            <div class="col-md-6">2</div>
        </div>
    </div>
</div>
</body>
</html>