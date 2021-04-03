<?php

namespace App\Observers;

use App\Lab;

class LabObserver
{
    /**
     * Handle the lab "created" event.
     *
     * @param  \App\Lab  $lab
     * @return void
     */
    public function created(Lab $lab)
    {
        $lab->postLog();
    }

    /**
     * Handle the lab "updated" event.
     *
     * @param  \App\Lab  $lab
     * @return void
     */
    public function updated(Lab $lab)
    {
        $lab->postLog();
    }
}
