<?php

use Hooshid\RottentomatoesScraper\Rottentomatoes;

require __DIR__ . "/../vendor/autoload.php";

if (empty($_GET["url"])) {
    header("Location: /example");
    exit;
}

$url = trim(strip_tags($_GET["url"]));

$rottentomatoes = new Rottentomatoes();
$extract = $rottentomatoes->extract($url);
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
    <title>Extract</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>
<a href="/example/extract.php?<?php echo http_build_query($_GET); ?>&output=json" class="output-json-link">JSON
    Format</a>

<div class="container">
    <div class="boxed" style="max-width: 1300px;">
        <?php if ($error) { ?>
            <h1>Error: <?php echo $error; ?></h1>
        <?php } else { ?>
            <?php if ($result['title']) { ?>
                <h2 class="text-center pb-30">Extract data example: <?php echo $result['title']; ?></h2>
            <?php } ?>

            <div class="flex-container">
                <div class="col-25 menu-links">
                    <div class="menu-links-title">Movies</div>
                    <a href="extract.php?url=/m/godfather">The Godfather (1972)</a>
                    <a href="extract.php?url=/m/matrix">The Matrix (1999)</a>
                    <a href="extract.php?url=/m/the_father_2021">The Father (2021)</a>

                    <div class="menu-links-title">TV</div>
                    <a href="extract.php?url=/tv/game_of_thrones">Game of Thrones</a>
                    <a href="extract.php?url=/tv/breaking_bad">Breaking Bad</a>
                </div>

                <div class="col-75">
                    <table class="table">
                        <!-- Main Url -->
                        <tr>
                            <td style="width: 140px;"><b>RottenTomatoes Full Url:</b></td>
                            <td>[<a href="<?php echo $result['full_url']; ?>"><?php echo $result['full_url']; ?></a>]
                            </td>
                        </tr>

                        <!-- Title of page -->
                        <?php if ($result['title']) { ?>
                            <tr>
                                <td><b>Title:</b></td>
                                <td><?php echo $result['title']; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- Thumbnail -->
                        <?php if ($result['thumbnail']) { ?>
                            <tr>
                                <td><b>Thumbnail:</b></td>
                                <td><img src="<?php echo $result['thumbnail']; ?>"
                                         alt="<?php echo $result['title']; ?> thumbnail" style="max-width: 100px;"></td>
                            </tr>
                        <?php } ?>

                        <!-- Score & votes -->
                        <?php if ($result['votes']) { ?>
                            <tr>
                                <td><b>Score:</b></td>
                                <td>score: <?php echo $result['score']; ?>,
                                    votes: <?php echo number_format($result['votes']); ?></td>
                            </tr>
                        <?php } ?>

                        <!-- User Score & votes -->
                        <?php if ($result['user_votes']) { ?>
                            <tr>
                                <td><b>User Score:</b></td>
                                <td>score: <?php echo $result['user_score']; ?>,
                                    votes: <?php echo number_format($result['user_votes']); ?></td>
                            </tr>
                        <?php } ?>

                        <!-- Summary -->
                        <?php if ($result['summary']) { ?>
                            <tr>
                                <td><b>Summary:</b></td>
                                <td><?php echo $result['summary']; ?></td>
                            </tr>
                        <?php } ?>

                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>