<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Paginator;

use Ig0rbm\Memo\Entity\Paginator\Page;

use function in_array;

class Paginator
{
    public const DEFAULT_ITEMS_PER_PAGE = 10;

    public const FIRST_PAGE_ACTION = '<<';
    public const LAST_PAGE_ACTION  = '>>';
    public const NEXT_PAGE_ACTION  = '>';
    public const PREV_PAGE_ACTION  = '<';

    public const ACTIONS = [
        self::FIRST_PAGE_ACTION,
        self::LAST_PAGE_ACTION,
        self::NEXT_PAGE_ACTION,
        self::PREV_PAGE_ACTION,
    ];

    public static function isAction (string $action): bool
    {
        return in_array($action, self::ACTIONS);
    }

    public function do(string $action, Page $page): Page
    {
        if ($action === self::FIRST_PAGE_ACTION) {
            return $page->turnToFirstPage();
        }

        if ($action === self::LAST_PAGE_ACTION) {
            return $page->turnToLastPage();
        }

        return $page;
    }
}
