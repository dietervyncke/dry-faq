<?php

namespace Tnt\Faq\Admin;

use Tnt\Faq\Model\FaqCategory;
use dry\admin\component\BooleanEdit;
use dry\admin\component\I18nSwitcher;
use dry\admin\component\RichtextEdit2;
use dry\admin\component\Stack;
use dry\admin\component\StringEdit;
use dry\orm\action\Create;
use dry\orm\action\Delete;
use dry\orm\action\Edit;
use dry\orm\component\InlineManager;
use dry\orm\component\NestedSortHandle;
use dry\orm\Index;
use dry\orm\IndexRow;
use dry\orm\sort\NestedDragSorter;

class FaqCategoryManager extends \dry\orm\Manager
{
    public function __construct(array $languages)
    {
        parent::__construct(FaqCategory::class, [
            'title' => 'Faq',
            'singular' => 'category',
            'plural' => 'categories',
            'id' => 'faq-category',
        ] );

        $generalComponents = [];
        $contentComponents = [];

        foreach ($languages as $language) {

            $generalComponents[$language] = [
                new StringEdit('title_'.$language, [
                    'v8n_required' => true,
                    'suggest_slug' => 'slug_'.$language,
                    'label' => 'title'
                ]),
                new StringEdit( 'slug_'.$language, [
                    'v8n_required' => true,
                    'handle_duplicate' => true,
                    'slugify_on_blur' => true,
                    'label' => 'slug'
                ]),
            ];

            $contentComponents[$language] = [
                new StringEdit('introduction_'.$language, [
                    'v8n_max_length' => 100,
                    'label' => 'introduction'
                ]),
                new RichtextEdit2('body_'.$language, [
                    'label' => 'body'
                ]),
            ];
        }

        $generalComponentsContainer = new Stack(Stack::VERTICAL, $generalComponents[$languages[0]]);
        $contentComponentsContainer = new Stack(Stack::VERTICAL, $contentComponents[$languages[0]]);

        if (count($languages) > 1) {
            $generalComponentsContainer = new I18nSwitcher($generalComponents);
            $contentComponentsContainer = new I18nSwitcher($contentComponents);
        }

        $this->actions[] = $create = new Create([
            $generalComponentsContainer,
            new BooleanEdit('is_visible'),
        ], [
            'popup' => true
        ]);

        $this->actions[] = $edit = new Edit([
            new Stack(Stack::HORIZONTAL, [
                new Stack(Stack::VERTICAL, [
                    $contentComponentsContainer,
                    new InlineManager(new FaqItemManager($languages), [
                        'restrict_by_foreign_key' => 'faq_category'
                    ]),
                ]),
                new Stack(Stack::VERTICAL, $create->components, [
                    'title' => 'Publication settings'
                ]),
            ], [
                'grid' => [5, 2]
            ]),
        ]);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add category');

        $this->index = new Index([
            new NestedSortHandle('title_nl'),
            $edit->create_link(),
            $delete->create_link(),
        ], [
            'field_to_row_class' => [
                'is_visible', null, IndexRow::STYLE_DISABLED
            ]
        ] );

        $this->index->sorter = new NestedDragSorter('parent', 'sort_index', 'sort_string');
    }
}
