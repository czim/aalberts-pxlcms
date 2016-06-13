<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;

class ProjectPresenter extends AbstractPresenter
{
    
    public function date()
    {
        return $this->normalizeDate($this->entity->date);
    }

    public function dateTime()
    {
        return $this->normalizeDateTime($this->entity->date);
    }

    public function datePeriod()
    {
        $period = '';

        if ($this->entity->starting_date) {
            $period .= $this->entity->starting_date;
        }

        // only add the second date if it is set and not the same as the first date
        if (    $this->entity->starting_date
            &&  (   ! $this->entity->launch_date
                ||  $this->entity->launch_date == $this->entity->starting_date
                )
        ) {
            return $period;
        }

        if ($this->entity->starting_date) {
            $period .= ' - ';
        }

        $period .= $this->entity->launch_date;

        return $period;
    }

    public function hasHeaderImage()
    {
        return (     $this->entity->projectGalleries
                &&   count($this->entity->projectGalleries)
                &&   $this->entity->projectGalleries->first()->projectGalleryImages
                &&   count($this->entity->projectGalleries->first()->projectGalleryImages)
                );
    }

    public function headerImage()
    {
        if ( ! $this->hasHeaderImage()) return null;

        return $this->entity->projectGalleries->first()->projectGalleryImages->first()->file;
    }

    /**
     * Returns the data required for rendering of a previous/next link.
     * This should return whatever the prev-next partial expects for 'prev' or 'next' key data.
     *
     * @return string
     */
    public function prevNext()
    {
        return route(
            config('aalberts.routes.projects-detail', 'projects-detail'),
            [ $this->entity->slug ]
        );
    }
}
