<?php
    require_once 'Date.php';

function getDateOfComic(int $id): Date {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://xkcd.com/$id/info.0.json");

    $result = json_decode(curl_exec($ch), true);
    $date = new Date($result['year'] ?? 2006, $result['month'] ?? 01, $result['day'] ?? 01);

    curl_close($ch);

    return $date;
}

function getRate(Date $date): float {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date");

    $result = simplexml_load_string(curl_exec($ch));
    $rate = 0.0;

    foreach ($result->Valute as $valute) {
        if ($valute->CharCode == 'USD') {
            $rate = (float)str_replace(',', '.', (string)$valute->Value);
            break;
        }
    }

    curl_close($ch);

    return $rate;
}

function createRow(int $count, string $date, float $rate): string {
    return
    "<tr class=\"table__tr\">
        <td class=\"table__td\">$count</td>
        <td class=\"table__td\">$date</td>
        <td class=\"table__td\">$rate</td>
    </tr>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (str_starts_with($key, 'id_')) {
            $date = getDateOfComic($value);
            $rate = getRate($date);

            $json = json_encode([
                'date' => $date->getStr(),
                'rate' => $rate
            ]);

            setcookie('rate_'.time(), $json, time() + 60);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>REST API</title>
</head>
<body>
    <table class="table">
        <tr class="table__tr">
            <th class="table__th">ID</th>
            <th class="table__th">Date</th>
            <th class="table__th">Rate</th>
        </tr>
        <?php
            $count = 0;
            foreach ($_COOKIE as $id => $value) {
                if (str_starts_with($id, 'rate_')) {
                    ++$count;
                    $value = json_decode($value, true);
                    echo createRow($count, $value['date'], $value['rate']);
                }
            }
        ?>
</body>
</html>