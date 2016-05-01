<?php
namespace Aalberts\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * CmsModel Adapter
 *
 * Based on Eloquent, but with overrides and adaptations for accessing
 * Aalberts CMS database content.
 *
 * @property int     $cmsModule
 * @property boolean $active
 * @property int     $position
 */
class CmsModel extends Model
{

    // relationsConfig special / standard model types
    const RELATION_TYPE_MODEL    = 0;
    const RELATION_TYPE_IMAGE    = 1;
    const RELATION_TYPE_FILE     = 2;
    const RELATION_TYPE_CHECKBOX = 3;
    const RELATION_TYPE_CATEGORY = 4;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdts';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modifiedts';

    /**
     * Disable timestamps for CMS models by default
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Default hidden properties, standard for CMS table
     *
     * @var array
     */
    protected $hidden = [
        'active',
        'position',
    ];

    /**
     * The column to use for active scoping
     *
     * @var string
     */
    protected $cmsActiveColumn = 'active';

    /**
     * The column to use for position ordering
     *
     * @var string
     */
    protected $cmsPositionColumn = 'position';

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Information about CMS model-reference relationships
     *
     * @var array
     */
    protected $relationsConfig = [];

    /**
     * By default, fall back to translation fallback with Translatable
     *
     * @var bool
     */
    protected $useTranslationFallback = true;

    /**
     * The model class which represents the cms_languages content
     *
     * @var string
     */
    protected static $cmsLanguageModel = Language::class;

    /**
     * The model class which represents the resizes in the cms
     *
     * @var string
     */
    protected static $cmsResizeModel = Resize::class;

    /**
     * Whether, if sluggable model, the slug is to be saved on the model itself
     *
     * @var bool
     */
    protected $cmsSluggableLocally;

    /**
     * Date columns stored as something other than a Unix Timestamp
     *
     * @var string[]
     */
    protected $nonTimestampDates = [];

    /**
     * The default CMS model listify config
     *
     * @var array
     */
    protected $cmsListifyConfig = [
        'top_of_list' => 1,
        'column'      => 'position',
        'scope'       => '1 = 1',
        'add_new_at'  => 'bottom',
    ];

    /**
     * The default configured CMS sorting clauses, in attribute => direction pairs
     * Only relevant when not sorting by position / listify !
     *
     * @var array
     */
    protected $cmsOrderBy = [];


    // ------------------------------------------------------------------------------
    //      Date adjustments for Unix Timestamp storage
    // ------------------------------------------------------------------------------

    /**
     * Get the attributes that should be converted to dates.
     * Overridden to skip timestamp columns
     *
     * @inheritdoc
     */
    public function getDates()
    {
        $defaults = [];

        return $this->timestamps ? array_merge($this->dates, $defaults) : $this->dates;
    }

    /**
     * Override to make sure timestamp fields are mutated correctly
     *
     * @inheritdoc
     */
    protected function mutateAttributeForArray($key, $value)
    {
        // for timestamps, do NOT pass through the mutator, or
        // we'll end up with Carbon instances
        if (    static::CREATED_AT === $key || static::UPDATED_AT === $key
            ||  in_array($key, $this->nonTimestampDates)
        ) {
            return $value;
        }

        return parent::mutateAttributeForArray($key, $value);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @inheritdoc
     */
    protected function asDateTime($value)
    {
        if (0 === $value || null === $value || '0000-00-00 00:00:00' === $value) {
            return null;
        }

        // catch standard datetime format
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
            $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        }

        return parent::asDateTime($value);
    }

