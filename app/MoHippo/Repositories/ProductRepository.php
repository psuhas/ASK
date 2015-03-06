<?php

namespace MoHippo\Repositories;


use Illuminate\Contracts\Cache\Repository as Cache1;
use MoHippo\AmazonProduct;

class ProductRepository {

    private $repository;
    private $cache;
    private $keyword;

    function __construct(Cache1 $cache, AmazonProduct $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    public function paginate($keyword='', $page = 1 )
    {
        if($keyword !='' && $this->keyword != $keyword)
            $this->keyword = $keyword;

        $keyword = $this->keyword;

        return $this->cache->remember('product', 10, function() use($keyword,$page) {

            return $this->repository->search($keyword, $page);
        } );
    }//paginate
}