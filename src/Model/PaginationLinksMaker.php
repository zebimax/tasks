<?php

namespace Ajax\Tasks\Model;

class PaginationLinksMaker
{
    /** @var string */
    private $disabledElement = ' disabled';

    /** @var string */
    private $prevElement     = '&laquo;';

    /** @var string */
    private $activeElement   = ' active';

    /** @var string */
    private $nextElement     = '&raquo;';

    /**
     * @param int $count
     * @param int $current
     * @param int $byPage
     * @param int $pageLinks
     *
     * @return array
     */
    public function __invoke($count, $current, $byPage, $pageLinks = 5)
    {
        if ($count <= $byPage || $byPage < 1) {
            return $this->getOnePagePagination();
        }
        $links        = [];
        $countPages   = ceil($count / $byPage);
        $startPage    = (int)floor(($current - 1) / $pageLinks) * $pageLinks + 1;
        $endBlockPage = $startPage + $pageLinks - 1;
        $endPage      = $endBlockPage > $countPages ? $countPages : $endBlockPage;
        $disabledPrev = $current > 1 ? '' : $this->disabledElement;
        $linkIdPrev   = $current > 1 ? $current - 1 : 1;
        $links[]      = $this->getLink($linkIdPrev, $this->prevElement, $disabledPrev);
        for ($page = $startPage; $page <= $endPage; $page++) {
            $active  = $page === $current ? $this->activeElement : '';
            $links[] = $this->getLink($page, $page, '', $active);
        }
        $disabledNext = $page <= $countPages ? '' : $this->disabledElement;
        $linkIdNext   = $page <= $countPages ? $page : $page - 1;
        $links[]      = $this->getLink($linkIdNext, $this->nextElement, $disabledNext);

        return $links;
    }

    /**
     * @param string $linkId
     * @param string $linkShow
     * @param string $disabled
     * @param string $active
     * @param string $additional
     *
     * @return array
     */
    private function getLink(
        $linkId = '',
        $linkShow = '',
        $disabled = '',
        $active = '',
        $additional = ''
    ) {
        return [
            'disabled'   => $disabled,
            'active'     => $active,
            'additional' => $additional,
            'link_id'    => $linkId,
            'link_show'  => $linkShow
        ];
    }

    /**
     * @return array
     */
    private function getOnePagePagination()
    {
        return [
            $this->getLink(1, $this->prevElement, $this->disabledElement),
            $this->getLink(1, 1, false, $this->activeElement),
            $this->getLink(1, $this->nextElement, $this->disabledElement)
        ];
    }
}
