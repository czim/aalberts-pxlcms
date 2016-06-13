<?php
namespace Aalberts;

use Aalberts\Models\Language;
use App\Models\Aalberts\Cms\Country;
use Illuminate\Database\Eloquent\Collection;

class AalbertsHelper
{

    /**
     * @return int
     */
    public function organization()
    {
        return config('aalberts.organization');
    }

    /**
     * @return string
     */
    public function organizationCode()
    {
        return config('aalberts.organizationcode');
    }

    /**
     * @return Collection|Language[]
     */
    public function languages()
    {
        return collect();
    }

    /**
     * @return Collection|Country[]
     */
    public function countries()
    {
        return collect();
    }

}
