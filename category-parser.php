<?php
set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';

$resource = 'https://www.mysupersite.ru';
$category = $resource . '/catalog/sumki';

$client = new \Guzzle\Http\Client(['timeout' => 5, 'connect_timeout' => 5]);

$items_links = [];
for ($i = 1; $i<=55; $i++) {
    $request = $client->createRequest('GET', $category.'?'.http_build_query(['page' => $i]));
    try {
        $response = $request->send();
    } catch (Exception $e) {
        echo 'Ошибка при загрузке ' . $i . '. Текст ошибки: ' . $e->getMessage() . PHP_EOL;
        continue;
    }

    if ($response->isSuccessful()) {
        $items = [];

        preg_match_all(
            '#class="product__info".+?href="(.+?)">.+?</a>#s',
            $response->getBody(true),
            $items
        );

        foreach ($items[1] as $item) {
            $items_links[] = $resource.$item;
        }
    } else {
        echo 'Не удалось получить данные со страницы '. $i. PHP_EOL;
    }
}

$items_links = array_unique($items_links);

file_put_contents(__DIR__.'/items.txt', implode(PHP_EOL, $items_links));
