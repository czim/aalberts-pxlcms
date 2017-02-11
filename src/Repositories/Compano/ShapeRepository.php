<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Shape;

class ShapeRepository extends AbstractFilterableRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_MISC ];

    public function model()
    {
        return Shape::class;
    }

}
