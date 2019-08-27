<?php

namespace Grafite\Cms\Repositories;

use Carbon\Carbon;
use Grafite\Cms\Models\Translation;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationRepository
{
    public $model;

    public function __construct(Translation $translation)
    {
        $this->model = $translation;
    }

    /**
     * Create or Update an entry
     *
     * @param  integer $entityId
     * @param  string $entityType
     * @param  string $lang
     * @param  array $payload
     *
     * @return boolean
     */
    public function createOrUpdate($entityId, $entityType, $lang, $payload)
    {
        $translation = $this->model->firstOrCreate([
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'language' => $lang,
        ]);

        unset($payload['_method']);
        unset($payload['_token']);

        $translation->entity_data = json_encode($payload);

        return $translation->save();
    }

    /**
     * Get all published entries
     *
     * @param  string $type
     *
     * @return \Grafite\Cms\Models\Translation
     */
    public function publishedItems($type)
    {
        $published = collect([]);

        $items = $this->model->where('entity_type', $type)
            ->where('entity_data->is_published', 'on')
            ->where('entity_data->published_at', '<=', Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s'))
            ->get();

        foreach ($items as $item) {
            $published->push($item->data);
        }

        return new LengthAwarePaginator($published, $published->count(), 24, request('page', 1), []);
    }

    /**
     * Find by URL
     *
     * @param  string $url
     * @param  string $type
     *
     * @return Object|null
     */
    public function findByUrl($url, $type)
    {
        $item = $this->model->where('entity_type', $type)->where('entity_data->url', $url)->first();

        if ($item &&
            ($item->data->is_published == 1 || $item->data->is_published == 'on') &&
            $item->data->published_at <= Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s')
        ) {
            return $item->data;
        }

        return null;
    }

    /**
     * Find an entity by its Id
     *
     * @param  integer $entityId
     * @param  string $entityType
     *
     * @return Object|null
     */
    public function findByEntityId($entityId, $entityType)
    {
        $item = $this->model->where('entity_type', $entityType)->where('entity_id', $entityId)->first();

        if ($item && ($item->data->is_published == 1 || $item->data->is_published == 'on') && $item->data->published_at <= Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s')) {
            return $item->data;
        }

        return null;
    }

    /**
     * Get entities by type and language
     *
     * @param  string $lang
     * @param  string $type
     *
     * @return Illuminate\Support\Collection
     */
    public function getEntitiesByTypeAndLang($lang, $type)
    {
        $entities = collect();
        $collection = $this->model->where('entity_type', $type)->where('entity_data->lang', $lang)->get();

        foreach ($collection as $item) {
            $instance = app($item->type)->attributes = $item->data;
            $entities->push($instance);
        }

        return $entities;
    }
}
