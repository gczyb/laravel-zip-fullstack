<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Városok</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h2>Városok listája</h2>
    <table>
        <thead>
            <tr>
                <th>Város</th>
                <th>Megye</th>
                <th>Irányítószám</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cities as $city)
                <tr>
                    <td>{{ $city->name }}</td>
                    <td>{{ $city->county->name ?? '' }}</td>
                    <td>
                        @foreach($city->postalCodes as $postalCode)
                            {{ $postalCode->code }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>