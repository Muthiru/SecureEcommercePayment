<?php

// Namespace for the Vault store core components
namespace Vault;

// Vault namespaced configuration and utility functions
// Category name constants
define('CAT_EDP', 'Eau de Parfum');
define('CAT_BODY_BATH', 'Body & Bath');

define('PRODUCTS', [

    [
        'id' => 'prod_001',
        'name' => 'Oud Encens',
        'category' => CAT_EDP,
        'price' => 295.00,
        'image' => 'https://images.pexels.com/photos/3059609/pexels-photo-3059609.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'bestseller',
        'desc' => 'Aged Hindi oud, church incense & benzoin resin. Deep, ceremonial, long-lasting. 50ml EDP.',
    ],
    [
        'id' => 'prod_002',
        'name' => 'Iris Poudrée',
        'category' => CAT_EDP,
        'price' => 275.00,
        'image' => 'https://images.pexels.com/photos/8450248/pexels-photo-8450248.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'new',
        'desc' => 'Orris root, violet leaf & cashmeran musk. Soft, powdery and skin-close. 50ml EDP.',
    ],
    [
        'id' => 'prod_003',
        'name' => 'Rose Absolue',
        'category' => CAT_EDP,
        'price' => 320.00,
        'image' => 'https://images.pexels.com/photos/13875786/pexels-photo-13875786.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => null,
        'desc' => 'Bulgarian rose absolute, lychee & white sandalwood. Lush and unapologetic. 50ml EDP.',
    ],
    [
        'id' => 'prod_004',
        'name' => 'Bois de Cachemire ',
        'category' => CAT_EDP,
        'price' => 345.00,
        'image' => 'https://images.pexels.com/photos/2922276/pexels-photo-2922276.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'sale',
        'original_price' => 415.00,
        'desc' => 'Cashmere wood, labdanum & spiced vanilla. Warm and wearable into the night. 75ml EDP.',
    ],

    [
        'id' => 'prod_005',
        'name' => 'Bergamote Froide',
        'category' => 'Cologne',
        'price' => 165.00,
        'image' => 'https://images.pexels.com/photos/3831748/pexels-photo-3831748.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => null,
        'desc' => 'Calabrian bergamot, petitgrain & vetiver. Sharp citrus that dries down cool and green. 100ml EDC.',
    ],
    [
        'id' => 'prod_006',
        'name' => 'Mousse de Chêne',
        'category' => 'Cologne',
        'price' => 185.00,
        'image' => 'https://images.pexels.com/photos/1666404/pexels-photo-1666404.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'new',
        'desc' => 'Oakmoss accord, cedarwood & grey ambergris. A classic fougère reinterpreted. 100ml EDC.'
    ],
    [
        'id' => 'prod_007',
        'name' => 'Aqua Lumière',
        'category' => 'Cologne',
        'price' => 145.00,
        'image' => 'https://images.pexels.com/photos/3640668/pexels-photo-3640668.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => null,
        'desc' => 'Marine accord, white tea & transparent musk. Clean and effortless from morning onwards. 100ml EDC.',
    ],

    [
        'id' => 'prod_008',
        'name' => 'Huile Précieuse',
        'category' => CAT_BODY_BATH,
        'price' => 115.00,
        'image' => 'https://images.pexels.com/photos/7795850/pexels-photo-7795850.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'bestseller',
        'desc' => 'Dry body oil with rose hip, argan & jasmine sambac. Absorbs instantly, no residue. 100ml.',
    ],
    [
        'id' => 'prod_009',
        'name' => 'Lait Corps Velours',
        'category' => CAT_BODY_BATH,
        'price' => 88.00,
        'image' => 'https://images.pexels.com/photos/29642374/pexels-photo-29642374.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => null,
        'desc' => 'Whipped body lotion, peony & white musk with shea butter. 200ml.'
    ],
    [
        'id' => 'prod_010',
        'name' => 'Savon Noir Rituel',
        'category' => CAT_BODY_BATH,
        'price' => 58.00,
        'image' => 'https://images.pexels.com/photos/7960114/pexels-photo-7960114.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => null,
        'desc' => 'Moroccan black soap with eucalyptus, argan oil & peppermint extract. 250g.',
    ],

    [
        'id' => 'prod_011',
        'name' => 'Coffret Découverte',
        'category' => 'Gift Sets',
        'price' => 135.00,
        'image' => 'https://images.pexels.com/photos/13875783/pexels-photo-13875783.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'new',
        'desc' => 'Five 10ml travel vials — one from each fragrance family. Perfect introduction to VauLT. Presented in a matte black case.',
    ],
    [
        'id' => 'prod_012',
        'name' => 'Coffret Prestige',
        'category' => 'Gift Sets',
        'price' => 245.00,
        'image' => 'https://images.pexels.com/photos/33820347/pexels-photo-33820347.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=400&h=500',
        'badge' => 'sale',
        'original_price' => 310.00,
        'desc' => 'Full-size EDP of your choice + Huile Précieuse + Lait Corps Velours. Ribbon-tied lacquered box with wax seal.',
    ],
]);

// Returns the product array for the given ID, or null if not found.
function getProduct(string $id): ?array
{
    foreach (PRODUCTS as $p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

// Returns validated cart items from the session, stripping unknown or zero-qty entries.
function getCart(): array
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return [];
    }
    $valid = [];
    foreach ($_SESSION['cart'] as $id => $qty) {
        if (getProduct($id) && is_int($qty) && $qty > 0) {
            $valid[$id] = $qty;
        }
    }
    return $valid;
}

// Returns the total number of individual items in the cart.
function cartCount(): int
{
    $cart = getCart();
    return array_sum($cart);
}

// Returns the pre-tax subtotal for all items in the cart.
function cartSubtotal(): float
{
    $cart = getCart();
    $total = 0;
    foreach ($cart as $id => $qty) {
        $p = getProduct($id);
        if ($p) {
            $total += $p['price'] * $qty;
        }
    }
    return $total;
}
