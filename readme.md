# Repainter

A painting game made in 48 hours at Global Game Jam 2020 (theme: "Repair"), then brought up to date in 2026. Someone has splattered a painting. Pick colours and a brush from the palette and paint it back before the clock runs out. The server scores how close you got and keeps a high score table for each level.

![Repairing a splattered smiley face](docs/screenshot.webp)

Made at the jam by David Neilsen, Rey Benher and Joey Lee. Modernised by David Neilsen.

## The 2026 update

The jam version ran on Laravel 6 and PHP 7, with the score worked out in the browser. The update:

- **Laravel 13 on PHP 8.3+** (tested on 8.4 and 8.5). A fresh Laravel 13 app with the game's code moved across, instead of six major upgrades on top of jam code. The front end is built with Vite instead of Laravel Mix.
- **The server is the source of truth for scores.** In the jam version the browser sent its own score and time bonus, so anyone could post any score. Now a *round* starts on the server, which scores the painting and works out the time bonus from its own clock. A high score can only be added for a finished round, once.
- **Originals are private.** The original paintings were public files, so a script could submit the answer. They now live outside `public/` and a round's original is only served after it finishes.
- **A scoring bug fixed.** Unchanged areas were detected one colour channel at a time, so a perfect repaint could score as low as 38. A perfect repaint now scores 100 on every level, and there's a test for it.
- **Tests and checks.** Pest tests for the scoring and the API, Larastan at its strictest level, Pint, and GitHub Actions running them on PHP 8.4 and 8.5.
- **Original art only.** Levels that used copyrighted characters were removed and replaced with new ones.

## How it works

The game is an AngularJS app (`resources/js`). The Laravel side is a small JSON API:

| Endpoint | What it does |
|---|---|
| `GET /api/levels` | The levels: name, palette, time limit and the splattered image |
| `POST /api/rounds` | Starts a round on a level (the server starts the clock) |
| `POST /api/rounds/{round}/painting` | Scores the painting so far. Called while you paint, to drive the live gauge |
| `POST /api/rounds/{round}/finish` | Stops the clock; the latest score stands and the server adds the time bonus |
| `GET /api/rounds/{round}/original` | The original painting, only once the round is finished |
| `POST /api/rounds/{round}/high-score` | Adds a finished round to the table with the player's name |
| `GET /api/levels/{level}/high-scores` | The top ten for a level |

The interesting parts:

- `app/Services/PaintingScorer.php` compares the painting with the original and the splattered image. All three are shrunk to a 100 x 100 grid and lightly blurred, so the score is about colour and shape rather than exact pixels. The score is how much of the gap between the splattered image and the original you closed: 100 is perfect, 0 is no better than you started, below 0 is worse.
- `app/Models/Round.php` holds the rules: the time limit (plus a short grace period for slow uploads), the time bonus, and when a round can still go on the high score table. Old rounds that never made the table are pruned daily.
- `config/levels.php` lists the levels. Splattered images are in `public/images/levels`, originals in `resources/levels`.

## Run it locally

Needs PHP 8.3+ (with GD and SQLite), Composer and Node.

```sh
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Tests and checks

```sh
vendor/bin/pest       # tests
vendor/bin/phpstan    # static analysis (Larastan, max level)
vendor/bin/pint       # code style
```

## Jam brainstorm

The ideas list from the start of the jam:

Ideas:

Doable:

Sysadmin repair
 - console sim
 - apache not working etc
 - hdd full

Painting repair

Tower defense
 - repair own towers?
 - multi player balance attack and defence

Other:

PC repair game
 - physical?
 - board game?

Reverse lemings
 - Instead of destorying terrain, must repair/build it to get to exit

Pipe game
 - repair pipes

Circut board repair
 - have to fix currernts etc
 - could be bomb 

Repair nuclear reactor
 
City repair

Evil AI is repairing itself, must be destroied

Space station repair
 - power failures
 - leaks
 - astroids
 - solar flares
 - aliens

Fluid sim
 - bucket
