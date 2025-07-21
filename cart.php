<?php
session_start();
require_once 'db_connect.php';

// Get cart items from database
$cart_items = [];
$total = 0;
$subtotal = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT ci.id, p.id as product_id, p.name, p.price, p.image, ci.quantity 
                           FROM cart_items ci
                           JOIN products p ON ci.product_id = p.id
                           WHERE ci.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
    $total = $subtotal; // In a real app you'd add tax/shipping
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronica - Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'electronica-blue': '#131921',
                        'electronica-light-blue': '#232F3E',
                        'electronica-yellow': '#FEBD69',
                        'electronica-orange': '#F3A847',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-electronica-blue text-white">
        <div class="flex items-center px-4 py-2">
            <div class="flex items-center">
                <a href="index.php" class="border border-transparent hover:border-white p-2">
                    <i class="fas fa-bars text-sm mr-1"></i>
                </a>
                <a href="index.php" class="border border-transparent hover:border-white p-2">
                    <span class="text-2xl font-bold text-white">Electronica</span>
                </a>
            </div>

            <div class="hidden md:flex items-center mx-2 border border-transparent hover:border-white p-2 cursor-pointer">
                <i class="fas fa-map-marker-alt mr-1"></i>
                <div>
                    <div class="text-xs text-gray-300">Deliver to</div>
                    <div class="text-sm font-bold">United States</div>
                </div>
            </div>

            <div class="flex flex-1 mx-2">
                <div class="flex w-full">
                    <select class="bg-gray-100 text-black text-sm px-2 py-2 rounded-l-md border-r border-gray-300 focus:outline-none">
                        <option>All</option>
                        <option>Electronics</option>
                        <option>Computers</option>
                        <option>Home</option>
                    </select>
                    <input type="text" class="flex-grow px-4 py-2 focus:outline-none text-black">
                    <button class="bg-electronica-yellow hover:bg-electronica-orange text-black px-4 py-2 rounded-r-md">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="hidden md:flex items-center mx-1 border border-transparent hover:border-white p-2 cursor-pointer">
                <img src="https://flagcdn.com/w20/us.png" alt="US Flag" class="h-4 mr-1">
                <span class="text-sm font-bold">EN</span>
                <i class="fas fa-caret-down ml-1"></i>
            </div>

            <div class="hidden md:flex flex-col mx-1 border border-transparent hover:border-white p-2 cursor-pointer">
                <div class="text-xs">Hello, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Sign in'; ?></div>
                <div class="text-sm font-bold">Account & Lists <i class="fas fa-caret-down"></i></div>
            </div>

            <div class="hidden md:flex flex-col mx-1 border border-transparent hover:border-white p-2 cursor-pointer">
                <div class="text-xs">Returns</div>
                <div class="text-sm font-bold">& Orders</div>
            </div>

            <div class="flex items-center mx-1 border border-transparent hover:border-white p-2 cursor-pointer">
                <div class="relative">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                    <span class="absolute -top-1 -right-1 bg-electronica-orange text-black text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        <?php echo count($cart_items); ?>
                    </span>
                </div>
                <span class="text-sm font-bold ml-1">Cart</span>
            </div>
        </div>

        <div class="flex items-center bg-electronica-light-blue px-4 py-2 text-sm">
            <div class="flex items-center mr-4 border border-transparent hover:border-white p-1 cursor-pointer">
                <i class="fas fa-bars mr-1"></i>
                <span>All</span>
            </div>
            <a href="#" class="mr-4 border border-transparent hover:border-white p-1 cursor-pointer">Today's Deals</a>
            <a href="#" class="mr-4 border border-transparent hover:border-white p-1 cursor-pointer">Customer Service</a>
            <a href="#" class="mr-4 border border-transparent hover:border-white p-1 cursor-pointer">Registry</a>
            <a href="#" class="mr-4 border border-transparent hover:border-white p-1 cursor-pointer">Gift Cards</a>
            <a href="#" class="mr-4 border border-transparent hover:border-white p-1 cursor-pointer">Sell</a>
        </div>
    </header>

    <!-- Cart Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center mb-4">
            <h1 class="text-2xl font-bold">Shopping Cart</h1>
            <span class="ml-4 text-gray-600"><?php echo count($cart_items); ?> items</span>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- Cart Items -->
            <div class="md:w-2/3 bg-white rounded-lg shadow-sm p-4">
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="flex flex-col sm:flex-row border-b border-gray-200 py-4">
                            <div class="sm:w-1/3 flex justify-center">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="h-32 object-contain">
                            </div>
                            <div class="sm:w-2/3 mt-4 sm:mt-0 sm:pl-4">
                                <div class="flex justify-between">
                                    <h2 class="text-lg font-medium"><?php echo htmlspecialchars($item['name']); ?></h2>
                                    <span class="text-lg font-bold">$<?php echo number_format($item['price'], 2); ?></span>
                                </div>
                                <div class="mt-2 text-green-600">In Stock</div>
                                <div class="mt-2 text-sm">Eligible for FREE Shipping</div>
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" class="mr-2">
                                    <span class="text-sm">This is a gift</span>
                                    <a href="#" class="text-blue-500 text-sm ml-4 hover:underline">Learn more</a>
                                </div>
                                <div class="flex items-center mt-4">
                                    <div class="border border-gray-300 rounded-md flex">
                                        <button class="px-3 py-1 bg-gray-100 hover:bg-gray-200" 
                                                onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="px-3 py-1 border-x border-gray-300"><?php echo $item['quantity']; ?></span>
                                        <button class="px-3 py-1 bg-gray-100 hover:bg-gray-200"
                                                onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button class="text-blue-500 text-sm ml-4 hover:underline"
                                            onclick="removeItem(<?php echo $item['id']; ?>)">
                                        Delete
                                    </button>
                                    <button class="text-blue-500 text-sm ml-4 hover:underline">Save for later</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <h2 class="text-xl font-medium mb-2">Your Electronica Cart is empty</h2>
                        <p class="text-gray-600 mb-4">Shop today's deals</p>
                        <a href="index.php" class="bg-electronica-yellow hover:bg-electronica-orange text-black px-4 py-2 rounded-md inline-block">
                            Continue shopping
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if (count($cart_items) > 0): ?>
                    <div class="text-right mt-4">
                        <span class="text-gray-600">Subtotal (<?php echo count($cart_items); ?> items): </span>
                        <span class="text-lg font-bold">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cart Summary -->
            <?php if (count($cart_items) > 0): ?>
                <div class="md:w-1/3">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="text-green-600 mb-2">
                            <i class="fas fa-check-circle mr-1"></i>
                            <span>Your order qualifies for FREE Shipping</span>
                        </div>
                        <div class="mb-4">
                            <span class="text-gray-600">Subtotal (<?php echo count($cart_items); ?> items): </span>
                            <span class="text-lg font-bold">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="gift" class="mr-2">
                            <label for="gift" class="text-sm">This order contains a gift</label>
                        </div>
                        <button class="w-full bg-electronica-yellow hover:bg-electronica-orange text-black py-2 rounded-md mb-2">
                            Proceed to checkout
                        </button>
                        <div class="text-xs text-gray-500 mt-2">
                            By placing your order, you agree to Electronica's privacy notice and conditions of use.
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-4 mt-4">
                        <h3 class="font-medium mb-2">Recommended for you</h3>
                        <div class="flex overflow-x-auto gap-2">
                            <!-- Recommended items would come from database -->
                            <div class="flex-shrink-0 w-32">
                                <img src="https://m.media-amazon.com/images/I/71Swqqe7XAL._AC_SX466_.jpg" alt="Product" class="h-24 object-contain mx-auto">
                                <div class="text-sm mt-2">Wireless Earbuds</div>
                                <div class="text-sm font-bold">$49.99</div>
                            </div>
                            <div class="flex-shrink-0 w-32">
                                <img src="https://m.media-amazon.com/images/I/61+Q6RhJD6L._AC_SL1500_.jpg" alt="Product" class="h-24 object-contain mx-auto">
                                <div class="text-sm mt-2">Bluetooth Speaker</div>
                                <div class="text-sm font-bold">$89.99</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-electronica-light-blue text-white mt-8">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">Get to Know Us</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">About Us</a></li>
                        <li><a href="#" class="hover:underline">Careers</a></li>
                        <li><a href="#" class="hover:underline">Press Releases</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Make Money with Us</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">Sell products</a></li>
                        <li><a href="#" class="hover:underline">Become an Affiliate</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Payment Products</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">Business Card</a></li>
                        <li><a href="#" class="hover:underline">Shop with Points</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Let Us Help You</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">Your Account</a></li>
                        <li><a href="#" class="hover:underline">Your Orders</a></li>
                        <li><a href="#" class="hover:underline">Shipping Rates</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-600 mt-8 pt-8 text-center">
                <img src="logo.png" alt="Electronica" class="h-8 mx-auto mb-4">
                <div class="text-sm">
                    <a href="#" class="hover:underline">Conditions of Use</a> | 
                    <a href="#" class="hover:underline">Privacy Notice</a> | 
                    <a href="#" class="hover:underline">Interest-Based Ads</a>
                </div>
                <div class="text-xs mt-2">© 1996-<?php echo date('Y'); ?>, Electronica.com, Inc.</div>
            </div>
        </div>
    </footer>

    <script>
        function updateQuantity(itemId, action) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }

        function removeItem(itemId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>