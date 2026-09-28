<?php
$a = 0;
$b = 0;

// цикл for
echo "===== Цикл for ===== <br>";
for($i = 0; $i <= 5; $i++) {
    echo "Шаг $i) a = $a и b = $b <br>";
    $a += 10;
    $b += 5;
}

echo "End of the loop with for: a = $a, b = $b <br><br>";

// цикл while 
echo "===== Цикл while ===== <br>";
$i = 0;
$a = 0;
$b = 0;

while($i < 6) {
    echo "Шаг $i) a = $a и b = $b <br>";
    $a += 10;
    $b += 5;

    $i++;
}

echo "End of the loop with while: a = $a, b = $b <br><br>";

// цикл do-while
echo "===== Цикл do-while ===== <br>";
$i = 0;
$a = 0;
$b = 0;

do {
    echo "Шаг $i) a = $a и b = $b <br>";
    $a += 10;
    $b += 5;

    $i++;

} while($i < 6);

echo "End of the loop with do-while: a = $a, b = $b <br><br>";
?>