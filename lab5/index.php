<?php
declare(strict_types=1);

/**
 * Класс описывает одну банковскую транзакцию.
 */
class Transaction {
    // забабахали приватные поля
    private int $id;
    private DateTime $date;
    private float $amount;
    private string $description;
    private string $merchant;
    private string $category;

    // конструктор
    public function __construct(int $id, string $date, float $amount, string $description, string $merchant, string $category = "Прочее") {
        $this->id = $id;
        $this->date = new DateTime($date);
        $this->amount = $amount;
        $this->description = $description;
        $this->merchant = $merchant;
        $this->category = $category;
    }

    // теперь геттеры
    public function getId(): int {
        return $this->id;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getMerchant(): string {
        return $this->merchant;
    }

    public function getCategory(): string {
            return $this->category;
        }

    public function getDaysSinceTransaction(): int {
        $now = new DateTime();

        $diff = $this->date->diff($now);
        return $diff->days;
    }
}

//$t = new Transaction(1, "2026-10-01", 50.0, "Кофе", "Starbucks");
//echo $t->getDaysSinceTransaction();


/**
 * Контракт для любого хранилища банковских транзакций.
 */
interface TransactionStorageInterface {
    public function addTransaction(Transaction $transaction): void;
    public function removeTransactionById(int $id): void;
    public function getAllTransactions(): array;
    public function findById(int $id): ?Transaction;
}

/**
 * Хранит коллекцию транзакций в памяти (в массиве).
 * Отвечает только за хранение и базовые операции доступа.
 */
class TransactionRepository implements TransactionStorageInterface {
    private array $transactions = [];

    public function addTransaction(Transaction $tr): void {
        $this->transactions[] = $tr;
    }

    public function removeTransactionById(int $id): void {
        foreach($this->transactions as $key => $tr) {
            if($tr->getId() === $id) {
                unset($this->transactions[$key]);
                $this->transactions = array_values($this->transactions);
                return;
            }
        }
    }
    
    public function getAllTransactions(): array {
        return $this->transactions;
    }

    public function findById(int $id): ?Transaction {
        foreach($this->transactions as $tr) {
            if($tr->getId() === $id) {
                return $tr;
            }
        }
        return null;
    }
}

/* $repo = new TransactionRepository();
$repo->addTransaction(new Transaction(1, "2026-10-01", 50.0, "Кофе", "Starbucks"));
$repo->addTransaction(new Transaction(2, "2026-10-05", 120.5, "Продукты", "Linella"));

echo count($repo->getAllTransactions()) . "\n";

echo $repo->findById(2)->getMerchant() . "\n";
$repo->removeTransactionById(1);

echo count($repo->getAllTransactions()) . "\n";
var_dump($repo->findById(1));*/

/**
 * Выполняет бизнес-логику над транзакциями: расчёты и сортировку.
 * Сам транзакции не хранит, а получает их из хранилища.
 */
class TransactionManager {
    public function __construct(
        private TransactionStorageInterface $repository // TransactionRepository $repository
    ) {
    }

    public function calculateTotalAmount(): float {
        $sum = 0.0;
        foreach($this->repository->getAllTransactions() as $tr) {
            $sum += $tr->getAmount();
        }
        return $sum;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float {
        $fDate = new DateTime($startDate);
        $sDate = new DateTime($endDate);
        $sum = 0.0;

        foreach($this->repository->getAllTransactions() as $tr) {
            if($tr->getDate() >= $fDate && $tr->getDate() <= $sDate) {
                $sum += $tr->getAmount();
            }
        }        
        return $sum;
    }

    public function countTransactionsByMerchant(string $merchant): int {
        $merchantCount = 0;
        
        foreach($this->repository->getAllTransactions() as $tr) {
            if($tr->getMerchant() === $merchant) {
                $merchantCount++;
            }
        }
        return $merchantCount;
    }

    public function sortTransactionsByDate(): array {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b): int {
            return $a->getDate() <=> $b->getDate();
        });
            
        return $transactions;
    }

