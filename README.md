# TGN Project

## Prerequisites

- PHP 8.1 or higher
- Node.js 14 or higher
- Composer
- npm

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/MMCode-ADMIN/tgapp
   cd tgapp
   ```

2. Install dependencies:
   ```sh
   composer install
   ```

3. Install Node.js dependencies:
   ```sh
   npm install
   ```

## Running the Project

Start the crawler:
```sh
php app.php
```

## The crawler will:

- Load URLs from urls.txt
- Fetch each page using Puppeteer
- Parse product information (title, price, availability)
- Store results in SQLite database (products.sqlite)

