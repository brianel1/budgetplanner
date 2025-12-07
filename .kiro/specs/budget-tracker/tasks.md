# Implementation Plan

- [x] 1. Set up project structure and database





  - [x] 1.1 Create project folders and files


    - Create folders: `css/`, `js/`, `includes/`
    - Create database connection file `includes/db.php`
    - _Requirements: 7.1_
  - [x] 1.2 Create database schema


    - Create `database.sql` with CREATE TABLE statements for users, categories, transactions, budgets
    - Include foreign key constraints
    - _Requirements: 8.2_

  - [x] 1.3 Create base layout template

    - Create `includes/header.php` with Bootstrap 5 CDN links and mobile viewport meta
    - Create `includes/footer.php` with closing tags and JS
    - _Requirements: 7.1, 7.2_

- [x] 2. Implement User Authentication





  - [x] 2.1 Create registration page


    - Create `register.php` with form for username, email, password
    - Add form validation and INSERT query to users table
    - Hash password using password_hash()
    - _Requirements: 1.1, 1.2_
  - [x] 2.2 Create login page


    - Create `index.php` (login page) with email and password form
    - Add SELECT query to verify credentials using password_verify()
    - Start PHP session on successful login
    - _Requirements: 1.3, 1.4_
  - [x] 2.3 Create logout functionality


    - Create `logout.php` to destroy session and redirect to login
    - _Requirements: 1.7_
  - [x] 2.4 Create session check include


    - Create `includes/session.php` to check if user is logged in
    - Redirect to login if not authenticated
    - _Requirements: 8.6_

- [x] 3. Implement Dashboard





  - [x] 3.1 Create dashboard page


    - Create `dashboard.php` with welcome message and summary cards
    - Display total income, total expenses, remaining balance using SELECT SUM queries
    - Show recent transactions list
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

  - [x] 3.2 Add navigation menu

    - Add navigation links to Categories, Transactions, Budgets, Reports, Profile
    - Use Bootstrap navbar or bottom navigation for mobile
    - _Requirements: 7.4_



- [x] 4. Implement Category Management



  - [x] 4.1 Create categories page


    - Create `categories.php` with list of user's categories
    - Display category name and type (income/expense)
    - Add Edit and Delete buttons for each category
    - _Requirements: 2.2_
  - [x] 4.2 Create add category functionality


    - Create `add_category.php` with form for category_name and type
    - Add validation and INSERT query
    - _Requirements: 2.1, 2.5_
  - [x] 4.3 Create edit category functionality


    - Create `edit_category.php` with pre-filled form
    - Add UPDATE query to save changes
    - _Requirements: 2.3_
  - [x] 4.4 Create delete category functionality


    - Add DELETE query in `delete_category.php`
    - Redirect back to categories list
    - _Requirements: 2.4_

- [x] 5. Implement Transaction Management






  - [x] 5.1 Create transactions page

    - Create `transactions.php` with list of user's transactions
    - Display amount, category, date, description, type
    - Add Edit and Delete buttons
    - _Requirements: 3.2_

  - [x] 5.2 Create add transaction functionality

    - Create `add_transaction.php` with form for amount, category (dropdown), date, description, type
    - Add validation and INSERT query
    - _Requirements: 3.1, 3.5_

  - [x] 5.3 Create edit transaction functionality

    - Create `edit_transaction.php` with pre-filled form
    - Add UPDATE query to save changes
    - _Requirements: 3.3_

  - [x] 5.4 Create delete transaction functionality

    - Add DELETE query in `delete_transaction.php`
    - Redirect back to transactions list
    - _Requirements: 3.4_

- [x] 6. Implement Budget Management





  - [x] 6.1 Create budgets page


    - Create `budgets.php` with list of user's budgets
    - Display category, monthly amount, month/year, and spent amount comparison
    - Add Edit and Delete buttons
    - _Requirements: 4.2_
  - [x] 6.2 Create add budget functionality


    - Create `add_budget.php` with form for category (dropdown), monthly_amount, month_year
    - Add validation and INSERT query
    - _Requirements: 4.1, 4.5_

  - [x] 6.3 Create edit budget functionality

    - Create `edit_budget.php` with pre-filled form
    - Add UPDATE query to save changes
    - _Requirements: 4.3_

  - [x] 6.4 Create delete budget functionality

    - Add DELETE query in `delete_budget.php`
    - Redirect back to budgets list
    - _Requirements: 4.4_

- [x] 7. Implement Reports Page





  - [x] 7.1 Create reports page


    - Create `reports.php` with spending summary
    - Add month/year selector for filtering
    - Display total income, total expenses for selected period
    - _Requirements: 6.1, 6.3_

  - [x] 7.2 Add category breakdown

    - Display expenses grouped by category with totals
    - Use SELECT with GROUP BY category_id
    - _Requirements: 6.2_

- [x] 8. Implement Profile Management





  - [x] 8.1 Create profile page


    - Create `profile.php` with form to update username and email
    - Add UPDATE query to save changes
    - _Requirements: 1.5_

  - [x] 8.2 Add account deletion

    - Add delete account button with confirmation
    - DELETE user and all associated data (cascading)
    - _Requirements: 1.6_

- [x] 9. Apply Mobile-First Styling





  - [x] 9.1 Create mobile CSS


    - Create `css/style.css` with portrait-only mobile layout
    - Set max-width container (e.g., 480px) centered on screen
    - Style large touch-friendly buttons
    - _Requirements: 7.1, 7.2, 7.3_
  - [x] 9.2 Style all pages with Bootstrap cards


    - Apply Bootstrap card components for data display
    - Use Bootstrap form classes for inputs
    - Ensure consistent mobile look across all pages
    - _Requirements: 7.2_

- [-] 10. Final testing and cleanup



  - [ ] 10.1 Test all CRUD operations


    - Verify add, edit, delete works for categories, transactions, budgets
    - Test user registration, login, logout, profile update
    - _Requirements: 1.1-1.7, 2.1-2.5, 3.1-3.6, 4.1-4.5_
  - [ ] 10.2 Test dashboard calculations
    - Verify total income, expenses, and remaining balance are correct
    - Test budget vs actual comparison
    - _Requirements: 5.2, 5.3, 5.4, 5.5_
  - [ ] 10.3 Test data isolation
    - Verify users can only see their own data
    - _Requirements: 8.6_
