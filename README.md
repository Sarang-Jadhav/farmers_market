# Farmers Market Web Application

A comprehensive role-based Farmers Market web application built with PHP, MySQL, Bootstrap, and HTML/CSS.

## 📁 Project Structure

```
farmers_market/
├── db.php                          # Database connection
├── index.php                       # Home page
├── login.php                       # Login page with role-based redirect
├── register.php                    # Registration with role selection
├── logout.php                      # Logout functionality
├── profile.php                     # User profile management
├── database.sql                    # Database schema and initial data
│
├── farmer/
│   ├── dashboard.php              # Farmer dashboard with statistics
│   ├── add_product.php            # Add new product
│   ├── manage_products.php        # Edit/Delete products
│   └── view_orders.php            # View orders from customers
│
├── user/
│   ├── dashboard.php              # Customer dashboard
│   ├── view_products.php          # Browse all products
│   ├── cart.php                   # Shopping cart
│   ├── checkout.php               # Order checkout
│   ├── orders.php                 # Order history
│   └── get_cart_count.php         # AJAX endpoint for cart count
│
└── includes/
    ├── header.php                 # Navigation header
    ├── footer.php                 # Footer component
    └── auth.php                   # Authentication & authorization functions
```

## 🔧 Setup Instructions

### 1. Prerequisites
- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### 2. Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `farmers_market`
3. Import the `database.sql` file:
   - Click on the `farmers_market` database
   - Go to "Import" tab
   - Select `database.sql` file
   - Click "Go"

### 3. Installation Steps

1. **Extract Project**
   - Extract the `farmers_market` folder to your XAMPP htdocs directory
   - Path: `C:\xampp\htdocs\farmers_market`

2. **Configure Database Connection** (Optional)
   - Open `db.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');  // Default is empty
     define('DB_NAME', 'farmers_market');
     ```

3. **Start XAMPP**
   - Start Apache and MySQL services

4. **Access Application**
   - Open browser and go to: `http://localhost/farmers_market/`

## 🔐 Demo Accounts

### Farmer Account
- **Email:** farmer@test.com
- **Password:** password123
- **Access:** http://localhost/farmers_market/login.php

### Customer Account
- **Email:** customer@test.com
- **Password:** password123
- **Access:** http://localhost/farmers_market/login.php

## 👨‍🌾 Farmer Features

1. **Dashboard**
   - View product count
   - View order statistics
   - View total revenue
   - Quick access to all features

2. **Add Product**
   - Product name
   - Description
   - Price per kg
   - Available quantity

3. **Manage Products**
   - Edit product details
   - Delete products
   - Update prices and quantities

4. **View Orders**
   - See all orders placed for their products
   - View customer information
   - View order items and quantities

## 👥 Customer Features

1. **Dashboard**
   - View cart items count
   - View total orders
   - View total spending
   - Browse featured products

2. **Browse Products**
   - View all available products
   - See farmer names
   - View prices and quantities
   - Add multiple items to cart

3. **Shopping Cart**
   - Add/Remove items
   - Update quantities dynamically
   - View total price calculation
   - Proceed to checkout

4. **Checkout**
   - Review order items
   - Confirm delivery
   - Place order

5. **Order History**
   - View all past orders
   - See order details
   - View items in each order

6. **Profile**
   - Update personal information
   - Change password
   - View account details

## 🔒 Security Features

### Authentication & Authorization
- ✅ Password hashing using `password_hash()`
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ Page-level authorization checks
- ✅ Farmer pages blocked for customers
- ✅ Customer pages blocked for farmers

### Database Security
- ✅ Prepared statements for SQL injection prevention
- ✅ Input validation on all forms
- ✅ Email uniqueness checking
- ✅ Foreign key constraints

## 📊 Database Tables

### users
- `id` (Primary Key)
- `name` (VARCHAR 100)
- `email` (UNIQUE VARCHAR 100)
- `password` (VARCHAR 255 - hashed)
- `role` (ENUM: 'farmer', 'customer')
- `created_at` (TIMESTAMP)

### products
- `id` (Primary Key)
- `farmer_id` (Foreign Key → users)
- `name` (VARCHAR 100)
- `description` (TEXT)
- `price_per_kg` (DECIMAL 10,2)
- `quantity` (DECIMAL 10,2)
- `created_at` (TIMESTAMP)

### cart
- `id` (Primary Key)
- `user_id` (Foreign Key → users)
- `product_id` (Foreign Key → products)
- `quantity` (DECIMAL 10,2)
- `added_at` (TIMESTAMP)

### orders
- `id` (Primary Key)
- `user_id` (Foreign Key → users)
- `total_price` (DECIMAL 10,2)
- `order_date` (TIMESTAMP)

### order_items
- `id` (Primary Key)
- `order_id` (Foreign Key → orders)
- `product_id` (Foreign Key → products)
- `quantity` (DECIMAL 10,2)
- `price` (DECIMAL 10,2)

## 🎨 UI/UX Features

### Design
- **Bootstrap 5.3** for responsive design
- **Font Awesome 6.0** for icons
- **Custom CSS** with color scheme:
  - Primary: #2c5f2d (Green)
  - Secondary: #f4a642 (Orange)
  - Success: #28a745 (Light Green)

### Responsive Layout
- Mobile-friendly navigation
- Responsive grid system
- Bootstrap modals for actions
- Mobile-optimized tables

### Navigation
- Role-based navigation menu
- Dynamic menu based on user role
- Quick access to key pages
- Logout functionality

## 🔄 Workflow Examples

### Farmer Workflow
1. Login with farmer credentials
2. Land on farmer dashboard
3. Click "Add Product" → Add product details
4. Click "Manage Products" → Edit/Delete products
5. Click "Orders" → View customer orders
6. Logout

### Customer Workflow
1. Login with customer credentials
2. Land on customer dashboard
3. Click "Shop" → Browse all products
4. Add items to cart with desired quantity
5. Go to cart and review items
6. Click "Checkout" to place order
7. View order history in "Orders" page
8. Logout

## 📝 Key Implementation Details

### Cart System
- Multi-item support
- Dynamic quantity input
- Real-time cart count update
- AJAX cart count fetch

### Checkout Process
- Order creation in database
- Order items tracking
- Product quantity deduction
- Cart clearing after checkout
- Transaction support

### Authorization
- Session validation on every page
- Role checking with auth helper functions
- Automatic redirect for unauthorized access
- Logout clears session

## 🚀 Enhancement Ideas

1. **Payment Integration**
   - Stripe or PayPal integration
   - Payment status tracking

2. **Notifications**
   - Email notifications for orders
   - SMS alerts

3. **Search & Filter**
   - Product search
   - Category filters
   - Farmer filtering

4. **Reviews & Ratings**
   - Customer reviews
   - Product ratings
   - Farmer ratings

5. **Advanced Features**
   - Delivery tracking
   - Wishlists
   - Product recommendations
   - Bulk orders

## 📞 Support & Troubleshooting

### Common Issues

**1. "Connection failed" Error**
- Ensure MySQL is running in XAMPP
- Check database credentials in `db.php`
- Verify database name is `farmers_market`

**2. "Access Denied" on Pages**
- Make sure you're logged in
- Check user role matches page requirements
- Clear browser cookies and try again

**3. Cart Not Updating**
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify `get_cart_count.php` path is correct

**4. Products Not Showing**
- Ensure farmer has added products
- Check product quantity > 0
- Verify products are not deleted

## 📄 License

This project is open-source and free to use for educational purposes.

---

**Created:** 2026
**Last Updated:** April 21, 2026
**Version:** 1.0.0
