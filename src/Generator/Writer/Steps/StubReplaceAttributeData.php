<?php
namespace Aalberts\Generator\Writer\Steps;

use Czim\PxlCms\Generator\Writer\Model\Steps\StubReplaceAttributeData as PxlCmsStubReplaceAttributeData;

class StubReplaceAttributeData extends PxlCmsStubReplaceAttributeData
{

    /**
     * Returns the replacement for the casts placeholder
     *
     * @return string
     */
    protected function getCastsReplace()
    {
        $attributes = $this->data['casts'] ?: [];

        if ( ! count($attributes)) return '';

        // align assignment signs by longest attribute
        $longestLength = 0;

        foreach ($attributes as $attribute => $type) {

            if (strlen($attribute) > $longestLength) {
                $longestLength = strlen($attribute);
            }
        }

        $replace = $this->tab() . "protected \$casts = [\n";

        foreach ($attributes as $attribute => $type) {

            // fake date_timestamp 'type'
            if ($type == 'date_timestamp') {
                $type = 'date';
            }

            $replace .= $this->tab(2)
                . "'" . str_pad($attribute . "'", $longestLength + 1)
                . " => '" . $type . "',\n";
        }

        $replace .= $this->tab() . "];\n\n";

        return $replace;
    }

}
