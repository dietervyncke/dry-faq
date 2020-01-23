<?php

namespace Tnt\Faq\Facade;

use Oak\Facade;
use Tnt\Faq\Contracts\FaqCategoryRepositoryInterface;

class FaqCategory extends Facade
{
    protected static function getContract(): string
    {
        return FaqCategoryRepositoryInterface::class;
    }
}