<?php
header('Content-Type: application/xml; charset=utf-8');

$urls = [
    'https://www3.nhk.or.jp/rss/news/cat0.xml',
    'https://news.yahoo.co.jp/rss/topics/top-picks.xml'
];

$mergedItems = [];

foreach ($urls as $url) {
    $rss = @file_get_contents($url);
    if ($rss === false) continue;

    $xml = @simplexml_load_string($rss);
    if ($xml === false || !isset($xml->channel->item)) continue;

    foreach ($xml->channel->item as $item) {
        $mergedItems[] = $item;
    }
}

// 最新順にソート（pubDateがある場合のみ）
usort($mergedItems, function ($a, $b) {
    return strtotime($b->pubDate) - strtotime($a->pubDate);
});

// 出力用のRSSを組み立てる
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<rss version=\"2.0\">\n<channel>\n";
echo "<title>NHK + Yahoo! News Combined</title>\n";
echo "<link>#</link>\n";
echo "<description>Combined news feed</description>\n";

foreach (array_slice($mergedItems, 0, 20) as $item) {
    echo "<item>\n";
    echo "<title>" . htmlspecialchars($item->title, ENT_XML1, 'UTF-8') . "</title>\n";
    echo "<link>" . htmlspecialchars($item->link, ENT_XML1, 'UTF-8') . "</link>\n";
    echo "<pubDate>" . htmlspecialchars($item->pubDate, ENT_XML1, 'UTF-8') . "</pubDate>\n";
    echo "</item>\n";
}

echo "</channel>\n</rss>";
?>
