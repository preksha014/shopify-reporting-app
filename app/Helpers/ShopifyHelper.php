<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ShopifyHelper
{
    public static function getData(string $shopDomain, string $accessToken, string $query, array $variables = [])
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$shopDomain}/admin/api/2024-01/graphql.json", [
            'query' => $query,
            'variables' => $variables,
        ]);

        return $response;
    }
}