    public function sortTransactionsByAmountDesc(): array {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b): int {
            return $b->getAmount() <=> $a->getAmount();
        });

        return $transactions;
    }
    
}
/* $repo = new TransactionRepository();
$repo->addTransaction(new Transaction(3, "2026-10-10", 30.0, "Кофе", "Starbucks"));
$repo->addTransaction(new Transaction(1, "2026-10-01", 50.0, "Кофе", "Starbucks"));
$repo->addTransaction(new Transaction(2, "2026-10-05", 120.5, "Продукты", "Linella"));

$manager = new TransactionManager($repo);

echo $manager->calculateTotalAmount() . "\n";
echo $manager->calculateTotalAmountByDateRange("2026-10-01", "2026-10-05") . "\n";
echo $manager->countTransactionsByMerchant("Starbucks") . "\n";

foreach($manager->sortTransactionByDate() as $tr) {
    echo $tr->getId() . " ";
}

foreach($manager->sortTransactionsByAmountDesc() as $tr) {
    echo $tr->getId() . " ";
}*/

/**
 * Формирует HTML-таблицу из списка транзакций.
 */
final class TransactionTableRenderer {

    public function render(array $transactions): string {
        $html = '<table border="1" cellpadding="5">';
        $html .= '<tr><th>ID</th><th>Дата</th><th>Сумма</th><th>Описание</th><th>Получатель</th><th>Категория</th><th>Дней назад</th>';
        $html .= '</tr>';

        foreach ($transactions as $tr) {
            $html .= '<tr>';
            $html .= '<td>' . $tr->getId() . '</td>';
            $html .= '<td>' . $tr->getDate()->format('d.m.Y') . '</td>';
            $html .= '<td>' . number_format($tr->getAmount(), 2, '.', ' ') . '</td>';
            $html .= '<td>' . htmlspecialchars($tr->getDescription()) . '</td>';
            $html .= '<td>' . htmlspecialchars($tr->getMerchant()) . '</td>';
            $html .= '<td>' . htmlspecialchars($tr->getCategory()) . '</td>';
            $html .= '<td>' . $tr->getDaysSinceTransaction() . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }
}
/* $repo = new TransactionRepository();
$repo->addTransaction(new Transaction(3, "2026-10-10", 30.0, "Кофе", "Starbucks", "Кафе"));
$repo->addTransaction(new Transaction(1, "2026-10-01", 50.0, "Кофе", "Starbucks", "Кафе"));
$repo->addTransaction(new Transaction(2, "2026-10-05", 120.5, "Продукты", "Linella", "Магазин"));

$manager = new TransactionManager($repo);
$renderer = new TransactionTableRenderer();

echo $renderer->render($manager->sortTransactionsByDate());*/


// Задание 6
$repo = new TransactionRepository();
$repo->addTransaction(new Transaction(1, "2026-10-01", 50.0, "Кофе и круассан", "Starbucks", "Кафе"));
$repo->addTransaction(new Transaction(2, "2026-09-15", 320.5, "Продукты на неделю", "Linella", "Магазин"));
$repo->addTransaction(new Transaction(3, "2026-10-12", 150.0, "Пополнение мобильной связи", "Orange Moldova", "Связь"));
$repo->addTransaction(new Transaction(4, "2026-09-28", 210.0, "Доставка пиццы", "Glovo", "Еда"));
$repo->addTransaction(new Transaction(5, "2026-10-05", 189.9, "Подписка на сериалы", "Netflix", "Развлечения"));
$repo->addTransaction(new Transaction(6, "2026-09-20", 640.0, "Бензин", "Petrom", "Транспорт"));
$repo->addTransaction(new Transaction(7, "2026-10-18", 95.0, "Лекарства", "Farmacia Familiei", "Здоровье"));
$repo->addTransaction(new Transaction(8, "2026-10-22", 870.0, "Покупки в гипермаркете", "Kaufland", "Магазин"));
$repo->addTransaction(new Transaction(9, "2026-09-10", 79.0, "Музыкальная подписка", "Spotify", "Развлечения"));
$repo->addTransaction(new Transaction(10, "2026-10-25", 1250.0, "Спортивная форма", "Decathlon", "Спорт"));

$manager = new TransactionManager($repo);
$renderer = new TransactionTableRenderer();

echo $renderer->render($manager->sortTransactionsByDate());


