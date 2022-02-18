<!DOCTYPE html>
<html>
<head>
    <title>Report Mingguan</title>

    <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        th{
            color: white;
            background-color: red;
        }
        td, th{
            word-wrap: break-word;
            min-width: 160px;
            max-width: 160px;
            text-align: center;
            border: 1px solid #dddddd;
            padding: 8px;
        }
        </style>
</head>
<body>

    <h3>Cabang {{ $branch }}</h3>
    
    <p>Berikut Laporan Barang Yang Stoknya Kurang Dari Stok Minimum</p>

    <table>
        <tr>
            <th scope="col">Nama Barang</th>
            <th scope="col">Serial Number</th>
            <th scope="col">Stok</th>
            <th scope="col">Stok Minimum</th>
            <th scope="col">Keterangan</th>
        </thead>
        <tbody>
            @foreach($items as $i)
                <tr>
                    <td>{{ $i -> itemName }}</td>
                    <td>{{ $i -> serialNo }}</td>
                    <td>{{ $i -> itemStock }} {{ $i -> unit }}</td>
                    <td>{{ $i -> minStock }} {{ $i -> unit }}</td>
                    <td>{{ $i -> description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>