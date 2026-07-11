# Ecommerce Backend Roadmap

This project should grow around the same core admin areas used by mature ecommerce platforms: orders, products, customers, inventory, discounts, marketing, analytics, and settings.

## Features to Implement Next

1. Order operations
   - Add order detail pages with customer, payment, delivery, and item breakdown.
   - Add status history for each order so admins can see when it moved from pending to paid, packed, shipped, and delivered.
   - Add admin notes and customer-facing tracking notes.

2. Inventory management
   - Track SKU, stock quantity, low-stock threshold, and stock status per product.
   - Reduce stock automatically after successful Paystack payment.
   - Restore stock when an order is cancelled or refunded.

3. Discount management
   - Move hard-coded discount codes into a database table.
   - Support percentage discounts, fixed discounts, expiry dates, minimum order amount, max uses, and active/inactive status.
   - Add an admin page to create, edit, disable, and delete discount codes.

4. Banner and marketing management
   - Add placements for hero, category promo, checkout notice, and footer newsletter promo.
   - Store desktop and mobile banner images separately.
   - Add scheduling with start and end dates.

5. Customer management
   - Add customer order history in admin.
   - Add customer profile fields, phone, address book, and admin notes.
   - Add export CSV for customers and orders.

6. Reviews moderation
   - Make new product reviews pending by default.
   - Add approve, reject, and delete actions in admin.
   - Show verified purchase badge only when the reviewer bought the product.

7. Analytics
   - Add dashboard cards for revenue, paid orders, pending orders, top products, low stock, and conversion-ready cart metrics.
   - Add date filters for today, 7 days, 30 days, and custom date range.

8. Security and staff
   - Add separate admin/staff roles.
   - Add permissions for products, orders, discounts, banners, and settings.
   - Log important admin actions.

## Suggested Database Tables

- `order_status_history`: order_id, status, note, created_by, created_at
- `discount_codes`: code, type, value, min_total, max_uses, used_count, starts_at, ends_at, is_active
- `inventory_movements`: product_id, order_id, type, quantity, note, created_at
- `customer_addresses`: user_id, name, phone, address, city, state, is_default
- `admin_activity_logs`: user_id, action, entity_type, entity_id, metadata_json, created_at

## Reference Patterns

- Shopify admin groups operations around orders, products, customers, analytics, marketing, promotions, and discounts.
- Shopify plan features include manual orders, discount codes, inventory, and analytics.
- WooCommerce order management focuses on order statuses, bulk management, payment status, and fulfillment workflow.
