<?php
namespace Aalberts\Repositories\Compano;

use Aalberts\Enums\CacheTag;
use App\Models\Aalberts\Compano\Color;

class ColorRepository extends AbstractFilterableRepository
{
    protected $translated = true;
    protected $cacheTags = [ CacheTag::CMP_MISC ];

    public function model()
    {
        return Color::class;
    }

}
