<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Data from API</h1>
    @if(count($data) > 0)
        <ul>
            @foreach($data as $item)
                <li>{{ $item['title'] }} - {{ $item['price'] }}</li> <!-- Adjust keys based on your API response -->
            @endforeach
        </ul>
    @else
        <p>No data available.</p>
    @endif
</body>
</html>
