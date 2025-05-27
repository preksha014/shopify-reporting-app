<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeDetail;
use App\Models\OrderAttribute;

class ShopifyOrderService
{
    protected static array $parsedData = [];
    protected static string $shopDomain = '';

    /**
     * Load the response data from Shopify
     */
    public static function load(array $ordersData, string $shopDomain): void
    {
        self::$parsedData = $ordersData['data']['orders'] ?? [];
        self::$shopDomain = $shopDomain;

        self::syncOrders(self::$parsedData);
    }

    /**
     * Return list of orders
     */
    public static function getOrders(): array
    {
        return self::$parsedData['edges'] ?? [];
    }

    /**
     * Return if more pages exist
     */
    public static function hasNextPage(): bool
    {
        return self::$parsedData['pageInfo']['hasNextPage'] ?? false;
    }

    /**
     * Return the next cursor for pagination
     */
    public static function getNextCursor(): ?string
    {
        return self::$parsedData['pageInfo']['endCursor'] ?? null;
    }

    /**
     * Return the Shopify shop domain
     */
    public static function getShopDomain(): string
    {
        return self::$shopDomain;
    }

    /**
     * Sync orders with local database
     */
    protected static function syncOrders(array $ordersData): void
    {
        $ordersEdges = $ordersData['edges'] ?? [];
        foreach ($ordersEdges as $orderEdge) {
            $orderNode = $orderEdge['node'];
            
            if (Order::where('shopify_order_id', $orderNode['id'])->exists()) {
                continue;
            } 
            // Save customer
            $customer = Customer::firstOrCreate(
                ['email' => $orderNode['email']],
                [
                    'phone' => $orderNode['phone'] ?? null,
                    'city' => $orderNode['shippingAddress']['city'] ?? null,
                    'shipping_address' => $orderNode['shippingAddress']['address1'] ?? null,
                    'billing_address' => $orderNode['billingAddress']['address1'] ?? null,
                ]
            );

            // Save shipping and billing addresses
            $shippingAddress = self::saveAddress($orderNode['shippingAddress'] ?? []);
            $billingAddress = self::saveAddress($orderNode['billingAddress'] ?? []);

            // Save order            
            $order = Order::updateOrCreate(
               
                [
                    'shopify_order_id' => $orderNode['id'],
                    'customer_id' => $customer->id,
                    'shipping_address_id' => $shippingAddress?->id,
                    'billing_address_id' => $billingAddress?->id,
                    'phone' => $orderNode['phone'] ?? null,
                    'email' => $orderNode['email'] ?? null,
                    'custom_attributes' => $orderNode['customAttributes'] ?? null,
                    'tags' => $orderNode['tags'] ?? null,
                    'note' => $orderNode['note'] ?? null,
                ]
            );
           
            // Save line items
            foreach ($orderNode['lineItems']['edges'] ?? [] as $lineItemEdge) {
                $lineItem = $lineItemEdge['node'];

                $productAttribute = ProductAttribute::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'title' => $lineItem['title'],
                    ],
                    [
                        'original_unit_price' => $lineItem['originalUnitPriceSet']['presentmentMoney']['amount'] ?? 0,
                        'quantity' => $lineItem['quantity'],
                        'custom_attributes' => json_encode($lineItem['customAttributes'] ?? []),
                    ]
                );

                // Save line item custom attributes
                foreach ($lineItem['customAttributes'] ?? [] as $attr) {
                    ProductAttributeDetail::updateOrCreate(
                        [
                            'product_attribute_id' => $productAttribute->id,
                            'key' => $attr['key'],
                        ],
                        [
                            'value' => $attr['value'],
                        ]
                    );
                }
            }

            // Save order-level custom attributes
            foreach ($orderNode['customAttributes'] ?? [] as $attr) {
                OrderAttribute::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'key' => $attr['key'],
                    ],
                    ['value' => $attr['value']]
                );
            }
        }
    }

    /**
     * Save address record
     */
    private static function saveAddress(array $data): ?Address
    {
        if (empty($data)) return null;

        return Address::updateOrCreate(
            [
                'address1' => $data['address1'] ?? null,
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
                'country' => $data['country'] ?? null,
                'zip' => $data['zip'] ?? null,
            ],
            []
        );
    }
}
