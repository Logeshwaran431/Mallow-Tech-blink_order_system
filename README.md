<<<<<<< HEAD
# Blink Order System

A store order and inventory system built with Laravel 8 (PHP 7.4) and MySQL. It manages products, customers, orders and stock through a REST API and a set of web pages.

## Features

- Normalized MySQL schema built with migrations and Eloquent relationships
- REST APIs for customers, order creation, order history and low-stock products
- Automatic subtotal, tax and grand-total calculation for every order
- Stock deducted automatically when an order is placed
- Database transactions with row-level locking, so concurrent orders cannot oversell stock
- Order-confirmation emails sent by a queued job
- Request validation and consistent JSON error responses
- Factories and seeders for sample products and customers
- Web pages for orders, customers, products, low stock and restocking

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# set DB_DATABASE, DB_USERNAME and DB_PASSWORD in .env
php artisan migrate --seed
php artisan serve                  # http://127.0.0.1:8000
```

To reset the database with fresh sample data (this deletes all existing data):

```bash
php artisan migrate:fresh --seed
```

### Environment settings

| Key | Default | Purpose |
|---|---|---|
| `QUEUE_CONNECTION` | `database` in `.env.example` | `sync` runs jobs immediately; `database` queues them until a worker runs |
| `MAIL_MAILER` | `log` | Emails are written to `storage/logs/laravel.log` instead of being sent |
| `LOW_STOCK_THRESHOLD` | `10` | Stock level at or below which a product counts as low |
| `CURRENCY` | `INR` | Currency code returned by the API |
| `CURRENCY_SYMBOL` | `₹` | Symbol shown on prices in the pages, emails and API |

## Database

```
customers ──< orders ──< order_items >── products
```

| Table | Key columns | Rules |
|---|---|---|
| `products` | name, code (unique), price, tax_percentage, stock_on_hand | Stock cannot go below 0 |
| `customers` | name, email (unique) | |
| `orders` | customer_id, subtotal, tax_total, grand_total | A customer with orders cannot be deleted |
| `order_items` | order_id, product_id, quantity, unit_price, tax_percentage, subtotal, tax_amount, total | Price and tax are copied from the product when the order is placed. A product used in an order cannot be deleted |
| `jobs`, `failed_jobs` | | Used by the queue |

Relationships: `Customer → orders`, `Order → customer, items`, `OrderItem → order, product`, `Product → orderItems`.

## Order flow

```
POST /api/orders  or  the web "Place order" form
  │
  ├─ StoreOrderRequest validates: customer exists, at least 1 item, each product once, quantity ≥ 1
  │
  ▼
OrderService::create()
  ├─ DB transaction
  │   ├─ lock the product rows (SELECT … FOR UPDATE)
  │   ├─ check stock → if short: InsufficientStockException (422 for the API, form error on the web)
  │   ├─ create the order
  │   ├─ for each item: copy price and tax, calculate the line totals, reduce stock
  │   └─ save subtotal, tax_total and grand_total
  ├─ commit
  └─ SendOrderConfirmation::dispatch($order)      (queued job)
          │
          ▼
     Mail::to(customer email) → OrderConfirmationMail (plain text)
```

### Money calculations

- The calculations use `bcmath`, so there are no floating-point rounding errors.
- Line subtotal = unit price × quantity.
- Line tax = line subtotal × tax %, rounded half up to 2 decimal places.
- The order's subtotal, tax total and grand total are the sums of the rounded lines, so they always match the lines.

### Queue

- The confirmation email is dispatched only after the order's transaction commits.
- The job is attempted up to 3 times, waiting 10 seconds and then 60 seconds between attempts. After that it moves to `failed_jobs`.
- With `QUEUE_CONNECTION=database`, start a worker:

```bash
php artisan queue:work              # process jobs as they arrive
php artisan queue:work --once       # process a single job
php artisan queue:failed            # list failed jobs
php artisan queue:retry all         # retry failed jobs
```

### Restocking

`InventoryService::restock()` adds to the stock in a single atomic `UPDATE`, so it is safe to run while orders are being placed. Each restock is written to the log. Product edit forms cannot overwrite the stock count.

## API

Base URL: `http://127.0.0.1:8000/api`. Every response is JSON. Send `Accept: application/json` and `Content-Type: application/json`.

A Postman collection with every request is in `postman/blink-order-system.postman_collection.json`.

### Customers

| Method | Endpoint | Body or query |
|---|---|---|
| GET | `/customers` | `?search=&per_page=` |
| POST | `/customers` | `{"name": "Arun Kumar", "email": "arun.kumar@example.com"}` |
| GET | `/customers/{id}` | |
| PUT/PATCH | `/customers/{id}` | `{"name": "...", "email": "..."}` (either field) |
| DELETE | `/customers/{id}` | 204, or 409 if the customer has orders |
| GET | `/customers/{id}/orders` | Order history for one customer |

