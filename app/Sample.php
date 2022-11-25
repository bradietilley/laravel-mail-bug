<?php

namespace App;

use Illuminate\Support\Facades\Log;

class Sample
{
    protected $counter = 0;

    public function get(): int
    {
        $this->counter++;

        Log::debug('Sample::get() hit: ' . $this->counter);

        return $this->counter;
    }
}
