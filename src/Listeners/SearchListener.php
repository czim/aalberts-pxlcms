<?php
namespace Aalberts\Listeners;

use Aalberts;
use Aalberts\Events\SearchPerformed;
use App\Models\Aalberts\Cms\LogSearch;

/**
 * Class SearchListener
 *
 * Logs searches to cms_log_search when searches are performed.
 */
class SearchListener
{

    /**
     * Handle the event.
     *
     * @param SearchPerformed $event
     */
    public function handle(SearchPerformed $event)
    {
        // only do this if enabled in the environment
        if ( ! config('aalberts.log-searches')) return;

        LogSearch::create([
            'organization' => Aalberts::organization(),
            'term'         => $event->term,
            'date'         => \Carbon\Carbon::now(),
        ]);
    }

}
