<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Series;

class SeriesRepository extends AbstractFilterableRepository
{
    protected $translated = false;
    protected $cacheTags = [ CacheTag::CMP_MISC ];

    public function model()
    {
        return Series::class;
    }

}
