<?php

namespace Tnt\Faq\Repository;

use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Criteria\Equals;
use Tnt\Dbi\Criteria\GreaterThan;
use Tnt\Dbi\Criteria\IsNull;
use Tnt\Dbi\Criteria\IsTrue;
use Tnt\Dbi\Criteria\LessThan;
use Tnt\Dbi\Criteria\NotEquals;
use Tnt\Dbi\Criteria\OrderBy;
use Tnt\Faq\Contracts\FaqCategoryRepositoryInterface;
use Tnt\Faq\Model\FaqCategory;

class FaqCategoryRepository extends BaseRepository implements FaqCategoryRepositoryInterface
{
    /**
     * @var string
     */
    protected $model = FaqCategory::class;

    /**
     * Initial method
     */
    public function init()
    {
        $this->addCriteria(new OrderBy('sort_index'));
        $this->addCriteria(new OrderBy('sort_string'));

        parent::init();
    }

    /**
     * @return FaqCategoryRepositoryInterface
     */
    public function published(): FaqCategoryRepositoryInterface
    {
        $this->addCriteria(new IsTrue('is_visible'));

        return $this;
    }

    /**
     * @return FaqCategoryRepositoryInterface
     */
    public function root(): FaqCategoryRepositoryInterface
    {
        $this->addCriteria(new IsNull('parent'));

        return $this;
    }

    /**
     * @param FaqCategory $category
     * @return FaqCategoryRepositoryInterface
     */
    public function siblings(FaqCategory $category): FaqCategoryRepositoryInterface
    {
        $this->addCriteria(new Equals('parent', $category->parent));
        $this->addCriteria(new NotEquals('id', $category));

        return $this;
    }

    /**
     * @param FaqCategory $category
     * @return FaqCategoryRepositoryInterface
     */
    public function children(FaqCategory $category): FaqCategoryRepositoryInterface
    {
        $this->addCriteria(new Equals('parent', $category));

        return $this;
    }

    /**
     * @param FaqCategory $category
     * @return FaqCategoryRepositoryInterface
     */
    public function prev(FaqCategory $category): FaqCategoryRepositoryInterface
    {
        if ($category->parent) {
            $this->siblings($category);
        } else {
            $this->root();
        }

        $this->addCriteria(new LessThan('sort_index', $category->sort_index));
        $this->addCriteria(new OrderBy('sort_index', 'DESC'));
        $this->addCriteria(new OrderBy('sort_string', 'DESC'));

        return $this;
    }

    /**
     * @param FaqCategory $category
     * @return FaqCategoryRepositoryInterface
     */
    public function next(FaqCategory $category): FaqCategoryRepositoryInterface
    {
        if ($category->parent) {
            $this->siblings($category);

        } else {
            $this->root();
        }

        $this->addCriteria(new NotEquals('id', $category->id));
        $this->addCriteria(new GreaterThan('sort_index', $category->sort_index));

        return $this;
    }
}