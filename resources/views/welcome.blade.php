@extends('shopify-app::layouts.default')

@section('content')
    <link rel="stylesheet" href="{{ asset('build/assets/app-DNjP9tTQ.css') }}">
    <script src="{{ asset('build/assets/app-T1DpEqax.js') }}"></script>

    <div class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-200 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <!-- Latest 10 Orders Table -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                <h2 class="text-2xl font-bold mb-6">Latest 10 Orders</h2>

                @if (!empty($orders) && count($orders) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="border px-4 py-3 text-left">Order Name</th>
                                    <th class="border px-4 py-3 text-left">Email</th>
                                    <th class="border px-4 py-3 text-left">Date</th>
                                    <th class="border px-4 py-3 text-left">Total</th>
                                    <th class="border px-4 py-3 text-left">Financial Status</th>
                                    <th class="border px-4 py-3 text-left">Fulfillment Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800">
                                @foreach ($orders as $orderEdge)
                                    @php $order = $orderEdge['node']; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-3">{{ $order['name'] }}</td>
                                        <td class="border px-4 py-3">{{ $order['email'] ?? 'N/A' }}</td>
                                        <td class="border px-4 py-3">
                                            {{ \Carbon\Carbon::parse($order['createdAt'])->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="border px-4 py-3">
                                            {{ $order['totalPriceSet']['presentmentMoney']['amount'] }}
                                            {{ $order['totalPriceSet']['presentmentMoney']['currencyCode'] }}
                                        </td>
                                        <td class="border px-4 py-3">{{ $order['financialStatus'] ?? 'N/A' }}</td>
                                        <td class="border px-4 py-3">{{ $order['fulfillmentStatus'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No orders found.</p>
                @endif

                {{-- Order data from database --}}
                {{-- @dd($data); --}}

                {{-- @foreach ($data as $dataItem)
                @if(isset($data['edges']))
                @foreach ($data['edges'] as $edge)
                <div class="order-item">
                    <p><strong>Order ID:</strong> {{ $edge['node']['id'] ?? 'N/A' }}</p>
                    <p><strong>Name:</strong> {{ $edge['node']['name'] ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $edge['node']['email'] ?? 'N/A' }}</p>
                    <p><strong>Created:</strong> {{ $edge['node']['createdAt'] ?? 'N/A' }}</p>
                    <p><strong>Total Price:</strong> {{ $edge['node']['totalPriceSet']['presentmentMoney']['amount'] ??
                        'N/A' }}
                    </p>
                    <hr>
                </div>
                @endforeach
                @endif
                @endforeach --}}

                <ul>
                    @foreach ($products as $product)
                        <li class="mb-2">
                            <strong>{{ $product['node']['title'] }}</strong> -
                            ${{ $product['node']['variants']['edges'][0]['node']['price'] ?? 'N/A' }}
                        </li>
                    @endforeach
                </ul>

            </div>
        </div>
    </div>
@endsection