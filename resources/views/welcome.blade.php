@extends('shopify-app::layouts.default')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latest Shopify Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background: #343a40;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .badge {
            padding: 3px 7px;
            background: #17a2b8;
            color: white;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<h2>Latest 10 Shopify Orders</h2>
<h2>{{$shopDomain}}</h2>
<table>
    <thead>
        <tr>
            <th>Order Name</th>
            <th>Email</th>
            <th>Date</th>
            <th>Total</th>
            <th>Phone</th>
            {{-- <th>Tags</th>
            <th>Note</th> --}}
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
            @php $node = $order['node']; @endphp
            <tr>
                <td>{{ $node['name'] ?? 'N/A' }}</td>
                <td>{{ $node['email'] ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($node['createdAt'])->format('Y-m-d') ?? 'N/A' }}</td>
                <td>
                    {{ $node['totalPriceSet']['presentmentMoney']['amount'] ?? '0.00' }}
                    {{ $node['totalPriceSet']['presentmentMoney']['currencyCode'] ?? '' }}
                </td>
                <td>{{ $node['phone'] ?? 'N/A' }}</td>
                {{-- <td>
                    @foreach ($node['tags'] ?? [] as $tag)
                        <span class="badge">{{ $tag }}</span>
                    @endforeach
                </td>
                <td>{{ $node['note'] ?? 'N/A' }}</td> --}}
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center;">No orders found.</td>
            </tr>
        @endforelse
    </tbody>
</table>


</body>
</html>
