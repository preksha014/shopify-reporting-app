<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Product;

class Dashboard extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if ($user && !session()->has('shopDomain') && !session()->has('accessToken')) {
            session([
                'shopDomain' => $user->name,
                'accessToken' => $user->password,
            ]);
        }

        $shopDomain = session('shopDomain');
        $accessToken = session('accessToken');

        if (!$shopDomain || !$accessToken) {
            return response('Shop not authenticated.', 403);
        }

        $orderAfter = $request->query('order_after');
        $productAfter = $request->query('product_after');

        $query = <<<'GRAPHQL'
    query GetOrdersAndProducts($ordersFirst: Int!, $ordersAfter: String, $productsFirst: Int!, $productsAfter: String) {
      orders(first: $ordersFirst, after: $ordersAfter, sortKey: CREATED_AT, reverse: true) {
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

      products(first: $productsFirst, after: $productsAfter, sortKey: CREATED_AT, reverse: true) {
        pageInfo {
          hasNextPage
          endCursor
        }
        edges {
          node {
            id
            title
            bodyHtml
            variants(first: 1) {
              edges {
                node {
                  price
                }
              }
            }
          }
        }
      }
    }
    GRAPHQL;

        $variables = [
            'ordersFirst' => 10,
            'ordersAfter' => $orderAfter,
            'productsFirst' => 10,
            'productsAfter' => $productAfter,
        ];

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$shopDomain}/admin/api/2024-01/graphql.json", [
                    'query' => $query,
                    'variables' => $variables,
                ]);

        if (!$response->ok()) {
            return response('Shopify API error: ' . $response->body(), 500);
        }

        $data = $response->json('data');

        // Handle Orders
        foreach ($data['orders']['edges'] as $edge) {
            $order = $edge['node'];
            if (!Order::where('shopify_order_id', $order['id'])->exists()) {
                Order::create([
                    'shopify_order_id' => $order['id'],
                    'name' => $order['name'],
                    'email' => $order['email'] ?? null,
                    'created_at_shopify' => date('Y-m-d H:i:s', strtotime($order['createdAt'])),
                    'total_price' => $order['totalPriceSet']['presentmentMoney']['amount'],
                    'currency_code' => $order['totalPriceSet']['presentmentMoney']['currencyCode'],
                ]);
            }
        }

        // Handle Products
        foreach ($data['products']['edges'] as $edge) {
            $product = $edge['node'];
            $price = $product['variants']['edges'][0]['node']['price'] ?? null;
            if (!Product::where('shopify_product_id', $product['id'])->exists()) {
                Product::create([
                    'shopify_product_id' => $product['id'],
                    'title' => $product['title'],
                    'body_html' => $product['bodyHtml'],
                    'price' => $price,
                ]);
            }
        }
        return view('welcome', [
            'orders' => $data['orders']['edges'],
            'products' => $data['products']['edges'],
            'ordersPageInfo' => $data['orders']['pageInfo'],
            'productsPageInfo' => $data['products']['pageInfo'],
        ]);
    }
}