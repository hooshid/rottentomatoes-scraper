<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <title>Rottentomatoes</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<div class="container">
    <div class="boxed" style="max-width: 700px;">
        <h2 class="text-center pb-30">Search</h2>

        <form action="/example/search.php" method="get">
            <div class="form-group">
                <label for="search">Search:</label>
                <input class="form-field" type="text" id="search" name="search" maxlength="50" placeholder="Search...">
            </div>

            <div class="form-group">
                <label for="type">Type:</label>
                <select id="type" name="type" class="form-field">
                    <option value="movie">Movies</option>
                    <option value="tv">Tv Shows</option>
                </select>
            </div>

            <div class="row">
                <input type="submit" value="Search">
            </div>
        </form>

    </div>

    <div class="boxed" style="max-width: 700px;">
        <h2 class="text-center pb-30">Extract data</h2>

        <div class="menu-links">
            <a href="/example/extract.php?url=/m/matrix">Movie: The Matrix (1999)</a>
            <a href="/example/extract.php?url=/m/avatar">Movie: Avatar (2009)</a>
            <a href="/example/extract.php?url=/tv/game_of_thrones">TV Series: Game of Thrones</a>
            <a href="/example/extract.php?url=/tv/the_blacklist">TV Series: The Blacklist</a>
            <a href="/example/celebrity.php?url=johnny_depp">Celebrity: Johnny Depp</a>
            <a href="/example/celebrity.php?url=emma_watson">Celebrity: Emma Watson</a>
        </div>
    </div>
</div>

</body>
</html>
