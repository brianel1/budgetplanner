# Requirements Document

## Introduction

BudgetPlanner is a mobile-view, portrait-only Personal Budget Planner system built using HTML, CSS, Bootstrap 5, JavaScript, PHP, and MySQL 8.0. The system opens directly as a mobile app interface inside the browser and provides full CRUD functionality across all core modules: users, categories, transactions, and budgets. The application follows a specific workflow: User Input → Validation → MySQL Operation → Processing (balance/calculation/categorization) → Response → Updated Mobile Display.

## Glossary

- **BudgetPlanner**: The Personal Budget Planner web application
- **User**: A registered individual who uses the system to manage personal finances
- **Category**: A custom classification for organizing transactions with a type (income or expense)
- **Transaction**: A financial record with amount, category, date, description, and type (income/expense)
- **Budget**: A monthly spending limit set per category for a specific month/year
- **Dashboard**: The main overview screen displaying recent transactions and budget status
- **CRUD**: Create, Read, Update, Delete operations using INSERT, UPDATE, DELETE, SELECT queries
- **Mobile Form**: Touch-optimized input forms designed for portrait mobile view
- **Budget Summary**: Calculated display showing total income, total expenses, and remaining balance
- **Processing**: System operations including automatic balance updates, category organization, real-time budget vs actual comparison, and transaction categorization

## Database Schema

### Users Table
- user_id (PK)
- username
- email
- password
- created_date

### Categories Table
- category_id (PK)
- user_id (FK)
- category_name
- type (income/expense)

### Transactions Table
- transaction_id (PK)
- user_id (FK)
- category_id (FK)
- amount
- transaction_date
- description
- type (income/expense)

### Budgets Table
- budget_id (PK)
- user_id (FK)
- category_id (FK)
- monthly_amount
- month_year

## Navigation Flow

Home Screen → Login → Dashboard → (Add Transaction | View History | Set Budget | Manage Categories)

## Requirements

### Requirement 1: User Management

**User Story:** As a user, I want to register, log in, manage my profile, and delete my account, so that I can securely access and control my budget planning experience.

#### Acceptance Criteria

1. WHEN a user submits valid registration data (username, email, password) through the mobile form THEN BudgetPlanner SHALL validate the input and execute an INSERT query to create a new user record in the Users table with created_date
2. WHEN a user submits a registration form with an existing email THEN BudgetPlanner SHALL reject the registration and display an error message on the mobile display
3. WHEN a user submits valid login credentials THEN BudgetPlanner SHALL execute a SELECT query to authenticate and redirect to the dashboard
4. WHEN a user submits invalid login credentials THEN BudgetPlanner SHALL reject the login attempt and display an error message
5. WHEN a user updates their profile information (username, email) THEN BudgetPlanner SHALL validate the input and execute an UPDATE query to modify the user record
6. WHEN a user deletes their account THEN BudgetPlanner SHALL execute DELETE queries to remove the user and all associated data (categories, transactions, budgets) from the database
7. WHEN a user clicks the logout button THEN BudgetPlanner SHALL terminate the session and redirect to the login page

### Requirement 2: Category Management (CRUD)

**User Story:** As a user, I want to create, view, modify, and remove custom spending categories, so that I can organize my transactions by user-defined categories.

#### Acceptance Criteria

1. WHEN a user submits a new category through the mobile form with category_name and type (income/expense) THEN BudgetPlanner SHALL validate the input, execute an INSERT query to create the category in the Categories table, and trigger category organization processing
2. WHEN a user views the categories page THEN BudgetPlanner SHALL execute a SELECT query and display all categories belonging to that user organized by type
3. WHEN a user updates an existing category (category_name or type) THEN BudgetPlanner SHALL validate the input, execute an UPDATE query to modify the category record, and trigger category organization processing
4. WHEN a user deletes a category THEN BudgetPlanner SHALL execute a DELETE query to remove the category from the Categories table
5. WHEN a user attempts to create a category with invalid data (empty category_name) THEN BudgetPlanner SHALL reject the submission and display a validation error on the mobile display

### Requirement 3: Transaction Management (CRUD)

**User Story:** As a user, I want to record, view, edit, and delete income and expense transactions with category, amount, date, and description, so that I can track all my financial activities accurately.

#### Acceptance Criteria

1. WHEN a user submits a new transaction through the mobile form with amount, category_id, transaction_date, description, and type (income/expense) THEN BudgetPlanner SHALL validate the input, execute an INSERT query to create the transaction in the Transactions table, trigger automatic balance updates and transaction categorization, and update the mobile display
2. WHEN a user views the transaction history THEN BudgetPlanner SHALL execute a SELECT query and display all transactions belonging to that user with category name, amount, date, description, and type
3. WHEN a user updates an existing transaction (amount, category, date, description, type) THEN BudgetPlanner SHALL validate the input, execute an UPDATE query to modify the transaction record, trigger automatic balance updates, and update the mobile display
4. WHEN a user deletes a transaction THEN BudgetPlanner SHALL execute a DELETE query to remove the transaction from the Transactions table, trigger automatic balance updates, and update the mobile display
5. WHEN a user submits a transaction with invalid data (empty amount, missing category) THEN BudgetPlanner SHALL reject the submission and display a validation error
6. WHEN transaction data changes THEN BudgetPlanner SHALL process the balance calculations and update the mobile display to reflect current database values

