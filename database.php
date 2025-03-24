<?php
class Database {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('sqlite:expenses.db');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->initializeTable();
    }

    private function initializeTable(){
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            description TEXT,
            amount REAL,
            category TEXT,
            date TEXT
        )');

        // Check if the "category" column exists
        $check = $this->pdo->query('PRAGMA table_info(expenses)');
        $columns = $check->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'name');

        // If the "category" column does not exist, add it
        if (!in_array('category', $columnNames)) {
            $this->pdo->exec('ALTER TABLE expenses ADD COLUMN category TEXT');
        }
    }

    public function getPdo() {
        return $this->pdo;      
    }
}
