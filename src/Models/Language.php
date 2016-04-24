<?php
namespace Aalberts\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

/**
 * @property string  $code
 * @property string  $name
 * @property string  $local
 * @property boolean $active
 * @property boolean $default
 * @property integer $position
 */
class Language extends Model
{
    use Rememberable;

    protected $table = 'cms_language';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'local',
        'active',
        'default',
        'position',
    ];

    protected $casts = [
        'active'    => 'boolean',
        'default'   => 'boolean',
    ];

}
