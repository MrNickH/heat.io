<?php

class Pagination
{

    public $itemName;
    public $perpage;
    public $pageCount;
    public $totalitems;
    public $page;
    public $first;
    public $last;
    public $count;

    public function __construct(String $itemName, int $totalItems, $currentPage = '', $perpage = 15)
    {

        $this->itemName = $itemName;

        $this->totalitems = $totalItems;
        $this->page = (empty($currentPage) || !$currentPage) ? ($_GET['page'] && is_numeric($_GET['page']))? $_GET['page'] : 1 : $currentPage;

        $this->perpage = $perpage ?? 15;
        $this->pageCount = ceil($this->totalitems / $this->perpage) ?: 1;
        $this->page = $this->page == 'last' || $this->page > $this->pageCount ? $this->pageCount : $this->page;

        $this->first = $totalItems > 0 ? (($this->page - 1 ) * $this->perpage) + 1 : 0;
        $this->last = ($this->page * $this->perpage) >= $this->totalitems ? $this->totalitems : ($this->page * $this->perpage);
        $this->count =  $totalItems > 0 ? ($this->last + 1) - $this->first : 0;
    }

    public function getOutput()
    {
        return \View::partialView('Partial/common/pagination',
            [
                'item' => $this->itemName,
                'page' => $this->page,
                'totalcount' => $this->totalitems,
                'pageCount' => $this->pageCount,
                'first' => $this->first,
                'last' => $this->last,
                'count' => $this->count,
                'info' => true,
                'pages' => true

            ]
        );
    }

    public function getPagesOnly()
    {
        return \View::partialView('Partial/common/pagination',
            [
                'item' => $this->itemName,
                'page' => $this->page,
                'totalcount' => $this->totalitems,
                'pageCount' => $this->pageCount,
                'first' => $this->first,
                'last' => $this->last,
                'count' => $this->count,
                'info' => false,
                'pages' => true

            ]
        );
    }

    public function getInfoOnly()
    {
        return \View::partialView('Partial/common/pagination',
            [
                'item' => $this->itemName,
                'page' => $this->page,
                'totalcount' => $this->totalitems,
                'pageCount' => $this->pageCount,
                'first' => $this->first,
                'last' => $this->last,
                'count' => $this->count,
                'info' => true,
                'pages' => false
            ]
        );
    }

    public function getIndexesForPage()
    {
        return array_keys(array_fill($this->first - 1, $this->count, true));
    }
}