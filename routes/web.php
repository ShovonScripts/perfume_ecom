<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class , 'index'])->name('home');

Route::get('/dashboard', [PageController::class , 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');

    // Account Dashboard
    Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [App\Http\Controllers\AccountController::class , 'dashboard'])->name('dashboard');
            Route::get('/orders', [App\Http\Controllers\AccountController::class , 'myOrders'])->name('orders.index');
            Route::get('/orders/{order}', [App\Http\Controllers\AccountController::class , 'show'])->name('orders.show');
            Route::get('/wishlist', [App\Http\Controllers\WishlistController::class , 'index'])->name('wishlist');
        }
        );

        Route::post('/wishlist/toggle/{product}', [App\Http\Controllers\WishlistController::class , 'toggle'])
            ->middleware('auth')
            ->name('wishlist.toggle');


        // Product Reviews
        Route::post('products/{product}/reviews', [App\Http\Controllers\ProductReviewController::class , 'store'])
            ->name('reviews.store');
    });

// Shop Page
Route::get('/shop', [ProductController::class , 'index'])->name('shop.index');

// Product Page
Route::get('/product/{slug}', [ProductController::class , 'show'])->name('product.show');

// Category Page
Route::get('/categories', [CategoryController::class , 'index'])->name('categories.index');
Route::get('/category/{slug}', [CategoryController::class , 'show'])->name('category.show');

// Search
Route::get('/search', [\App\Http\Controllers\SearchController::class , 'index'])->name('search');

// SEO: Sitemap + Robots
Route::get('/sitemap.xml', [PageController::class , 'sitemap']);
Route::get('/robots.txt', [PageController::class , 'robots']);

// Cart Routes
Route::get('/cart', [CartController::class , 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class , 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class , 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class , 'remove'])->name('cart.remove');
Route::post('/cart/apply-coupon', [CartController::class , 'applyCoupon'])->name('cart.applyCoupon');
Route::delete('/cart/remove-coupon', [CartController::class , 'removeCoupon'])->name('cart.removeCoupon');

// Combo Cart Route
Route::post('/combo/{combo}/add', [\App\Http\Controllers\ComboController::class , 'addToCart'])->name('combo.add');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class , 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class , 'store'])->name('checkout.store');
Route::get('/order/{order}/payment', [App\Http\Controllers\OrderController::class , 'payment'])->name('orders.payment');
Route::post('/order/{order}/payment', [App\Http\Controllers\OrderController::class , 'submitPayment'])->name('orders.payment.submit');
Route::get('/order/success/{order}', [PageController::class , 'orderSuccess'])->name('order.success');
Route::get('/order/{order}/invoice', [App\Http\Controllers\OrderController::class , 'downloadInvoice'])->name('order.invoice.download');

// Admin Routes
Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Tier 1: All Admin Roles ──────────────────────
        // Dashboard + Order viewing (read-only for staff)
        Route::middleware('role:super_admin,manager,staff')
            ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class , 'index'])->name('dashboard');
            Route::resource('orders', \App\Http\Controllers\Admin\AdminOrderController::class)->only(['index', 'show']);
            Route::get('orders/{order}/invoice', [\App\Http\Controllers\Admin\AdminOrderController::class , 'invoice'])->name('orders.invoice');
            Route::resource('reviews', \App\Http\Controllers\Admin\AdminProductReviewController::class)->only(['index', 'update', 'destroy']);
        }
        );

        // ── Tier 2: Manager + Super Admin ────────────────
        // Full CRUD operations
        Route::middleware('role:super_admin,manager')
            ->group(function () {
            Route::resource('brands', \App\Http\Controllers\Admin\AdminBrandController::class);
            Route::resource('categories', \App\Http\Controllers\Admin\AdminCategoryController::class);
            Route::resource('products', \App\Http\Controllers\Admin\AdminProductController::class);
            Route::post('orders/bulk-update', [\App\Http\Controllers\Admin\AdminOrderController::class , 'bulkUpdate'])->name('orders.bulkUpdate');
            Route::post('orders/{order}/verify', [\App\Http\Controllers\Admin\AdminOrderController::class , 'verifyPayment'])->name('orders.verify'); // New Route
            Route::get('orders/export', [\App\Http\Controllers\Admin\AdminOrderController::class , 'export'])->name('orders.export');
            Route::resource('orders', \App\Http\Controllers\Admin\AdminOrderController::class)->only(['update']);
            Route::resource('coupons', \App\Http\Controllers\Admin\AdminCouponController::class);
            Route::resource('hero-banners', \App\Http\Controllers\Admin\AdminHeroBannerController::class);
            Route::resource('combos', \App\Http\Controllers\Admin\AdminComboController::class);

            // Customer Management
            Route::get('customers', [\App\Http\Controllers\Admin\AdminCustomerController::class , 'index'])->name('customers.index');
            Route::get('customers/{user}', [\App\Http\Controllers\Admin\AdminCustomerController::class , 'show'])->name('customers.show');
            Route::post('customers/{user}/block', [\App\Http\Controllers\Admin\AdminCustomerController::class , 'toggleBlock'])->name('customers.block');
            Route::post('customers/{user}/note', [\App\Http\Controllers\Admin\AdminCustomerController::class , 'updateNote'])->name('customers.updateNote');
        }
        );

        // ── Tier 3: Super Admin Only ─────────────────────
        // Sensitive operations
        Route::middleware('role:super_admin')
            ->group(function () {
            // Inventory Management
            Route::get('inventory', [\App\Http\Controllers\Admin\AdminInventoryController::class , 'index'])->name('inventory.index');
            Route::post('inventory/update', [\App\Http\Controllers\Admin\AdminInventoryController::class , 'updateStock'])->name('inventory.update');
            Route::get('inventory/export', [\App\Http\Controllers\Admin\AdminInventoryController::class , 'export'])->name('inventory.export');

            // Settings
            Route::get('settings', [\App\Http\Controllers\Admin\AdminSettingController::class , 'index'])->name('settings.index');
            Route::post('settings', [\App\Http\Controllers\Admin\AdminSettingController::class , 'update'])->name('settings.update');

            // Staff / Role Management
            Route::get('staff', [\App\Http\Controllers\Admin\AdminStaffController::class , 'index'])->name('staff.index');
            Route::get('staff/create', [\App\Http\Controllers\Admin\AdminStaffController::class , 'create'])->name('staff.create');
            Route::post('staff', [\App\Http\Controllers\Admin\AdminStaffController::class , 'store'])->name('staff.store');
            Route::post('staff/{user}/role', [\App\Http\Controllers\Admin\AdminStaffController::class , 'updateRole'])->name('staff.updateRole');
        }
        );

    });

require __DIR__ . '/auth.php';

// Temporary route to create storage link on live server
Route::get('/create-storage-link', function () {
    try {
        // Remove existing link if any
        if (file_exists(public_path('storage'))) {
            // Attempt to delete it depending on if it's a symlink or directory
            if (is_link(public_path('storage'))) {
                unlink(public_path('storage'));
            }
            else {
            // Ignore standard directory deletion here to avoid accidental data loss if it's the real storage.
            }
        }
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage Link Created Successfully';
    }
    catch (\Exception $e) {
        return 'Error creating storage link: ' . $e->getMessage();
    }
});
