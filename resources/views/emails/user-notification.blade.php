<!DOCTYPE html>
<html>
<head>
    <title>{{ $data['subject'] }}</title>
</head>
<body>
    <h1>{{ $data['title'] }}</h1>
    <p>{{ $data['body'] }}</p>

    <p>Terima kasih,</p>
    <p>{{ config('app.name') }}</p>
</body>
</html>
