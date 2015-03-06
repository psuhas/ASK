<?php

namespace MoHippo;

use GuzzleHttp\Client;


class AmazonProduct {

    private $url = "http://webservices.amazon.com/onca/xml?";//Signature=LX1th3X0NfA9fiRE%2BrSm9LcYOZkPlZl%2F3DJeUv8PSmg%3D&Timestamp=2016-03-06T12:58:47.000Z";
    private $default_param = array();

    function __construct()
    {
        $this->default_param = array(
            'AWSAccessKeyId' => $_ENV['AWS_ACCESS_KEY'],
            'AssociateTag' => $_ENV['AWS_ASSOCIATE_TAG'],
            'Keywords' => 'book',
            'Operation' => 'ItemSearch',
            'ResponseGroup' => 'Images,ItemAttributes',
            'SearchIndex' => 'All',
            'Service' => 'AWSECommerceService',
            'Timestamp'=>gmdate("Y-m-d\TH:i:s\Z"),
            'Version' => '2011-08-01',
            'ItemPage'=>1,
        );

    }//__construct

    public function search($Keywords = 'electronics',$page = 1)
    {
        $ret = array();

        $this->default_param['Keywords'] = $Keywords;
        $this->default_param['ItemPage'] = $page;


        $url  = "GET\nwebservices.amazon.com\n/onca/xml\n";

        ksort($this->default_param);

        $url .= http_build_query($this->default_param);
        $url = str_replace('+', '%20', $url);
        //var_dump($url);

        $sig = (base64_encode(hash_hmac('sha256', $url, $_ENV['AWS_ACCESS_SECRET'], true)));

        //var_dump($sig);
        $this->default_param['Signature'] = $sig;


        $request = $this->url.http_build_query($this->default_param);
        //var_dump($request);

        $response = simplexml_load_file($request);

        if($response->Items->Request->IsValid != true)
        {
            return false;
        }

        foreach($response->Items->Item as $v)
        {
            $ret[''.$v->ASIN] = array(
                'ASIN' => (string)$v->ASIN,
                'DetailPageURL' => (string)$v->DetailPageURL,
                'LargeImage' => (string)$v->MediumImage->URL,
                'FormattedPrice' => (string)$v->ItemAttributes->ListPrice->FormattedPrice,
                'Title' => (string)$v->ItemAttributes->Title,
            );

        }

        //dd($response);
        //dd($ret);
        return $ret;


    }//search



}//AmazonProduct