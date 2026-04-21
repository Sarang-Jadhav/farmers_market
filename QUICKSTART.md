# 🚀 QUICK START GUIDE - Farmers Market Application

## Step-by-Step Setup (5 Minutes)

### ✅ Step 1: Copy Project to XAMPP
```
Copy farmers_market folder to: C:\xampp\htdocs\
```

### ✅ Step 2: Create Database

**Method A: Using phpMyAdmin (Easiest)**
1. Open: `http://localhost/phpmyadmin`
2. Click "New" on left sidebar
3. Database name: `farmers_market`
4. Click "Create"
5. Select `farmers_market` database
6. Click "Import" tab
7. Click "Browse" and select `database.sql` from your project
8. Click "Go"

**Method B: Using MySQL Command Line**
```bash
mysql -u root -p < C:\path\to\farmers_market\database.sql
```

### ✅ Step 3: Start XAMPP Services
- Open XAMPP Control Panel
- Click "Start" next to Apache
- Click "Start" next to MySQL

### ✅ Step 4: Access Application
Open in your browser:
```
http://localhost/farmers_market/
```

---

## 🔑 Demo Login Credentials

### Farmer Account
- **Email:** farmer@test.com
- **Password:** password123

### Customer Account
- **Email:** customer@test.com
- **Password:** password123

---

## 📋 What Each Page Does

### Public Pages (No Login Required)
- `/index.php` - Home page
- `/login.php` - Login page
- `/register.php` - Registration page

### Farmer Pages (After Login as Farmer)
- `/farmer/dashboard.php` - Dashboard with stats
- `/farmer/add_product.php` - Add new products
- `/farmer/manage_products.php` - Edit/delete products
- `/farmer/view_orders.php` - View customer orders

### Customer Pages (After Login as Customer)
- `/user/dashboard.php` - Dashboard with stats
- `/user/view_products.php` - Browse & add to cart
- `/user/cart.php` - Manage shopping cart
- `/user/checkout.php` - Place order
- `/user/orders.php` - View order history

### All Users
- `/profile.php` - Edit profile & change password
- `/logout.php` - Logout

---

## 🧪 Test Scenarios

### Scenario 1: Farmer Adding Products
1. Login as farmer@test.com
2. Click "Add Product"
3. Fill in: Name, Description, Price, Quantity
4. Click "Add Product"
5. Go to "Manage Products" to see it listed

### Scenario 2: Customer Ordering
1. Login as customer@test.com
2. Click "Shop"
3. Enter quantity for a product
4. Click "Add"
5. Go to "Cart" to see items
6. Click "Checkout"
7. Click "Place Order"
8. Go to "Orders" to see order history

### Scenario 3: Farmer Viewing Orders
1. Login as farmer@test.com
2. Click "Orders"
3. Click "View Items" to see what was ordered
4. See customer name and order details

---

## ⚙️ Customization

### Change Database Credentials
Edit `/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');  // If you set a password
define('DB_NAME', 'farmers_market');
```

### Change Colors
Edit `/includes/header.php` in the `<style>` section:
```css
--primary-color: #2c5f2d;      /* Change main green color */
--secondary-color: #f4a642;    /* Change orange accent */
--danger-color: #dc3545;       /* Change red color */
--success-color: #28a745;      /* Change light green */
```

### Update Navbar Links
Edit `/includes/header.php` to modify navigation menu

---

## 🐛 Troubleshooting

**Q: Getting "Connection failed" error**
- A: Make sure MySQL is running in XAMPP
- Go to XAMPP Control Panel and click "Start" next to MySQL

**Q: Database doesn't exist**
- A: Follow Step 2 above to create the database

**Q: Can't login with demo accounts**
- A: Make sure you've imported the `database.sql` file
- The demo accounts are created by the SQL file

**Q: Pages showing blank or 404**
- A: Check if the file path is correct
- Make sure XAMPP is running Apache

**Q: Styles/CSS not loading**
- A: Check browser console (F12) for errors
- May need to clear browser cache (Ctrl+Shift+Delete)

---

## 📁 Complete File Structure

```
farmers_market/
├── db.php                          ✓
├── index.php                       ✓
├── login.php                       ✓
├── register.php                    ✓
├── logout.php                      ✓
├── profile.php                     ✓
├── database.sql                    ✓
├── README.md                       ✓
├── QUICKSTART.md                   ✓
│
├── farmer/
│   ├── dashboard.php              ✓
│   ├── add_product.php            ✓
│   ├── manage_products.php        ✓
│   └── view_orders.php            ✓
│
├── user/
│   ├── dashboard.php              ✓
│   ├── view_products.php          ✓
│   ├── cart.php                   ✓
│   ├── checkout.php               ✓
│   ├── orders.php                 ✓
│   └── get_cart_count.php         ✓
│
├── includes/
│   ├── header.php                 ✓
│   ├── footer.php                 ✓
│   └── auth.php                   ✓
│
└── assets/
    └── (for images, CSS, JS - optional)
```

---

## 🎯 Key Features Implemented

✅ Role-based authentication (Farmer & Customer)
✅ Session-based authorization
✅ Separate dashboards for each role
✅ Product management (add, edit, delete)
✅ Multi-item shopping cart
✅ Dynamic quantity management
✅ Order processing with transactions
✅ Order history tracking
✅ Bootstrap responsive design
✅ User profile management
✅ Password hashing & security
✅ Database relationships & constraints

---

## 🚀 Next Steps

1. Test the application with demo accounts
2. Add more products
3. Create multiple user accounts
4. Place orders and verify inventory
5. Customize colors and branding
6. Add your own products and test the workflow

---

**Need Help?** Check the README.md file for detailed documentation.

**Last Updated:** April 21, 2026
