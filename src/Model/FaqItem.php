<?php

namespace Tnt\Faq\Model;

use dry\orm\special\Boolean;

class FaqItem extends \dry\orm\Model
{
    const TABLE = 'faq_item';

    public static $special_fields = [
        'faq_category' => FaqCategory::class,
        'is_visible' => Boolean::class,
    ];
}