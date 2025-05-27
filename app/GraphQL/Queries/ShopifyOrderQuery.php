<?php

namespace App\GraphQL\Queries;

class ShopifyOrderQuery
{
    public static function getOrders(): string
    {
        return <<<'GRAPHQL'
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
                        phone
                        shippingAddress {
                            address1
                            city
                            province
                            country
                            zip
                        }
                        billingAddress {
                            address1
                            city
                            province
                            country
                            zip
                        }
                        totalPriceSet {
                            presentmentMoney {
                                amount
                                currencyCode
                            }
                        }
                        lineItems(first: 10) {
                            edges {
                                node {
                                    title
                                    quantity
                                    originalUnitPriceSet {
                                        presentmentMoney {
                                            amount
                                        }
                                    }
                                    customAttributes {
                                        key
                                        value
                                    }
                                }
                            }
                        }
                        customAttributes {
                            key
                            value
                        }
                        tags
                        note
                    }
                }
            }
        }
        GRAPHQL;
    }
}
