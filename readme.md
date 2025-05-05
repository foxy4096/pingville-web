# Pingville Web


Pingville Web is the companion website for the game PingVille. It manages user accounts, authentication, tokens, achievements, and other related functionalities to enhance the gaming experience and provide a seamless integration with the game.

## Features

- User authentication and account management
- Token generation and validation
- Achievement tracking and management

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/foxy4096/pingville-web.git
    ```
2. Navigate to the project directory:
    ```bash
    cd pingville-web
    ```
3. Create a database for the application.
4. Create a `_secrets.php` file in the project root to store your environment variables, such as database credentials:
    ```php
    <?php
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "pingville_db";
    ?>
    ```
5. Set up your PHP environment (e.g., using Laragon, XAMPP, or similar).

## Usage

Start the development server:
```bash
php -S localhost:8000
```

Open your web browser and navigate to `http://localhost:8000` to access the application.
