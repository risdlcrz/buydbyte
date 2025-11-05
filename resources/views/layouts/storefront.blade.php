<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Home') - {{ config('app.name', 'BuyDByte') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Storefront Styles -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #ef4444;
            --info-color: #4299e1;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .product-card .card-img-top {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
        }
        
        .category-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            color: white;
            transform: scale(1.03);
        }
        
        .footer {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            color: white;
        }
        
        .price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .sale-price {
            color: #e53e3e;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #a0aec0;
            font-size: 1rem;
        }
        
        .badge-sale {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        
        .search-box {
            border-radius: 25px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .cart-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            position: absolute;
            top: -8px;
            right: -8px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-shop"></i>
                BuyDByte
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                <div class="mx-auto" style="width: 400px;">
                    <form action="{{ route('storefront.products') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control search-box" name="search" 
                                   placeholder="Search products..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Side Menu -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('storefront.products') }}">
                            <i class="bi bi-grid"></i> Products
                        </a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            <span class="cart-badge" id="notification-count" style="display: none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 300px;">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <div id="notifications-list">
                                <li><p class="dropdown-item text-muted mb-0">No new notifications</p></li>
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center" href="{{ route('customer.notifications.index') }}">View all notifications</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart"></i> Cart
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>
                    </li>

                    <!-- Compare -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('compare.index') }}">
                            <i class="bi bi-compare"></i> Compare
                            <span class="cart-badge comparison-count" id="comparison-count" style="display: none;">0</span>
                        </a>
                    </li>

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                {{ auth()->user()->first_name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('account') }}">My Account</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0 rounded-0" role="alert">
            <div class="container">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0" role="alert">
            <div class="container">
                <i class="bi bi-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5 py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-white mb-3">
                        <i class="bi bi-shop"></i>
                        BuyDByte
                    </h5>
                    <p class="text-light">Your one-stop shop for digital products and electronics. Quality guaranteed, fast delivery.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram fs-4"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('storefront.products') }}" class="text-light text-decoration-none">Products</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Categories</a></li>
                        <li><a href="#" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="text-white mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Returns</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Shipping</a></li>
                        <li><a href="#" class="text-light text-decoration-none">FAQs</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="text-white mb-3">Newsletter</h6>
                    <p class="text-light">Subscribe to get updates on new products and offers.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-light opacity-25">
            <div class="text-center text-light">
                <p>&copy; {{ date('Y') }} BuyDByte. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart Count Update -->
    <script>
        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateComparisonCount();
        });

        function updateCartCount() {
            fetch('{{ route("cart.count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
        }

        // Call updateCartCount after successful cart operations
        document.addEventListener('submit', function(e) {
            if (e.target.matches('form[action*="/cart/add"]') || 
                e.target.matches('form[action*="/cart/update"]') ||
                e.target.matches('form[action*="/cart/remove"]') ||
                e.target.matches('form[action*="/cart/clear"]')) {
                // Add a small delay to allow the server to process the request
                setTimeout(updateCartCount, 500);
            }
        });

        // Comparison functionality
        function updateComparisonCount() {
            fetch('{{ route("compare.count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('comparison-count');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline' : 'none';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Handle comparison button clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.compare-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.compare-btn');
                const productId = btn.dataset.productId;
                const productSlug = btn.dataset.productSlug;
                
                // Disable button
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
                
                fetch(`/compare/add/${productSlug}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: '_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        // Show success message
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> Added';
                        btn.classList.remove('btn-outline-secondary');
                        btn.classList.add('btn-success');
                        
                        // Update comparison count
                        updateComparisonCount();
                        
                        // Show toast or alert
                        showAlert(data.message, 'success');
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.classList.remove('btn-success');
                            btn.classList.add('btn-outline-secondary');
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        const message = data && data.message ? data.message : 'Unknown error occurred';
                        showAlert(message, 'warning');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Comparison error:', error);
                    showAlert('Unable to add product to comparison. Please try again.', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            }
        });

        // Simple alert function
        function showAlert(message, type = 'info') {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 4000);
        }
    </script>

    <!-- Notifications JavaScript -->
    <script>
        function getNotificationIcon(type) {
            const icons = {
                'payment_status': '<i class="bi bi-credit-card text-primary fs-5"></i>',
                'payment_reminder': '<i class="bi bi-clock text-warning fs-5"></i>',
                'order_status': '<i class="bi bi-box-seam text-info fs-5"></i>',
                'promotion': '<i class="bi bi-tag text-success fs-5"></i>',
                'price_drop': '<i class="bi bi-graph-down-arrow text-danger fs-5"></i>',
                'feedback_response': '<i class="bi bi-chat-dots text-primary fs-5"></i>',
                'account_security': '<i class="bi bi-shield-lock text-secondary fs-5"></i>',
                'shipping_tracking': '<i class="bi bi-truck text-info fs-5"></i>',
                'back_in_stock': '<i class="bi bi-arrow-repeat text-success fs-5"></i>',
                'review_request': '<i class="bi bi-star text-warning fs-5"></i>',
                'loyalty_points': '<i class="bi bi-gift text-success fs-5"></i>',
            };
            return icons[type] || '<i class="bi bi-bell text-secondary fs-5"></i>';
        }

        function getPaymentStatusBadge(notification) {
            if (!notification || !notification.type) return '';
            if (!notification.type.includes('payment')) return '';

            const status = notification.status || notification.data?.status || '';
            const badges = {
                'pending': '<div class="mt-1"><span class="badge bg-warning">Pending</span></div>',
                'processing': '<div class="mt-1"><span class="badge bg-info">Processing</span></div>',
                'completed': '<div class="mt-1"><span class="badge bg-success">Completed</span></div>',
                'failed': '<div class="mt-1"><span class="badge bg-danger">Failed</span></div>',
                'refunded': '<div class="mt-1"><span class="badge bg-secondary">Refunded</span></div>',
                'partially_refunded': '<div class="mt-1"><span class="badge bg-info">Partially Refunded</span></div>',
                'due': '<div class="mt-1"><span class="badge bg-warning">Payment Due</span></div>',
                'overdue': '<div class="mt-1"><span class="badge bg-danger">Overdue</span></div>'
            };
            return badges[status] || '';
        }

        function updateNotifications() {
            fetch('{{ route("notifications.get") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                const notificationBadge = document.getElementById('notification-count');
                const notificationsList = document.getElementById('notifications-list');
                
                if (data.unread_count > 0) {
                    notificationBadge.textContent = data.unread_count;
                    notificationBadge.style.display = 'inline';
                } else {
                    notificationBadge.style.display = 'none';
                }

                if (data.notifications && data.notifications.length > 0) {
                    notificationsList.innerHTML = data.notifications
                        .map(notification => `
                            <li>
                                <a class="dropdown-item" href="${notification.link || '#'}">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            ${getNotificationIcon(notification.type)}
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">${notification.message}</p>
                                            <small class="text-muted">${notification.time_ago}</small>
                                            ${getPaymentStatusBadge(notification)}
                                        </div>
                                        ${!notification.read ? '<span class="badge bg-primary">New</span>' : ''}
                                    </div>
                                </a>
                            </li>
                        `).join('');
                } else {
                    notificationsList.innerHTML = '<li><p class="dropdown-item text-muted mb-0">No new notifications</p></li>';
                }
            })
            .catch(error => console.error('Error updating notifications:', error));
        }

        // Update notifications on page load and every minute
        document.addEventListener('DOMContentLoaded', function() {
            updateNotifications();
            setInterval(updateNotifications, 60000); // Check every minute
        });

        // Mark notifications as read when dropdown is opened
        document.querySelector('.notification-dropdown').addEventListener('show.bs.dropdown', function () {
            fetch('{{ route("notifications.markAsRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(() => updateNotifications())
            .catch(error => console.error('Error marking notifications as read:', error));
        });
    </script>
    
    @stack('scripts')
</body>
</html>