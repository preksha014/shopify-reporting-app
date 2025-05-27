<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\GraphQL\Queries\ShopifyOrderQuery;
use App\Helpers\ShopifyHelper;
use App\Services\ShopifyOrderService;

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
        $query = ShopifyOrderQuery::getOrders();
        $variables = [
            'first' => 10,
            'after' => $request->query('after'),
        ];

        $response = ShopifyHelper::getData($shopDomain, $accessToken, $query, $variables);

        if (!$response->ok()) {
            return response('Shopify API error: ' . $response->body(), 500);
        }
        ShopifyOrderService::load($response->json(), $shopDomain);

        // Return view with accessors
        return view('welcome', [
            'orders' => ShopifyOrderService::getOrders(),
            'hasNextPage' => ShopifyOrderService::hasNextPage(),
            'nextCursor' => ShopifyOrderService::getNextCursor(),
            'shopDomain' => ShopifyOrderService::getShopDomain(),
        ]);
    }
}
