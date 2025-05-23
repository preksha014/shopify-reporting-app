@extends('shopify-app::layouts.default')

@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')
    

    <div class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-200 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

            <!-- Header Section -->
            {{-- <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Welcome to your Shopify App</h1>
                        <p class="mt-2 text-base text-gray-600">
                            This is a sample app built with
                            <span class="font-semibold text-indigo-700">Laravel</span> and
                            <span class="font-semibold text-pink-700">Shopify App Bridge</span>.
                        </p>
                    </div>
                    <div>
                        <span class="bg-indigo-100 text-indigo-800 text-sm font-medium px-4 py-2 rounded-xl shadow-sm">
                            You are: {{ $shopDomain ?? 'Unknown Store' }}
                        </span>
                    </div>
                </div>
            </div> --}}

            <!-- Stats Cards Grid -->
            {{-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $cards = [
                        ['title' => 'Total Orders', 'color' => 'indigo', 'subtitle' => 'Updated just now'],
                        ['title' => 'Products Listed', 'color' => 'green', 'subtitle' => 'Active products in store'],
                        ['title' => 'New Customers', 'color' => 'pink', 'subtitle' => 'This month'],
                        ['title' => 'Monthly Revenue', 'color' => 'emerald', 'subtitle' => 'Based on last 30 days'],
                        ['title' => 'App Installs', 'color' => 'purple', 'subtitle' => 'Across all stores'],
                        ['title' => 'Support Tickets', 'color' => 'red', 'subtitle' => 'Pending responses'],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div
                        class="bg-white border border-gray-100 rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-200">
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-1/2 mb-3"></div> <!-- Title Placeholder -->
                            <div class="h-10 bg-gray-200 rounded w-1/4 mb-3"></div> <!-- Number Placeholder -->
                            <div class="h-3 bg-gray-200 rounded w-1/3"></div> <!-- Subtitle Placeholder -->
                        </div>
                    </div>
                @endforeach
            </div> --}}

            <!-- Latest 10 Orders Table -->
            <div class="bg-red-100 p-6 rounded-2xl shadow-lg border border-gray-100">
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

                @if ($hasNextPage)
                    <div class="mt-8 text-center">
                        <a href="{{ url()->current() }}?after={{ $nextCursor }}"
                            class="inline-block bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
                            Load More Orders
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection