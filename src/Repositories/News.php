<?php
namespace Aalberts\Repositories;

use App\Models\Aalberts\Cms\News as NewsModel;
use Czim\Repository\Criteria\Common\Custom;
use Czim\Repository\Criteria\Common\OrderBy;
use Czim\Repository\Enums\CriteriaKey;

class News extends AbstractRepository
{
    protected $translated = true;

    /**
     * Returns specified model class name.
     *
     * Note that this is the only method.
     *
     * @return string
     */
    public function model()
    {
        return NewsModel::class;
    }



    /**
     * @inheritdoc
     */
    public function defaultCriteria()
    {
        return parent::defaultCriteria()
            ->merge(
                collect([
                    // remove the global scope for position ordering
                    new Custom(function ($query) { return $query->unordered(); }),
                    // order by date instead
                    CriteriaKey::ORDER => new OrderBy('date', 'desc')
                ])
            );
    }

}
