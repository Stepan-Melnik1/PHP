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

    public function toArray(): array {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'amount' => $this->amount,
            'description' => $this->description,
            'merchant' => $this->merchant,
            'category' => $this->category,
        ];
    }

    public static function fromArray(array $data): self {
        return new self($data['id'], $data['date'], $data['amount'], $data['description'], $data['merchant'], $data['category']);
    }
}

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

/**
 * Хранилище транзакций в JSON-файле: данные переживают перезапуск программы.
 */
class JsonFileTransactionRepository implements TransactionStorageInterface {
    public function __construct(
        private string $filePath
    ) {
    }
 
    private function load(): array {
        if (!file_exists($this->filePath)) {
            return [];
        }
 
        $rows = json_decode(file_get_contents($this->filePath), true);
        if (!is_array($rows)) {
            return [];
        }
 
        $transactions = [];
        foreach ($rows as $row) {
            $transactions[] = Transaction::fromArray($row);
        }
        return $transactions;
    }
 
    private function save(array $transactions): void {
        $rows = [];
        foreach ($transactions as $tr) {
            $rows[] = $tr->toArray();
        }
        file_put_contents($this->filePath, json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function addTransaction(Transaction $transaction): void {
        $transactions = $this->load();
        $transactions[] = $transaction;
        $this->save($transactions);
    }
 
    public function removeTransactionById(int $id): void {
        $transactions = $this->load();
        foreach ($transactions as $key => $tr) {
            if ($tr->getId() === $id) {
                unset($transactions[$key]);
                break;
            }
        }
        $this->save($transactions);
    }

    public function getAllTransactions(): array {
        return $this->load();
    }
 
    public function findById(int $id): ?Transaction {
        foreach ($this->load() as $tr) {
            if ($tr->getId() === $id) {
                return $tr;
            }
        }
        return null;
    }
}
 


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

const DATA_FILE = __DIR__ . '/transactions.json';
 
$repo = new JsonFileTransactionRepository(DATA_FILE);
 
// Если файла ещё нет, заполняем его начальными 10 транзакциями.
if (!file_exists(DATA_FILE)) {
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
}
 
$manager = new TransactionManager($repo);
$renderer = new TransactionTableRenderer();
 
// Добавление, удаление, сброс (форма отправляет данные методом POST).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
 
    if ($action === 'add') {
        $maxId = 0;
        foreach ($repo->getAllTransactions() as $tr) {
            $maxId = max($maxId, $tr->getId());
        }
        try {
            $repo->addTransaction(new Transaction(
                $maxId + 1,
                (string)($_POST['date'] ?? ''),
                (float)($_POST['amount'] ?? 0),
                trim((string)($_POST['description'] ?? '')),
                trim((string)($_POST['merchant'] ?? '')),
                trim((string)($_POST['category'] ?? '')) ?: 'Прочее'
            ));
        } catch (Exception $e) {
            // неверная дата: транзакцию просто не добавляем
        }
    } elseif ($action === 'delete') {
        $repo->removeTransactionById((int)($_POST['id'] ?? 0));
    } elseif ($action === 'reset') {
        if (file_exists(DATA_FILE)) {
            unlink(DATA_FILE);
        }
    }
 
    // После изменения перезагружаем страницу, чтобы F5 не отправлял форму повторно.
    header('Location: index.php');
    exit;
}
 
// Поиск и вычисления (формы отправляют данные методом GET).
$searchId = (string)($_GET['search_id'] ?? '');
$from = (string)($_GET['from'] ?? '');
$to = (string)($_GET['to'] ?? '');
$merchant = (string)($_GET['merchant'] ?? '');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Банковские транзакции</title>
</head>
<body>
    <h1>Банковские транзакции</h1>
 
    <h2>Добавить транзакцию</h2>
    <form method="post">
        <input type="hidden" name="action" value="add">
        Дата: <input type="date" name="date" required>
        Сумма: <input type="number" name="amount" step="0.01" min="0.01" required>
        Описание: <input type="text" name="description" required>
        Получатель: <input type="text" name="merchant" required>
        Категория: <input type="text" name="category">
        <button type="submit">Добавить</button>
    </form>
 
    <h2>Удалить транзакцию</h2>
    <form method="post">
        <input type="hidden" name="action" value="delete">
        ID: <input type="number" name="id" min="1" required>
        <button type="submit">Удалить</button>
    </form>
 
    <form method="post">
        <input type="hidden" name="action" value="reset">
        <button type="submit">Сбросить к начальным данным</button>
    </form>
 
    <h2>Поиск по ID</h2>
    <form method="get">
        ID: <input type="number" name="search_id" min="1" value="<?= htmlspecialchars($searchId) ?>" required>
        <button type="submit">Найти</button>
    </form>
    <?php if ($searchId !== ''): ?>
        <?php $found = $repo->findById((int)$searchId); ?>
        <?php if ($found === null): ?>
            <p>Транзакция с ID <?= htmlspecialchars($searchId) ?> не найдена.</p>
        <?php else: ?>
            <?= $renderer->render([$found]) ?>
        <?php endif; ?>
    <?php endif; ?>
 
    <h2>Вычисления</h2>
    <p>Общая сумма всех транзакций: <b><?= number_format($manager->calculateTotalAmount(), 2, '.', ' ') ?></b></p>
 
    <form method="get">
        С: <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" required>
        По: <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" required>
        <button type="submit">Сумма за период</button>
    </form>
    <?php if ($from !== '' && $to !== ''): ?>
        <p>Сумма за период: <b><?= number_format($manager->calculateTotalAmountByDateRange($from, $to), 2, '.', ' ') ?></b></p>
    <?php endif; ?>
 
    <form method="get">
        Получатель: <input type="text" name="merchant" value="<?= htmlspecialchars($merchant) ?>" required>
        <button type="submit">Количество транзакций</button>
    </form>
    <?php if ($merchant !== ''): ?>
        <p>Транзакций у этого получателя: <b><?= $manager->countTransactionsByMerchant($merchant) ?></b></p>
    <?php endif; ?>
 
    <h2>Все транзакции</h2>
    <?= $renderer->render($repo->getAllTransactions()) ?>
 
    <h2>Сортировка по дате (от старых к новым)</h2>
    <?= $renderer->render($manager->sortTransactionsByDate()) ?>
 
    <h2>Сортировка по сумме (по убыванию)</h2>
    <?= $renderer->render($manager->sortTransactionsByAmountDesc()) ?>
</body>
</html>




