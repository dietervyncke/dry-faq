<?php

namespace Tnt\Faq\Model;

use dry\orm\relationship\HasMany;
use dry\orm\special\Boolean;

class FaqCategory extends \dry\orm\Model
{
    const TABLE = 'faq_category';

    const SORT_STRING_PAD_LENGTH = 4;

    /**
     * @var array $special_fields
     */
    public static $special_fields = [
        'parent' => FaqCategory::class,
        'is_visible' => Boolean::class,
    ];

    /**
     * @return HasMany
     */
    public function get_questions(): HasMany
    {
        return $this->has_many(FaqItem::class, 'faq_category');
    }

    /**
     * @return HasMany
     */
    public function get_children(): HasMany
    {
        return $this->has_many(FaqCategory::class, 'parent');
    }

    /**
     * Delete category and related items
     */
    public function delete(): void
    {
        foreach($this->children as $c) {
            $c->delete();
        }

        foreach($this->questions as $q) {
            $q->delete();
        }

        parent::delete();
    }

    /**
     *  Updated nested items
     */
    public function update_sort_string()
    {
        $sorter = new \dry\orm\sort\NestedDragSorter('parent', 'sort_index', 'sort_string');
        $sorter->update_sort_string($this);
    }
}