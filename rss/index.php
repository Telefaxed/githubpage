<?php
// Minimal frontend for viewing and adding RSS items


$feedFile = __DIR__ . '/rss.xml';


// Load feed contents safely if file exists
$feedXml = null;
if (file_exists($feedFile)) {
libxml_use_internal_errors(true);
$feedXml = simplexml_load_file($feedFile);
libxml_clear_errors();
}


?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>RSS Updater</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<main class="container">
<h1>RSS Updater</h1>


<section class="feed">
<h2>Current feed</h2>
<?php if ($feedXml): ?>
<div class="feed-meta">
<strong><?php echo htmlspecialchars((string)$feedXml->channel->title); ?></strong>
<p><?php echo htmlspecialchars((string)$feedXml->channel->description); ?></p>
<p><a href="rss.xml" target="_blank">Open rss.xml</a></p>
</div>
<ul class="items">
<?php foreach ($feedXml->channel->item as $item): ?>
<li>
<a href="<?php echo htmlspecialchars((string)$item->link); ?>" target="_blank"><?php echo htmlspecialchars((string)$item->title); ?></a>
<div class="meta">
<small><?php echo htmlspecialchars((string)$item->pubDate); ?></small>
</div>
<p><?php echo htmlspecialchars((string)$item->description); ?></p>
</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>No rss.xml found — the updater will create one on first submission using the supplied fields.</p>
<?php endif; ?>
</section>


<section class="form">
<h2>Add feed item</h2>
<form id="rssForm" action="update_rss.php" method="post">
<label>Title (required)<br>
<input name="title" type="text" required maxlength="200">
</label>
<label>Link (required)<br>
<input name="link" type="url" required maxlength="1000">
</label>
<label>Description<br>
<textarea name="description" rows="4" maxlength="2000"></textarea>
</label>
<!-- optional guid field, if user leaves blank we'll generate one -->
<label>GUID (optional)<br>
<input name="guid" type="text" maxlength="200">
</label>
<div class="actions">
<button type="submit">Add item</button>
</div>
</form>
</section>
</main>
<script src="script.js"></script>
</body>
</html>