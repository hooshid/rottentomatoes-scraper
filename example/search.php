<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;

require __DIR__ . "/../vendor/autoload.php";

// if we have no search, go back to search page
if (empty($_GET["search"])) {
    header("Location: /example");
    exit;
}

$search = trim(strip_tags($_GET["search"]));

$rottentomatoes = new Rottentomatoes();
$result = $rottentomatoes->search($search, $_GET['type'])['result'];

if (isset($_GET["output"])) {
    header("Content-Type: application/json");
    echo json_encode($result);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Search - <?php echo strip_tags($_GET["search"]) ?></title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>
<a href="/example/search.php?<?php echo http_build_query($_GET); ?>&output=json" class="output-json-link">JSON Format</a>

<div class="container">
    <div class="boxed">
        <h2 class="text-center pb-30">Result</h2>

        <div class="flex-container">
            <table class="table">
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Year</th>
                    <th>Score</th>
                    <th>Type</th>
                </tr>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><img src="<?php echo $row['thumbnail']; ?>" width="80px" alt="<?php echo $row['title']; ?>"></td>
                        <td><a href="<?php echo $row['full_url']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
                        <td><?php echo $row['year']; ?></td>
                        <td><?php echo $row['score']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>