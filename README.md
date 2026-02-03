# 🎬 Web Shopping Application System - DVD Store
<p align="center">
  <img src="./Heading.png" alt="Dashbaord Banner" />
</p>

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql)
![HTML5](https://img.shields.io/badge/HTML5-E34C26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-brightgreen?style=flat-square)

A full-featured e-commerce web application for browsing, searching, and purchasing DVDs online. Built with PHP, MySQL, HTML5, CSS3, and JavaScript, featuring user authentication, shopping cart management, and order processing.

## ✨ Features

### 🔐 User Authentication
- **Customer Registration**: New customers can sign up with personal information
- **Secure Login**: Password hashing with PHP's `password_hash()` function
- **Session Management**: Secure session-based authentication
- **Profile Management**: Update customer information and passwords

### 🛍️ Shopping Experience
- **Product Search**: Search DVDs by title with real-time filtering
- **Product Catalog**: Browse all available DVDs with pricing and stock information
- **Shopping Cart**: Add, modify, and remove items from cart
- **Stock Management**: Real-time inventory tracking
- **Cart Persistence**: Cart data saved in database for future sessions

### 📦 Order Management
- **Checkout Process**: Streamlined multi-step checkout
- **Order Confirmation**: Receipt generation with order details
- **Order History**: View all previous orders and their status
- **Shipping Status**: Track order shipping status
- **Order Details**: View itemized order information

### 💳 Payment & Logistics
- **Total Cost Calculation**: Automatic subtotal and total calculations
- **Shipping Address**: Auto-populated from customer profile
- **Order Receipts**: Printable invoice generation
- **Inventory Updates**: Stock automatically updated after checkout

## 🗂️ Project Structure

```
DVD Website Project/
├── index.html                 # Login landing page
├── new_customer.html          # Customer registration form
├── search_products.html       # Product search interface
├── update_profile.html        # Profile update form
├── customer_menu.php          # Main customer dashboard
├── search_products.php        # Product search backend
├── add_to_cart.php           # Add to cart logic
├── view_cart.php             # Shopping cart display
├── manage_cart.php           # Cart modification logic
├── checkout.php              # Checkout confirmation page
├── process_checkout.php      # Process order & payment
├── receipt.php               # Order receipt display
├── check_order_status.php    # Order history view
├── view_order_details.php    # Detailed order information
├── update_profile.php        # Profile update backend
├── process_login.php         # Login authentication
├── process_signup.php        # Registration processing
├── logout.php                # Logout & cart options
├── save_and_logout.php       # Save cart & logout
├── empty_and_logout.php      # Clear cart & logout
├── customer_menu.php         # Customer dashboard
├── .hintrc                   # WebHint configuration
└── sql/
    ├── dvd.sql               # DVD table schema
    ├── dvd_data.sql          # Sample DVD data
    ├── customer_table.sql    # Customer table schema
    ├── products.sql          # Products table schema
    ├── orders.sql            # Orders table schema
    ├── orderdetails.sql      # Order details schema
    ├── order_items.sql       # Order items schema
    ├── carts.sql             # Shopping cart schema
    └── insertion.sql         # Sample product data
```

## 🛠️ Installation & Setup

### Prerequisites
- **PHP** 7.4 or higher
- **MySQL** 8.0 or higher
- **Web Server** (Apache, Nginx, or similar)
- **Web Browser** (Chrome, Firefox, Safari, Edge)

### Step 1: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE shoppingline;
USE shoppingline;
```

2. Run the SQL schema files in the following order:
```bash
# From your terminal or MySQL client
mysql -u root -p shoppingline < sql/dvd.sql
mysql -u root -p shoppingline < sql/customer_table.sql
mysql -u root -p shoppingline < sql/carts.sql
mysql -u root -p shoppingline < sql/orders.sql
mysql -u root -p shoppingline < sql/orderdetails.sql
mysql -u root -p shoppingline < sql/order_items.sql
mysql -u root -p shoppingline < sql/dvd_data.sql
mysql -u root -p shoppingline < sql/insertion.sql
```

### Step 2: Configure Database Connection

Update database credentials in all PHP files:
```php
$servername = "";
$username = "";           // Your MySQL username
$password = "SECRET";         // Your MySQL password
$dbname = "shoppingline";
$port = "";
```

Files to update:
- [process_login.php](process_login.php)
- [process_signup.php](process_signup.php)
- [update_profile.php](update_profile.php)
- [search_products.php](search_products.php)
- [add_to_cart.php](add_to_cart.php)
- [view_cart.php](view_cart.php)
- [manage_cart.php](manage_cart.php)
- [checkout.php](checkout.php)
- [process_checkout.php](process_checkout.php)
- [receipt.php](receipt.php)
- [check_order_status.php](check_order_status.php)
- [view_order_details.php](view_order_details.php)

### Step 3: Deploy Files

1. Copy all project files to your web server's root directory
2. Ensure proper file permissions (755 for directories, 644 for files)
3. Access the application at `http://localhost/` (or your server URL)

## 🚀 Usage Guide

### For New Customers
1. Click **"Sign In Here"** on the login page
2. Fill out registration form with your details
3. Receive your customer number
4. Log in with customer number and password

### For Existing Customers
1. Enter customer number and password
2. Browse DVDs using the search feature
3. Add items to cart
4. View and modify cart contents
5. Proceed to checkout
6. Receive order receipt

### Customer Dashboard Features
- **Search by Keyword**: Find DVDs by title
- **View/Edit Cart**: Manage shopping cart items
- **Update Profile**: Modify personal information
- **Check Order Status**: View order history
- **Check Out**: Process purchases
- **Logout**: Exit the system

## 📊 Database Schema

### DVD Table
```sql
dvd_id (INT) | title (VARCHAR) | genre (VARCHAR) | price (DECIMAL) | stock (INT)
```

### Customers Table
```sql
customer_id (INT) | name (VARCHAR) | street (VARCHAR) | city (VARCHAR) 
state (VARCHAR) | zip (VARCHAR) | phone (VARCHAR) | email (VARCHAR) | password (VARCHAR)
```

### Cart Table
```sql
customer_id (INT) | dvd_id (INT) | quantity (INT)
```

### Orders Table
```sql
ono (INT) | customer_id (INT) | order_date (DATE) 
shipping_status (VARCHAR) | total_amount (DECIMAL)
```

### Order Details Table
```sql
ono (INT) | dvd_id (INT) | quantity (INT) | price (DECIMAL)
```

## 🔒 Security Features

✅ **Password Hashing**: Uses PHP's `password_hash()` with PASSWORD_DEFAULT algorithm
✅ **SQL Injection Prevention**: Prepared statements with parameterized queries
✅ **Session Management**: Secure session-based authentication
✅ **Input Validation**: Server-side validation for all user inputs
✅ **XSS Protection**: HTML sanitization with `htmlspecialchars()`
✅ **CORS Headers**: Proper headers for cross-origin requests

## 🎨 UI/UX Features

- **Responsive Design**: Mobile-friendly layouts
- **Modern Styling**: CSS3 with gradient backgrounds and animations
- **Intuitive Navigation**: Easy-to-use menu system
- **Visual Feedback**: Status messages and alerts
- **Professional Design**: Clean, modern interface with consistent branding

## 🌟 Key Technologies

| Technology | Purpose |
|-----------|---------|
| **PHP 7.4+** | Server-side scripting |
| **MySQL 8.0** | Database management |
| **HTML5** | Markup structure |
| **CSS3** | Styling & animations |
| **JavaScript** | Client-side interactivity |
| **MySQLi** | Database connectivity |
| **PDO** | Alternative database abstraction |

## 📝 API Endpoints

### Authentication
- `POST /process_login.php` - User login
- `POST /process_signup.php` - New customer registration

### Products
- `GET /search_products.php` - Search DVDs
- `POST /add_to_cart.php` - Add item to cart

### Cart
- `GET /view_cart.php` - View shopping cart
- `POST /manage_cart.php` - Update/delete cart items

### Orders
- `POST /process_checkout.php` - Process order
- `GET /receipt.php` - View order receipt
- `GET /check_order_status.php` - View order history
- `GET /view_order_details.php` - View order details

### Profile
- `GET /update_profile.html` - Profile form
- `POST /update_profile.php` - Update profile information

## 🐛 Troubleshooting

### Database Connection Issues
- Verify MySQL is running
- Check database credentials
- Ensure database exists: `SHOW DATABASES;`
- Check user permissions: `SHOW GRANTS FOR 'root'@'localhost';`

### Login Problems
- Verify customer exists: `SELECT * FROM customers WHERE customer_id = ?;`
- Check password hash: `SELECT password FROM customers WHERE customer_id = ?;`
- Clear browser cookies and try again

### Cart Issues
- Check cart table: `SELECT * FROM cart WHERE customer_id = ?;`
- Verify stock availability: `SELECT stock FROM dvd WHERE dvd_id = ?;`
- Clear session and re-login

## 📋 Sample Test Data

The project includes sample DVDs:
- The Matrix (Sci-Fi) - $19.99
- Dune (Sci-Fi) - $24.99
- Inception (Sci-Fi) - $22.50
- The Godfather (Crime) - $29.99
- Pulp Fiction (Crime) - $18.75
- The Shawshank Redemption (Drama) - $15.00
- Forrest Gump (Drama) - $16.50
- Jurassic Park (Adventure) - $17.99
- Finding Nemo (Animation) - $14.99
- Toy Story (Animation) - $14.99

**Test Credentials**: Use any customer_id from the database with password matching their record.

## 🚦 Future Enhancements

- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] Email notifications for orders
- [ ] Advanced search filters (genre, price range, rating)
- [ ] Product ratings and reviews
- [ ] Wishlist functionality
- [ ] Discount codes and coupons
- [ ] Admin dashboard for inventory management
- [ ] Two-factor authentication
- [ ] Order tracking with notifications
- [ ] API documentation (Swagger/OpenAPI)

## 📄 License

This project is licensed under the **MIT License** - see the LICENSE file for details.

## 👨‍💻 Developer Notes

- Always use prepared statements to prevent SQL injection
- Hash passwords with `password_hash()` and verify with `password_verify()`
- Sanitize user input with `htmlspecialchars()`
- Close database connections properly
- Use `session_start()` at the beginning of protected pages
- Test all forms with both valid and invalid data
- Keep database credentials in a separate config file in production

## 📞 Support & Contact

For issues, questions, or contributions, please contact the development team or submit an issue through your project management system.

---

**Version**: 1.0.0  
**Last Updated**: January 2025  
**Status**: ✅ Active Development
