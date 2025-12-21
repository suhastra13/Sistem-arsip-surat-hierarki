<!DOCTYPE html>
<html>

<head>
    <title>Laporan Statistik</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        /* Grid Layout untuk Kartu Statistik */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 5px solid #2c3e50;
            /* Warna garis atas */
        }

        .card h3 {
            margin: 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .card:hover {
            transform: translateY(-5px);
            /* Efek naik dikit pas di hover */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>📊 {{ $judul }}</h1>
        <p>User: <b>{{ Auth::user()->name }}</b> | Role: {{ strtoupper(Auth::user()->role) }}</p>
    </div>

    <div class="stats-container">
        @foreach($rekap as $label => $data)

        <a href="{{ $data['url'] }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="border-top-color: {{ $data['color'] }}; cursor: pointer; transition: transform 0.2s;">
                <h3>{{ $label }}</h3>
                <p style="color: {{ $data['color'] }};">{{ $data['jumlah'] }}</p>
                <span style="font-size: 12px; color: #888;">Klik untuk detail ➔</span>
            </div>
        </a>

        @endforeach
    </div>

    <br>
    <a href="{{ route('dashboard') }}" class="btn-back">⬅ Kembali ke Dashboard</a>

</body>

</html>