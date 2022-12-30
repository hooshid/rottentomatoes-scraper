<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;

require __DIR__ . "/../vendor/autoload.php";

if (empty($_GET["url"])) {
    header("Location: /example");
    exit;
}

$url = trim(strip_tags($_GET["url"]));

$rottentomatoes = new Rottentomatoes();
$extract = $rottentomatoes->celebrity($url);
$result = $extract['result'];
$error = $extract['error'];

if (isset($_GET["output"])) {
    header("Content-Type: application/json");
    echo json_encode($extract);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Celebrity</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>
<a href="/example/celebrity.php?<?php echo http_build_query($_GET); ?>&output=json" class="output-json-link">JSON
    Format</a>

<div class="container">
    <div class="boxed" style="max-width: 1300px;">
        <?php if ($error) { ?>
            <h1>Error: <?php echo $error; ?></h1>
        <?php } else { ?>
            <?php if ($result['name']) { ?>
                <h2 class="text-center pb-30">Extract data example: <?php echo $result['name']; ?></h2>
            <?php } ?>

            <div class="flex-container">
                <div class="col-25 menu-links">
                    <?php include("sidebar-menu.php") ?>
                </div>

                <div class="col-75">
                    <table class="table">
                        <!-- Main Url -->
                        <tr>
                            <td style="width: 140px;"><b>RottenTomatoes Full Url:</b></td>
                            <td>[<a href="<?php echo $result['full_url']; ?>"><?php echo $result['full_url']; ?></a>]
                            </td>
                        </tr>

                        <!-- Name -->
                        <?php if ($result['name']) { ?>
                            <tr>
                                <td><b>Name:</b></td>
                                <td><?php echo $result['name']; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- Thumbnail -->
                        <?php if ($result['thumbnail']) { ?>
                            <tr>
                                <td><b>Thumbnail:</b></td>
                                <td><img src="<?php echo $result['thumbnail']; ?>" alt="<?php echo $result['name']; ?> thumbnail" style="max-width: 100px;"></td>
                            </tr>
                        <?php } ?>

                        <!-- Bio -->
                        <?php if ($result['bio']) { ?>
                            <tr>
                                <td><b>Bio:</b></td>
                                <td><?php echo $result['bio']; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- Movies -->
                        <?php if ($result['movies']) { ?>
                            <tr>
                                <td><b>Movies:</b></td>
                                <td>
                                    <?php foreach ($result['movies'] as $row) { ?>
                                        <a href="extract.php?url=<?php echo $row['url']; ?>">
                                            <?php echo $row['title']; ?> (<?php echo $row['year']; ?>)
                                        </a>
                                        <br>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <!-- Series -->
                        <?php if ($result['series']) { ?>
                            <tr>
                                <td><b>Series:</b></td>
                                <td>
                                    <?php foreach ($result['series'] as $row) { ?>
                                        <a href="extract.php?url=<?php echo $row['url']; ?>">
                                            <?php echo $row['title']; ?> (<?php echo $row['year']; ?>)
                                        </a>
                                        <br>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>