### Requirement 4: Budget Management (CRUD)

**User Story:** As a user, I want to set, view, adjust, and remove monthly budget limits for categories, so that I can control and plan my spending per category.

#### Acceptance Criteria

1. WHEN a user sets a budget through the mobile form with category_id, monthly_amount, and month_year THEN BudgetPlanner SHALL validate the input, execute an INSERT query to create a budget record in the Budgets table, trigger real-time budget vs actual comparison processing, and update the mobile display
2. WHEN a user views the budget page THEN BudgetPlanner SHALL execute a SELECT query and display all budget records for that user with category name, monthly_amount, month_year, and current spending comparison
3. WHEN a user adjusts an existing budget (monthly_amount) THEN BudgetPlanner SHALL validate the input, execute an UPDATE query to save the new limit value, trigger real-time budget vs actual comparison processing, and update the mobile display
4. WHEN a user removes a budget THEN BudgetPlanner SHALL execute a DELETE query to remove the budget record from the Budgets table and update the mobile display
5. WHEN a user attempts to set a budget with invalid data (zero or negative monthly_amount) THEN BudgetPlanner SHALL reject the submission and display a validation error

### Requirement 5: Dashboard with Budget Summary

**User Story:** As a user, I want to see a dashboard with recent transactions and an automatically generated budget summary, so that I can understand my financial status at a glance.

#### Acceptance Criteria

1. WHEN a user views the dashboard THEN BudgetPlanner SHALL execute SELECT queries and display recent transactions with category, amount, date, and type
2. WHEN a user views the dashboard THEN BudgetPlanner SHALL process automatic balance updates and display total income from all income transactions
3. WHEN a user views the dashboard THEN BudgetPlanner SHALL process automatic balance updates and display total expenses from all expense transactions
4. WHEN a user views the dashboard THEN BudgetPlanner SHALL process real-time budget vs actual comparison and display remaining balance (budget limit minus total expenses)
5. WHEN transaction or budget data changes THEN BudgetPlanner SHALL trigger processing (balance/calculation/categorization) and recalculate the budget summary based on current database records
6. WHEN a user receives confirmation after any operation THEN BudgetPlanner SHALL display an updated summary on the dashboard

### Requirement 6: Report Module

**User Story:** As a user, I want to view spending summaries and category-based analysis, so that I can analyze my financial habits for academic presentation.

#### Acceptance Criteria

1. WHEN a user views the report page THEN BudgetPlanner SHALL execute SELECT queries, process the data, and display a spending summary
2. WHEN a user views the report page THEN BudgetPlanner SHALL process transaction categorization and display category-based expense analysis
3. WHEN a user selects a different time period THEN BudgetPlanner SHALL recalculate, process the data, and display updated report data

### Requirement 7: Mobile-First Portrait-Only Interface

**User Story:** As a user, I want a mobile app-like interface locked to vertical portrait layout with navigation to Add Transaction, View History, Set Budget, and Manage Categories, so that I can use the application as a mobile-first system in any browser.

#### Acceptance Criteria

1. WHEN the application loads THEN BudgetPlanner SHALL render in a portrait-only mobile view layout using Bootstrap 5
2. WHEN a user interacts with the interface THEN BudgetPlanner SHALL provide touch-optimized mobile forms with large buttons and card layouts
3. WHEN the viewport width changes THEN BudgetPlanner SHALL maintain the vertical mobile-centered layout without horizontal expansion
4. WHEN a user navigates from the dashboard THEN BudgetPlanner SHALL provide access to Add Transaction, View History, Set Budget, and Manage Categories screens
5. WHEN displaying system responses THEN BudgetPlanner SHALL update the mobile display following the complete workflow cycle

### Requirement 8: Data Validation and MySQL Operations

**User Story:** As a user, I want all my data validated before processing, so that the system maintains data integrity and follows the proper workflow.

#### Acceptance Criteria

1. WHEN any form is submitted THEN BudgetPlanner SHALL validate all input data for required fields and correct formats before executing MySQL queries
2. WHEN validation passes THEN BudgetPlanner SHALL execute the appropriate MySQL query (INSERT, UPDATE, DELETE, or SELECT)
3. WHEN a MySQL operation completes THEN BudgetPlanner SHALL trigger processing (automatic balance updates, category organization, real-time budget vs actual comparison, transaction categorization) as applicable
4. WHEN processing completes THEN BudgetPlanner SHALL generate a system response and update the mobile display with confirmation and updated summary
5. WHEN a MySQL operation fails THEN BudgetPlanner SHALL display an appropriate error message and maintain data integrity
6. WHEN a user logs in THEN BudgetPlanner SHALL retrieve and display only data belonging to that authenticated user from the Users, Categories, Transactions, and Budgets tables