### Products

| Method | Endpoint | Body or query |
|---|---|---|
| GET | `/products` | `?search=&status=in_stock\|low_stock\|out_of_stock&per_page=` |
| GET | `/products/low-stock` | `?threshold=` (default 10) |
| GET | `/products/{id}` | |
| PUT/PATCH | `/products/{id}` | `{"name": "...", "code": "...", "price": "46.50", "tax_percentage": "6"}` |
| POST | `/products/{id}/restock` | `{"quantity": 50}` |

### Orders

| Method | Endpoint | Body or query |
|---|---|---|
| POST | `/orders` | see below |
| GET | `/orders` | `?customer_id=&from=YYYY-MM-DD&to=YYYY-MM-DD&per_page=` |
| GET | `/orders/{id}` | |

Place an order:

```json
{
  "customer_id": 5,
  "items": [
    { "product_id": 7, "quantity": 2 },
    { "product_id": 8, "quantity": 1 }
  ]
}
```

Prices are never accepted from the client. They are read from the products table when the order is placed.

### Response format

Money is returned as an exact decimal string with a display version alongside it:

```json
"currency": "INR",
"grand_total": "110.56",
"grand_total_formatted": "₹110.56"
```

### Errors

| Status | When | Example |
|---|---|---|
| 422 | Validation failed | `{"message": "The given data was invalid.", "errors": {...}}` |
| 422 | Not enough stock | `{"message": "Insufficient stock for one or more products.", "errors": {"items": [{"product_id": 10, "code": "MLK-OAT", "requested": 999, "available": 3}]}}` |
| 404 | Record or route not found | `{"message": "Resource not found."}` |
| 409 | Deleting a customer who has orders | `{"message": "Customer has orders and cannot be deleted."}` |

## Web pages

| URL | Page |
|---|---|
| `/` | Redirects to `/orders` |
| `/orders` | Order history, filterable by customer and date range |
| `/orders/create` | Place-order form |
| `/orders/{id}` | Order detail with line items and totals |
| `/customers` | Customer list with search and order counts |
| `/customers/create`, `/customers/{id}/edit` | Create and edit customers |
| `/customers/{id}` | Customer detail with order history |
| `/products` | Product catalogue with search, stock-status filter and stock badges; restock or edit any row |
| `/products/low-stock` | Low-stock list with an adjustable threshold and an Add button on each row |
| `/products/{id}/edit` | Add stock, and edit name, code, price and tax |

## Code layout

| Layer | Files |
|---|---|
| Models | `app/Models/Customer`, `Product`, `Order`, `OrderItem` |
| API controllers | `app/Http/Controllers/Api/CustomerController`, `CustomerOrderController`, `OrderController`, `ProductController` |
| Web controllers | `app/Http/Controllers/Web/CustomerController`, `OrderController`, `ProductController` |
| Form requests | `app/Http/Requests/Customer/*`, `Order/*`, `Product/*`, `Web/PlaceOrderRequest` |
| API resources | `app/Http/Resources/CustomerResource`, `ProductResource`, `OrderResource`, `OrderItemResource` |
| Services | `app/Services/OrderService` (orders, stock, totals), `InventoryService` (restocking) |
| Queue and mail | `app/Jobs/SendOrderConfirmation`, `app/Mail/OrderConfirmationMail`, `resources/views/emails/orders/confirmation.blade.php` |
| Errors | `app/Exceptions/InsufficientStockException`, `app/Exceptions/Handler`, `app/Http/Middleware/ForceJsonResponse` |
| Helpers | `app/Support/Money` (₹ formatting with Indian digit grouping) and the `@money` Blade directive |
| Config | `config/inventory.php` (low-stock threshold), `app.currency`, `app.currency_symbol` |
| Factories | `CustomerFactory`, `ProductFactory` (with `lowStock()` and `outOfStock()` states) |
| Seeders | `ProductSeeder` (5 fixed products + 18 random), `CustomerSeeder` (a demo customer + 10 random) |
| Routes | `routes/api.php` (route names prefixed `api.`), `routes/web.php` |

## Sample data

After seeding:

- **Demo customer:** `demo.customer@example.com`
- **Fixed products:**

| Code | Product | Starting stock |
|---|---|---|
| COF-001 | Arabica Coffee Beans 1kg | 120 |
| TEA-001 | Green Tea 50 Bags | 80 |
| CUP-008 | Paper Cup 8oz (100 pcs) | 8 (low) |
| MLK-OAT | Oat Milk 1L | 3 (low) |
| SYR-CHO | Chocolate Syrup 750ml | 0 (out of stock) |
=======
# Mallow-Tech-blink_order_system
>>>>>>> 77c0507a3d15a5dc6b1026a82fe8dfc4b2a8f4ce
