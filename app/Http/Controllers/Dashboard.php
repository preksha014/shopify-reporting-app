<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class Dashboard extends Controller
{
    public function dashboard(Request $request)
    {
        // Try getting user from Auth
        $user = Auth::user();

        // First time: Get from Auth, then store in session
        if ($user && !session()->has('shopDomain') && !session()->has('accessToken')) {
            session([
                'shopDomain' => $user->name,
                'accessToken' => $user->password, // Replace with token column if different
            ]);
        }

        // Fallback to session
        $shopDomain = session('shopDomain');
        $accessToken = session('accessToken');

        // dd(session()->all());
        // If still missing, return error
        if (!$shopDomain || !$accessToken) {
            return response('Shop not authenticated.', 403);
        }

        // Handle pagination cursor
        $afterCursor = $request->query('after');

        // GraphQL query to get orders
        $query = <<<'GRAPHQL'
        query GetOrders($first: Int!, $after: String) {
            orders(first: $first, after: $after, sortKey: CREATED_AT, reverse: true) {
                pageInfo {
                    hasNextPage
                    endCursor
                }
                edges {
                    node {
                        id
                        name
                        email
                        createdAt
                        totalPriceSet {
                            presentmentMoney {
                                amount
                                currencyCode
                            }
                        }
                       
                    }
                }
            }
        }
        GRAPHQL;

        // Variables for query
        $variables = [
            'first' => 10,
            'after' => $afterCursor,
        ];

        // Send request to Shopify
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$shopDomain}/admin/api/2024-01/graphql.json", [
                    'query' => $query,
                    'variables' => $variables,
                ]);

        // Handle failure
        if (!$response->ok()) {
            return response('Shopify API error: ' . $response->body(), 500);
        }

        // Extract orders and pagination info
        $data = $response->json('data.orders');
        $orders = $data['edges'];
        $pageInfo = $data['pageInfo'];

        // dd($orders);
        // Loop and save each order to the database
        foreach ($orders as $edge) {
            $orderNode = $edge['node'];

            // Prevent duplicates
            if (!Order::where('shopify_order_id', $orderNode['id'])->exists()) {
                Order::create([
                    'shopify_order_id' => $orderNode['id'],
                    'name' => $orderNode['name'],
                    'email' => $orderNode['email'] ?? null,
                    'created_at_shopify' => date('Y-m-d H:i:s', strtotime($orderNode['createdAt'])), // FIXED
                    'total_price' => $orderNode['totalPriceSet']['presentmentMoney']['amount'],
                    'currency_code' => $orderNode['totalPriceSet']['presentmentMoney']['currencyCode'],
                ]);
            }
        }
        // Pass to view
        return view('welcome', [
            'orders' => $orders,
            'hasNextPage' => $pageInfo['hasNextPage'],
            'nextCursor' => $pageInfo['endCursor'],
            'shopDomain' => $shopDomain,
        ]);
    }
}