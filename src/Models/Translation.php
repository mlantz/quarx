<?php

namespace Grafite\Cms\Models;

use Grafite\Cms\Models\CmsModel;

// Reads config and if given extend the provided class per module or by CMS Base.
$cmsModel = config('cms.models.menu') ?? CmsModel::class;
if (! is_a($cmsModel, CmsModel::class, true)) {
    throw InvalidConfiguration::modelIsNotValid($cmsModel);
}
class_alias($cmsModel, 'Grafite\Cms\Models\CmsBaseModel');


class Translation extends Grafite\Cms\Models\CmsBaseModel
{
    public $table = 'translations';

    public $primaryKey = 'id';

    protected $guarded = [];

    public static $rules = [];

    protected $fillable = [
        'entity_id',
        'entity_type',
        'entity_data',
        'language',
    ];

    public function getDataAttribute()
    {
        $object = app($this->entity_type);

        $attributes = (array) json_decode($this->entity_data);
        $object->attributes = array_merge($attributes, [
            'id' => $this->entity_id,
        ]);

        return $object;
    }
}
