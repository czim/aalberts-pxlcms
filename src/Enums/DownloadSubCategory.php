<?php
namespace Aalberts\Enums;

use MyCLabs\Enum\Enum;

class DownloadSubCategory extends Enum
{
    const VALVE                             = 'valve';
    const THERMOSTATS                       = 'thermostats';
    const ELECTRIC_ACTUATOR                 = 'electric-actuator';
    const ELECTRIC_CONTROLLERS              = 'electric-controllers';
    const PNEUMATIC_ACTUATORS               = 'pneumatic-actuators';
    const PNEUMATIC_CONTROLLERS_POSITIONERS = 'pneumatic-controllers-positioners';
    const DIFFERENTIAL_PRESSURE_CONTROL     = 'differential-pressure-controls';
    const PRESSURE_REDUCING_VALVES          = 'pressure-reducing-valves';
    const ACCESSORIES                       = 'accessories';
}
