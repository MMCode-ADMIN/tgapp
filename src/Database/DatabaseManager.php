<?php


namespace WebCrawler\Database;

use PDO;
use PDOException;
use WebCrawler\Model\Product;

class DatabaseManager
{
    const string DB_PATH = 'products.sqlite';

    private PDO $db;
    private string $dbPath;

    public function __construct() {
        $this->dbPath = self::DB_PATH;
        $this->initialize();
    }

    /**
     * @return void
     */
    private function initialize(): void {
        try {
            $this->db = new PDO("sqlite:{$this->dbPath}");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch (PDOException $e) {
            throw new \RuntimeException("Database initialization failed: " . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    private function createTables(): void {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            url TEXT NOT NULL,
            title TEXT,
            price INTEGER,
            currency TEXT,
            availability TEXT,
            scraped_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        $this->db->exec($sql);
    }

    /**
     * @param Product $product
     * @return void
     */
    public function saveProduct(Product $product): void
    {
        $sql = "INSERT INTO products (url, title, price, currency, availability, scraped_at)
                VALUES (:url, :title, :price, :currency, :availability, :scraped_at)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':url' => $product->getUrl(),
            ':title' => $product->getTitle(),
            ':price' => $product->getMoney()->getAmount(),
            ':currency' => $product->getMoney()->getCurrency()->value,
            ':availability' => $product->getAvailability(),
            ':scraped_at' => $product->getScrapedAt()->format('Y-m-d H:i:s')
        ]);
    }
}