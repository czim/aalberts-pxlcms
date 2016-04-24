<?php
namespace Aalberts\Generator\Writer\Steps;
use Aalberts\Generator\Writer\CmsModelWriter;

/**
 * Should always be executed last, since only then do we know
 * what to include for the imports.
 */
class StubReplaceImportsAndTraits extends \Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceImportsAndTraits
{

    /**
     * @return string
     */
    protected function getTraitsReplace()
    {
        $traits = [];

        if ($this->data['is_translated']) {
            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.traits.translatable_fqn')
            );
        } else {
            $this->context->importsNotUsed[] = CmsModelWriter::IMPORT_TRAIT_TRANSLATABLE;
        }

        if ($this->data['is_listified']) {
            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.traits.listify_fqn')
            );
            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.traits.listify_constructor_fqn')
            );
        } else {
            $this->context->importsNotUsed[] = CmsModelWriter::IMPORT_TRAIT_LISTIFY;
        }

        if ( ! $this->context->blockRememberableTrait) {
            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.traits.rememberable_fqn')
            );
        } else {
            $this->context->importsNotUsed[] = CmsModelWriter::IMPORT_TRAIT_REMEMBERABLE;
        }

        // scopes

        if ($this->useScopeActive()) {
            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.traits.scope_active_fqn')
            );
        } else {
            $this->context->importsNotUsed[] = CmsModelWriter::IMPORT_TRAIT_SCOPE_ACTIVE;
        }

        if ($this->useScopePosition()) {

            if (count($this->data['ordered_by'])) {
                $traits[] = $this->context->getClassNameFromNamespace(
                    config('pxlcms.generator.models.traits.scope_cmsordered_fqn')
                );
            } else {
                $traits[] = $this->context->getClassNameFromNamespace(
                    config('pxlcms.generator.models.traits.scope_position_fqn')
                );
            }

        } else {
            $this->context->importsNotUsed[] = CmsModelWriter::IMPORT_TRAIT_SCOPE_ORDER;
        }

        // sluggable?
        if ($this->context->modelIsSluggable) {

            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.slugs.sluggable_trait')
            );

        } elseif ($this->context->modelIsParentOfSluggableTranslation) {

            $traits[] = $this->context->getClassNameFromNamespace(
                config('pxlcms.generator.models.slugs.sluggable_translated_trait')
            );
        }

        // organization
        if ($this->data['has_organization']) {
            $traits[] = 'ForOrganization';
        }

        if ( ! count($traits)) return '';


        // set them in the right order
        if (config('pxlcms.generator.aesthetics.sort_imports_by_string_length')) {

            // sort from shortest to longest
            usort($traits, function ($a, $b) {
                return strlen($a) - strlen($b);
            });

        } else {
            sort($traits);
        }


        $lastIndex = count($traits) - 1;

        $replace = $this->tab() . 'use ';

        foreach ($traits as $index => $trait) {

            $replace .= ($index > 0 ? $this->tab(2) : null)
                . $trait
                . ($index == $lastIndex ? ";\n" : ',')
                . "\n";
        }

        return $replace;
    }


    /**
     * Returns the replacement for the use use-imports placeholder
     *
     * @return string
     */
    protected function getImportsReplace()
    {
        $replace = parent::getImportsReplace();

        if ($this->data['has_organization']) {
            $replace = rtrim($replace) . "\n";
            $replace .= "use Aalberts\\Models\\Scopes\\ForOrganization;\n"
                      . "\n";
        }

        return $replace;
    }

}
