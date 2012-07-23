<?php

namespace Dark\RedisListBundle\Pagination;

use Predis\Client;
use Symfony\Component\HttpFoundation\Request;
use Dark\RedisListBundle\Collector\Collector;

class Pagination extends Iterator
{
    private $params;
    private $client;
    private $collector;
    private $templating;
    private $template;
    private $request;

    public function __construct(Client $client, Collector $collector, $templating, $request, $template)
    {
        $this->client = $client;
        $this->collector = $collector;
        $this->templating = $templating;
        $this->request = $request;
        $this->template = $template;
        $this->params = array();
    }

    public function paginate($hashName, $page = 1, $perPage = 15, $pageRange = 10, $route = null)
    {
        $count = $this->client->hlen($hashName);
        $from = $count - $page * $perPage;
        $to = $from + $pageRange;

        $range = range($from, $to);
        $elements = $this->client->hmget($hashName, $range);

        if (!is_null($route)) {
            $this->params['route'] = $route;
        } else {
            $this->params['route'] = $this->request->get("_route");
        }

        $this->params['current'] = $page;
        $this->params['pageRange'] = $pageRange;
        $this->params['pageCount'] = round($count / $perPage);
        $this->params['perPage'] = $perPage;
        $this->elements = $this->collector->process($elements);
    }

    public function render()
    {
        $params = $this->params;

        if ($params['current'] > 1) {
            $params['prev'] = $params['current'] - 1;
        }
        if ($params['current'] < $params['pageCount']) {
            $params['next'] = $params['current'] + 1;
        }

        $parts = round($params['pageRange'] / 2, 0, PHP_ROUND_HALF_EVEN);

        $firstPage = $params['current'] - $parts;
        $lastPage = ($params['current'] + $parts) > $params['pageCount'] ? $params['pageCount'] : $params['current'] + $parts;

        if ($firstPage <= 0) {
            $lastPage += abs($firstPage);
            $firstPage = 1;
        } elseif ($params['pageCount'] - $lastPage < $parts) {
            $firstPage -= $params['current'] + $parts - $lastPage;
        }

        for ($i = $firstPage; $i <= $lastPage; $i++) {
            $params['pagesInRange'][] = $i;
        }

        return $this->templating->render($this->template, $params);
    }
}