<?php

namespace Tnt\Faq\Admin;

use dry\admin\component\BooleanEdit;
use dry\admin\component\I18nSwitcher;
use dry\admin\component\RichtextEdit2;
use dry\admin\component\SortHandle;
use dry\admin\component\Stack;
use dry\admin\component\StringEdit;
use dry\admin\component\StringView;
use dry\orm\action\Create;
use dry\orm\action\Delete;
use dry\orm\action\Edit;
use dry\orm\Index;
use dry\orm\IndexRow;
use dry\orm\sort\DragSorter;
use Tnt\Faq\Model\FaqItem;

class FaqItemManager extends \dry\orm\Manager
{
    public function __construct(array $languages)
    {
        parent::__construct(FaqItem::class, [
            'title' => 'item',
            'singular' => 'item',
            'plural' => 'items',
            'id' => 'faq-item',
        ] );

        $contentComponents = [];

        foreach ($languages as $language) {

            $contentComponents[$language] = [
                new StringEdit('question_'.$language, [
                    'v8n_required' => true,
                    'label' => 'question'
                ]),
                new RichtextEdit2('answer_'.$language, [
                    'label' => 'answer'
                ]),
            ];
        }

        $contentComponentsContainer = new Stack(Stack::VERTICAL, $contentComponents[$languages[0]]);

        if (count($languages) > 1) {
            $contentComponentsContainer = new I18nSwitcher($contentComponents);
        }

        $this->actions[] = $create = new Create([
            new Stack(Stack::VERTICAL, [
                $contentComponentsContainer,
                new BooleanEdit('is_visible'),
            ]),
        ], [
            'popup' => true
        ]);

        $this->actions[] = $edit = new Edit($create->components, [
            'mode' => Create::MODE_SIDEPANEL
        ]);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add item');

        $this->index = new Index([
            new SortHandle(),
            new StringView('question_nl'),
            $edit->create_link(),
            $delete->create_link(),
        ], [
            'field_to_row_class' => [
                'is_visible', null, IndexRow::STYLE_DISABLED
            ]
        ]);

        $this->index->sorter = new DragSorter('sort_index');
    }
}
