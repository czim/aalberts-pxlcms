<?php
namespace Aalberts\Models\Presenters\Cms;

use Aalberts\Models\Presenters\AbstractPresenter;
use Aalberts\Models\Presenters\Traits\HasGalleryHeaderImage;

class ProjectPresenter extends AbstractPresenter
{
    use HasGalleryHeaderImage;

    protected $galleryRelation      = 'projectGalleries';
    protected $galleryImageRelation = 'projectGalleryImages';


    /**
     * @return null|string
     */
    public function date()
    {
        return $this->normalizeDate($this->entity->date);
    }

    /**
     * @return null|string
     */
    public function dateTime()
    {
        return $this->normalizeDateTime($this->entity->date);
    }

    /**
     * @return string
     */
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

    /**
     * Returns normalized location value.
     * If the location is empty (or just a comma), it will be returned as null.
     *
     * @return string|null
     */
    public function location()
    {
        if ( ! $this->entity->location || trim($this->entity->location) === ',') {
            return null;
        }

        return $this->entity->location;
    }

}
