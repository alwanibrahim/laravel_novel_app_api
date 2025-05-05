<!DOCTYPE html>
<html>
<head>
    <title>Extract Google Token</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        h1 {
            color: #333;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .loading {
            display: none;
        }
        .result {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Google OAuth Token Extractor</h1>

    <div class="container">
        <p>Mengekstrak access token dari URL fragment...</p>
        <div class="loading">Memuat data dari Google...</div>

        <div class="result">
            <h3>Access Token:</h3>
            <pre id="token-display">Menunggu ekstraksi token...</pre>

            <h3>Fragment URL:</h3>
            <pre id="fragment-display">Menunggu fragment URL...</pre>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Ekstrak token dari URL fragment
            const fragment = window.location.hash.substring(1);
            const params = {};

            // Parse parameter fragment
            fragment.split('&').forEach(item => {
                const parts = item.split('=');
                params[parts[0]] = decodeURIComponent(parts[1]);
            });

            // Tampilkan fragment untuk debugging
            $('#fragment-display').text(fragment);

            if (params.access_token) {
                // Tampilkan token untuk debugging
                $('#token-display').text(params.access_token);

                // Kirim token ke server untuk mendapatkan data user
                $('.loading').show();

                $.ajax({
                    url: '{{ route("google.token") }}',
                    type: 'POST',
                    data: {
                        access_token: params.access_token,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Server akan me-redirect dengan dd($googleUser),
                        // jadi kita tidak perlu handle response di sini
                    },
                    error: function(xhr) {
                        $('.loading').hide();
                        $('.result').append('<div class="error">Error: ' + xhr.responseText + '</div>');
                    }
                });
            } else {
                $('#token-display').text('Tidak ditemukan token di URL!');
            }
        });
    </script>
</body>
</html>
