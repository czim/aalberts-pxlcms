<?php
namespace Aalberts;

use Aalberts\Repositories\CountryRepository;
use Aalberts\Repositories\LanguageRepository;
use Aalberts\Repositories\Compano\SupplierRepository;
use App\Models\Aalberts\Cms\Country;
use App\Models\Aalberts\Cms\Language;
use App\Models\Aalberts\Compano\Supplier;
use Illuminate\Database\Eloquent\Collection;

class AalbertsHelper
{

    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * @var SupplierRepository
     */
    protected $supplierRepository;

    /**
     * Keyed by code
     *
     * @var null|Collection|Language[]
     */
    protected $languages;

    /**
     * Keyed by code
     * 
     * @var null|Collection|Country[]
     */
    protected $countries;

    /**
     * @var null|Language
     */
    protected $defaultLanguage;

    /**
     * @var null|Country
     */
    protected $defaultCountry;

    /**
     * @var bool|null|Supplier      false if not yet set
     */
    protected $defaultSupplier = false;


    public function __construct(
        LanguageRepository $languageRepository,
        CountryRepository $countryRepository,
        SupplierRepository $supplierRepository
    ) {
        $this->languageRepository = $languageRepository;
        $this->countryRepository  = $countryRepository;
        $this->supplierRepository = $supplierRepository;
    }

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
        return config('aalberts.salesorganizationcode');
    }

    /**
     * @return Collection|Language[]
     */
    public function languages()
    {
        if (null === $this->languages) {
            $this->languages = $this->languageRepository->available()
                ->keyBy('code');
        }

        return $this->languages;
    }

    /**
     * @return Language|null
     */
    public function defaultLanguage()
    {
        if (null === $this->languages) {
            $this->defaultLanguage = $this->languageRepository->defaultAvailable();
        }

        return $this->defaultLanguage;
    }

    /**
     * Returns code for currently active language
     *
     * @return null|Language
     */
    public function currentLanguage()
    {
        $locale    = app()->getLocale();
        $languages = $this->languages();
        
        if ( ! $languages->has($locale)) return null;
        
        return $languages->get($locale);
    }

    /**
     * @return Collection|Country[]
     */
    public function countries()
    {
        if (null === $this->countries) {
            $this->countries = $this->countryRepository->available()
                ->keyBy('code');
        }

        return $this->countries;
    }

    /**
     * @return Country|null
     */
    public function defaultCounty()
    {
        if (null === $this->languages) {
            $this->defaultCountry = $this->countryRepository->defaultAvailable();
        }

        return $this->defaultCountry;
    }

    /**
     * @return null|Supplier
     */
    public function defaultSupplier()
    {
        if (false === $this->defaultSupplier) {
            $supplierSlug = config('aalberts.supplier-slug');

            if ( ! $supplierSlug) {
                $this->defaultSupplier = null;
            } else {
                $this->defaultSupplier = $this->supplierRepository->findBySlug($supplierSlug);
            }
        }

        return $this->defaultSupplier;
    }

    /**
     * @return array    associative, keyed by property name
     */
    public function itemTableDimensionProperties()
    {
        return config('aalberts.item-fields.dimension', []);
    }

}