    public function getCreatedtsAttribute()
    {
        $value = $this->attributes[static::CREATED_AT];

        if (0 === $value || null === $value || '0000-00-00 00:00:00' === $value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    public function getModifiedtsAttribute()
    {
        $value = $this->attributes[static::UPDATED_AT];

        if (0 === $value || null === $value || '0000-00-00 00:00:00' === $value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    public function getAccessedtsAttribute()
    {
        $value = $this->attributes['accessedts'];

        if (0 === $value || null === $value || '0000-00-00 00:00:00' === $value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    /**
     * Mutator to make sure created timestamp uses MySQL DateTime format
     *
     * @param $value
     * @return $this
     */
    public function setCreatedtsAttribute($value)
    {
        if ($value instanceof Carbon) {
            $value = $value->format('Y-m-d H:i:s');
        } else {
            $value = null;
        }

        $this->attributes[static::CREATED_AT] = $value;

        return $this;
    }

    /**
     * Mutator to make sure modified timestamp uses MySQL DateTime format
     *
     * @param mixed $value
     * @return $this
     */
    public function setModifiedtsAttribute($value)
    {
        if ($value instanceof Carbon) {
            $value = $value->format('Y-m-d H:i:s');
        } else {
            $value = null;
        }

        $this->attributes[static::UPDATED_AT] = $value;

        return $this;
    }

    /**
     * Mutator to make sure accessed timestamp uses MySQL DateTime format
     *
     * @param mixed $value
     * @return $this
     */
    public function setAccessedtsAttribute($value)
    {
        if ($value instanceof Carbon) {
            $value = $value->format('Y-m-d H:i:s');
        } else {
            $value = null;
        }

        $this->attributes['accessedts'] = $value;

        return $this;
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @inheritdoc
     */
    public function attributesToArray()
    {
        // unset date values that are 0, so they don't cause problems with serializeDate()
        foreach ($this->getDates() as $key) {
            if ( ! isset($this->attributes[$key])) continue;

            // 0 should be considered 'unset' aswell
            if (0 === $this->attributes[$key]) {
                $this->attributes[$key] = null;
            }
        }

        $attributes = parent::attributesToArray();

        // make sure that our date values are strings
        foreach ($this->getDates() as $key) {
            if ($attributes[$key] instanceof \DateTime) {
                $attributes[$key] = $attributes[$key]->format($this->getDateFormat());
            }
        }

        return $attributes;
    }

    // ------------------------------------------------------------------------------
    //      Relationships
    // ------------------------------------------------------------------------------

    /**
     * Override for CMS naming
     *
     * @return string
     */
    public function getForeignKey()
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Returns value for either foreign key or eager loaded contents of relation,
     * depending on what is expected
     *
     * This should not break calls to the belongsTo relation method, including after
     * using the load() method to eager load the relation's contents
     *
     * @param string $key
     * @return mixed
     */
    public function getBelongsToRelationAttributeValue($key)
    {
        if ($this->relationLoaded($key)) {

            // check to make sure we don't break eager loading and internal
            // lookups for the foreign key
            $self = __FUNCTION__;

            $caller = array_first(debug_backtrace(false), function ($key, $trace) use ($self) {
                $caller = $trace['function'];

                // skip first two (since that includes the Model's generated method)
                if ($key < 2) {
                    return false;
                }

                if (array_get($trace, 'class') === 'Illuminate\Database\Eloquent\Relations\BelongsTo') {
                    return false;
                }

                return $caller != $self
                    && $caller != 'mutateAttribute'
                    && $caller != 'getAttributeValue'
                    && $caller != 'getAttribute'
                    && $caller != '__get';
            });

            if (    ! array_key_exists('class', $caller)
                ||  (   $caller['class'] !== 'Illuminate\Database\Eloquent\Model'
                    &&  $caller['class'] !== 'Illuminate\Database\Eloquent\Builder'
                    &&  $caller['class'] !== 'Illuminate\Database\Eloquent\Relations\Relation'
                    )
            ) {
                return $this->relations[$key];
            }
        }

        return $this->attributes[$key];
    }


    // ------------------------------------------------------------------------------
    //      Images
    // ------------------------------------------------------------------------------

    /**
     * Returns resize-enriched images for a special CMS model image relation
     *
     * To be called from an accessor, so it can return images based on its name,
     * which should be get<relationname>Attribute().
     *
     * @return Collection
     */
    protected function getImagesWithResizes()
    {
        // first get the images through the relation
        $relation = $this->getRelationForImagesWithResizesCaller();

        $images = $this->{$relation}()->get();

        if (empty($images)) return $images;

        // then get extra info and retrieve the resizes for it
        //$fieldId = $this->getCmsReferenceFieldId($relation);

        //$resizes = $this->getResizesForFieldId($fieldId);
        return $images;

        //if (empty($resizes)) return $images;

        // decorate the images with resizes
        //foreach ($images as $image) {
        //
        //    $fileName = $image->file;
        //    $imageResizes = [];
        //
        //    foreach ($resizes as $resize) {
        //
        //        $imageResizes[ $resize->prefix ] = [
        //            'id'     => $resize->id,
        //            'prefix' => $resize->prefix,
        //            'file'   => $resize->prefix . $fileName,
        //            'url'    => Paths::images($resize->prefix . $fileName),
        //            'width'  => $resize->width,
        //            'height' => $resize->height,
        //        ];
        //    }
        //
        //    // append full resizes info
        //    $image->resizes = $imageResizes;
        //}
        //
        //return $images;
    }

    /**
     * Get the relationship name of the image accessor for which images are enriched
     *
     * @return string
     */
    protected function getRelationForImagesWithResizesCaller()
    {
        $self = __FUNCTION__;

        $caller = Arr::first(debug_backtrace(false), function ($key, $trace) use ($self) {
            $caller = $trace['function'];

            return ! in_array($caller, ['getImagesWithResizes']) && $caller != $self;
        });

        if (is_null($caller)) return null;

        // strip 'get' from front and 'attribute' from rear
        return Str::camel(substr($caller['function'], 3, -9));
    }


    // ------------------------------------------------------------------------------
    //      Translatable support
    // ------------------------------------------------------------------------------

    /**
     * Retrieves (and caches) the locale
     *
     * @param string $locale
     * @return int|null     null if language was not found for locale
     */
    public function lookUpLanguageIdForLocale($locale)
    {
        $locale = $this->normalizeLocale($locale);

        /** @var Model $languageModel */
        $languageModel = static::$cmsLanguageModel;

        $language = $languageModel::where(config('pxlcms.translatable.locale_code_column'), $locale)
            ->remember((config('pxlcms.cache.languages', 15)))
            ->first();

        if (empty($language)) return null;

        return $language->id;
    }

    /**
     * Retrieves locale for a given language ID code
     *
     * @param int $languageId
     * @return string|null  of language for ID was not found
     */
    public function lookupLocaleForLanguageId($languageId)
    {
        /** @var Model $languageModel */
        $languageModel = static::$cmsLanguageModel;

        $language = $languageModel::find($languageId);

        if (empty($language)) return null;

        return $this->normalizeToLocale($language->code);
    }

    /**
     * Normalizes the locale so it will match the CMS's language code
     *
     * en-US to en, for instance?
     *
     * @param string $locale
     * @return string
     */
    protected function normalizeLocale($locale)
    {
        return strtolower($locale);
    }

    /**
     * Normalizes the language code in the CMS languages table so it
     * matches locales.
     *
     * @param string $languageCode
     * @return string
     */
    protected function normalizeToLocale($languageCode)
    {
        return strtolower($languageCode);
    }

}
