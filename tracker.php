<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'database.php';

$db = new Database();
$pdo = $db->getPdo();

// Function to show the menu
 function showMenu() {
    echo "\nExpense Tracker\n";
    echo "1. Add expense\n";
    echo "2. View expenses\n";
    echo "3. Delete expense\n";
    echo "4. Update expense\n";
    echo "5. Summarize expenses\n";
    echo "6. Summary by month\n";
    echo "7. Exit\n";
    echo "Enter your choice: ";
 }

 // Function to add an expense
 function addExpense($pdo) {
    echo "Enter description: ";
    $description = trim(fgets(STDIN));
    echo "Description entered succesfully\n";

    echo "Enter amount: ";
    $amount = floatval(trim(fgets(STDIN)));
    echo "Amount entered succesfully\n";

    echo "Enter category: ";
    $category = trim(fgets(STDIN));
    echo "Category entered succesfully\n";

    $date = date('Y-m-d');
    echo "Date entered succesfully\n";

    echo "Getting ready to insert data\n";
    $stmt = $pdo->prepare("INSERT INTO expenses (description, amount, category, date) VALUES (?, ?, ?, ?)");
    echo "Data prepared\n";
    $stmt->execute([$description, $amount, $category, $date]);
    echo "Data inserted succesfully\n";

    echo "Expense added successfully\n";
 }

 // Function to view expenses
 function viewExpenses($pdo) {
    $stmt = $pdo->query("SELECT * FROM expenses ORDER BY date DESC");
    $expenses = $stmt->fetchALL(PDO::FETCH_ASSOC);

    if (count($expenses) === 0) {
        echo "No expenses found.\n";
        return;
    }

    echo "\nID | Description | Amount | Category | Date\n";
    echo "--------------------------------------------\n";
    foreach ($expenses as $expense) {
        echo "{$expense['id']} | {$expense['description']} | {$expense['amount']} | {$expense['category']} | {$expense['date']}\n";
    }
}

// Function to delete an expense
function deleteExpense($pdo) {
    echo "Enter the ID of the expense you want to delete: ";
    $id = intval(trim(fgets(STDIN)));

    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->execute([$id]);

    echo "Expense deleted successfully\n";
    
}

function updateExpense($pdo) {
    echo "Enter the ID of the expense you would like to update: ";
    $id = intval(fgets(STDIN));

    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
    $stmt->execute([$id]);

    echo "What would you like to update?\n";
    echo "1. Description\n";
    echo "2. Amount\n";
    echo "3. Category\n";
    echo "4. Exit\n";
    echo "Enter your choice: ";
    $choice = intval(trim(fgets(STDIN)));

    switch($choice) {
        case 1:
            echo "Enter new description: ";
            $description = trim(fgets(STDIN));
            $stmt = $pdo->prepare("UPDATE expenses SET description = ? WHERE id = ?");
            $stmt->execute([$description, $id]);
            echo "Description updated successfully\n";
            break;
        case 2:
            echo "Enter Amount: ";
            $amount = intval(trim(fgets(STDIN)));
            $stmt = $pdo->prepare("UPDATE expenses SET amount = ? WHERE id = ?");
            $stmt->execute([$amount, $id]);
            echo "Amount updated successfully\n";
            break;
        case 3:
            echo "Enter new category: ";
            $category = trim(fgets(STDIN));
            $stmt = $pdo->prepare("UPDATE expenses SET category = ? WHERE id = ?");
            $pdo->execute([$category, $id]);
            echo "Category updated successfully\n";
            break;
        case 4:
            echo "Exiting...\n";
            exit;
        default:
            echo "Invalid choice. Please enter a valid choice.\n";
}
}

function summarizeExpenses($pdo) {
    $stmt = $pdo->query("SELECT category, SUM(amount) as total FROM expenses GROUP BY category");
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($expenses) === 0) {
        echo "No expenses found.\n";
        return;
    }

    echo "\nCategory | Total\n";
    echo "----------------\n";
    foreach ($expenses as $expense) {
        echo "{$expense['category']} | {$expense['total']}\n";
    }

    $stmt = $pdo->query("SELECT SUM(amount) as total FROM expenses");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total expenses: {$total['total']}\n";

}

function monthByMonth($pdo) {
    $stmt = $pdo->query("SELECT strftime('%m', date) as month, SUM(amount) as total FROM expenses GROUP BY month ORDER BY month");
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($expenses) === 0) {
        echo "No expenses found.\n";
        return;
    }

    echo "\nMonth | Total\n";
    echo "----------------\n";
    foreach ($expenses as $expense) {
        echo "{$expense['month']} | {$expense['total']}\n";
    }
}

// Main loop menu to interact with the user
while (true) {
    showMenu();
    $choice = intval(trim(fgets(STDIN)));


    switch($choice) {
        case 1:
            addExpense($pdo);
            break;
        case 2:
            viewExpenses($pdo);
            break;
        case 3:
            deleteExpense($pdo);
            break;
        case 4:
            updateExpense($pdo);
            break;
        case 5:
            summarizeExpenses($pdo);
            break;
        case 6:
            monthByMonth($pdo);
            break;
        case 7:
            echo "Exiting...\n";
            exit;
        default:
            echo "Invalid choice. Please enter a valid choice.\n";
    }

}