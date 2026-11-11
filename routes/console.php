<?php

use Illuminate\Support\Facades\Schedule;

// Clear out rounds that never made the high score table.
Schedule::command('model:prune')->daily();
