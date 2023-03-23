<?php

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\Menu\Config as MenuConfig;
use Magento\Framework\UrlInterface;
use Magento\Framework\DataObject;

class Menu extends DataObject
{
    /**
     * @var MenuConfig
     */
    private $menuConfig;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var string
     */
    private $delimiter = ' → ';

    /**
     * Menu constructor.
     *
     * @param MenuConfig $menuConfig
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        MenuConfig $menuConfig,
        UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($data);
        $this->menuConfig = $menuConfig;
        $this->url = $url;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }
        $foundItems = $this->search($this->getQuery());

        foreach ($foundItems as $key => $value) {
            $names = explode('::', $value['line']);
            if (isset($names[1])) {
                $foundItems[$key]['subtitle'] = "Menu: ". $names[0] .' > ' .$names[1];
            }
        }

        $this->setResults($foundItems);

        return $this;
    }

    /**
     * @param $q
     *
     * @return array
     * @throws \Exception
     */
    private function search($q)
    {
        $queryParts = explode(' ', $q);
        $menuItems = $this->getFlatStructure();
        $foundItems = array_filter($menuItems, function($arr) use ($queryParts) {
            foreach ($queryParts as $part) {
                if (stripos($arr['name'], $part) === false) {
                    return false;
                }
            }
            return true;
        });
        return $foundItems;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getFlatStructure()
    {
        /** @var \Magento\Backend\Model\Menu $menu */
        $menu = $this->menuConfig->getMenu();

        $result = [];
        $collector = [];
        $resultCount = 0;
        $recursive = function($n, $collector) use (&$recursive, &$result, &$resultCount) {
            foreach ($n as $item) {
                if (!$item->isAllowed() || $item->getId() == 'Magento_Marketplace::partners') {
                    continue;
                }
                if ($item->hasChildren()) {
                    $recursive($item->getChildren(), array_merge($collector, [(string)$item->getTitle()]));
                } else {
                    $result[$resultCount] = [
                        'line' => implode('::', array_filter(array_merge($collector, [(string)$item->getTitle()]))),
                        'id' => $item->getTitle(),
                        'name' => $item->getTitle(),
                        'subtitle' => '',
                        'type' => 'Menu',
                        'extraInfo' => [],
                        'url' => $this->url->getUrl($item->getAction())
                    ];
                    $resultCount++;
                }
            }
        };
        $recursive($menu, $collector);
        return $result;
    }
}