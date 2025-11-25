<?php
}
$channel = $channelNodes->item(0);


// Create item element and children
$item = $dom->createElement('item');


title:
$titleNode = $dom->createElement('title');
$titleNode->appendChild($dom->createTextNode($title));
$item->appendChild($titleNode);


$linkNode = $dom->createElement('link');
$linkNode->appendChild($dom->createTextNode($link));
$item->appendChild($linkNode);


$descNode = $dom->createElement('description');
$descNode->appendChild($dom->createCDATASection($description));
$item->appendChild($descNode);


$pubNode = $dom->createElement('pubDate');
$pubNode->appendChild($dom->createTextNode(gmdate('D, d M Y H:i:s \G\M\T')));
$item->appendChild($pubNode);


$guidNode = $dom->createElement('guid');
$guidNode->appendChild($dom->createTextNode($guid));
$item->appendChild($guidNode);


// Prepend item to channel (newest first) — insert before first item if exists
$firstItem = null;
foreach ($channel->childNodes as $cn) {
if ($cn->nodeName === 'item') { $firstItem = $cn; break; }
}


if ($firstItem) {
$channel->insertBefore($item, $firstItem);
} else {
$channel->appendChild($item);
}


// Write atomically with lock
$tempFile = $feedFile . '.tmp';
if (false === $dom->save($tempFile)) {
http_response_code(500);
echo "Failed to write temporary feed file.";
exit;
}


// Use rename to replace file atomically where possible
if (!rename($tempFile, $feedFile)) {
// fallback to direct write with lock
$fp = fopen($feedFile, 'c');
if (!$fp) {
http_response_code(500);
echo "Failed to open rss.xml for writing.";
exit;
}
if (!flock($fp, LOCK_EX)) {
fclose($fp);
http_response_code(500);
echo "Failed to get file lock.";
exit;
}
ftruncate($fp, 0);
rewind($fp);
$written = fwrite($fp, $dom->saveXML());
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
if ($written === false) {
http_response_code(500);
echo "Failed to write rss.xml.";
exit;
}
}


// Redirect back to index
header('Location: index.php');
exit;