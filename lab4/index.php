<?php 
declare(strict_types=1);

// 1.2 Создание массива транзакций
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2026-02-15",
        "amount" => 34.57,
        "description" => "Dinner with friends",
        "merchant" => "Andy's pizza",
    ],
    [
        "id" => 3,
        "date" => "2021-05-04",
        "amount" => 88.41,
        "description" => "Payment for bicycle",
        "merchant" => "Sherif",
    ],
    [
        "id" => 4,
        "date" => "2025-02-16",
        "amount" => 14.89,
        "description" => "Payment for plachinta",
        "merchant" => "La placinta",
    ],
    [
        "id" => 5,
        "date" => "2022-08-09",
        "amount" => 75.00,
        "description" => "I don't know",
        "merchant" => "Dimon4ick Company",
    ],
    [
        "id" => 6,
        "date" => "2024-04-14",
        "amount" => 89.15,
        "description" => "Just take my money",
        "merchant" => "Lookhed Martin",
    ],
];

// 1.4 Реализация функций
/**
 * Ищет транзакцию по названию организации с помощью фильтра
 * 
 * @param string $merchant
 * @return array|null
 */
function findTransactionByMerchant(string $merchant): ?array {
    global $transactions;
    
    $result = array_filter($transactions, function(array $tr) use ($merchant): bool {
        return $tr["merchant"] === $merchant;
    });

    if(count($result) === 0) {
        return null;
    }
    
    return array_values($result)[0];
}

/**
 * Вычисляет общую сумму всех транзакций.
 * 
 * @param array $transactions Массив транзакций
 * @return float  Общая сумма
 */
function calculateTotalAmount(array $transactions): float {
    $total = 0.0;

    foreach ($transactions as $tr) {
        $total += $tr["amount"];
    }

    return $total;

    // Второй вариант
    // return array_sum(array_column($transactions, "amount"));
}

/**
 * Возвращает количество дней между датой транзакции и текущим днём.
 *
 * @param string $date Дата транзакции в формате YYYY-MM-DD.
 * @return int Количество дней.
 */
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $now = new DateTime();

    $interval = $transactionDate->diff($now);
    return $interval->days;
}

/**
 * Ищет транзакцию по идентификатору 
 * 
 * @param int $id
 * @return array|null
 */
function findTransactionById(int $id): ?array {
    global $transactions;
    
    foreach($transactions as $tr) {
        if($id === $tr["id"]) {
            return $tr;
        }
    }

    return null;
}

/**
 * Ищет транзакцию по идентификатору с помощью array_filter()
 * 
 * @param int $id
 * @return array|null
 */
function findTransactionByIdFilter(int $id): ?array {
    global $transactions; 

    $result = array_filter($transactions, function (array $tr) use ($id): bool {
        return $tr["id"] === $id;
    });

    if(count($result) === 0) {
        return null;
    }
    
    return array_values($result)[0];
}
//var_dump(findTransactionByIdFilter(3));
//var_dump(findTransactionByIdFilter(99));

/**
 * Ищет транзакцию по части описания
 * 
 * @param string $descriptionPart - часть описания, которую нужно найти
 * @return array - массив найденных транзакций 
 */
function findTransactionByDescription(string $descriptionPart): array {
    global $transactions;

    $result = array_filter($transactions, function(array $tr) use ($descriptionPart): bool {
        return str_contains($tr["description"], $descriptionPart);
    });

    return array_values($result);
}
//print_r(findTransactionByDescription("Payment"));   
//print_r(findTransactionByDescription("friends"));  
//print_r(findTransactionByDescription("xyz")); 

/**
 * Добавляет новую транзакцию
 * 
 * @param int    $id
 * @param string $date
 * @param float  $amount
 * @param string $description
 * @param string $merchant
 * @return void
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void {
    global $transactions;
    
    $transactions[] = [
            "id" => $id,
            "date" => $date,
            "amount" => $amount,
            "description" => $description,
            "merchant" => $merchant,
        ];
    
}
addTransaction(7, "2025-06-10", 42.50, "Payment for coffee", "Coffee Shop");

// 2. Сортировка с usort()
$byDate = $transactions;
usort($byDate, function (array $a, array $b): int {
    return $a["date"] <=> $b["date"];
});

$byAmount = $transactions;
usort($byAmount, function (array $a, array $b): int {
    return $b["amount"] <=> $a["amount"];
});

//print_r(array_column($byDate, "id"));
//print_r(array_column($byAmount, "id"));



?>

<!-- 1.3 Вывод списка транзакций -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Транзакции</title>
</head>
<body>
    <table border = '1'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Merchant</th>
                <th>Days ago</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $tr): ?>
            <tr>
                <td><?= $tr["id"] ?></td>
                <td><?= htmlspecialchars($tr["date"]) ?></td>
                <td><?= number_format($tr["amount"], 2) ?></td>
                <td><?= htmlspecialchars($tr["description"]) ?> </td>
                <td><?= htmlspecialchars($tr["merchant"]) ?></td>
                <td><?= daysSinceTransaction($tr["date"]) ?></td>
            </tr>
        <?php endforeach; ?>    
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Итого: </td>
                <td><?= number_format(calculateTotalAmount($transactions), 2)?></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>