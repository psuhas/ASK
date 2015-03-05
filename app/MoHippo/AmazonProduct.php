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
            'ResponseGroup' => 'Images,ItemAttributes,Offers',
            'SearchIndex' => 'All',
            'Service' => 'AWSECommerceService',
            'Timestamp'=>gmdate("Y-m-d\TH:i:s\Z"), //'2016-03-06T12:58:47.000Z',//'2015-03-06T12%3A58%3A47.000Z'
            'Version' => '2011-08-01',
            //'Signature' => //'LX1th3X0NfA9fiRE+rSm9LcYOZkPlZl/3DJeUv8PSmg=',    // expries on 6th March 2016
            'ItemPage'=>3,
        );

    }//__construct

    public function search()
    {
        //$request = $this->prepare_request();

        $url  = "GET\nwebservices.amazon.com\n/onca/xml\n";
        ksort($this->default_param);
        $url .= http_build_query($this->default_param);
        $url = str_replace('+', '%20', $url);
        var_dump($url);
        $sig = (base64_encode(hash_hmac('sha256', $url, $_ENV['AWS_ACCESS_SECRET'], true)));

        var_dump($sig);
        $this->default_param['Signature'] = $sig;

        //dd();
        //var_dump("http://webservices.amazon.com/onca/xml?AWSAccessKeyId=AKIAIM37SKEJ6YF4HVNQ&AssociateTag=mohippocom-20&Keywords=book&Operation=ItemSearch&ResponseGroup=Images%2CItemAttributes%2COffers&SearchIndex=All&Service=AWSECommerceService&Timestamp=2016-03-06T12%3A58%3A47.000Z&Version=2011-08-01&Signature=LX1th3X0NfA9fiRE%2BrSm9LcYOZkPlZl%2F3DJeUv8PSmg%3D");

        $request = $this->url.http_build_query($this->default_param);
        var_dump($request);
        //dd($request);
        //$body = ['ItemPage'=>100];

        $response = simplexml_load_file($request);
        //$client = new Client();
        //$response = $client->get($request);
        dd($response);
        //$response = file_get_contents($request);
        //$parsed_xml = simplexml_load_string($response);
        //dd($parsed_xml);

    }//search

    private function prepare_request3()
    {
        $SecretAccessKey = $_ENV['AWS_ACCESS_SECRET'];
        $request['AWSAccessKeyId'] = $_ENV['AWS_ACCESS_KEY'];
        $request['AssociateTag'] = $_ENV['AWS_ASSOCIATE_TAG'];
        $request['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");
        $request['ResponseGroup'] = "ItemAttributes,Offers,Images";
        // $request['ItemPage'] = 1;
        $request['Service'] = 'AWSECommerceService';
        $request['Version'] = '2011-08-01';
        $request['Operation'] = 'ItemSearch';
        $request['SearchIndex'] = 'All';
        $request['Keywords'] = 'book';
        //$request['Page'] = 5;

        ksort($request); // Sorts in order of key

        $Prepend = "GET\nwebservices.amazon.com\n/onca/xml\n";
        $String = http_build_query($request);
        $PrependString = str_replace('+', '%20', $Prepend . $String);

        $Signature = base64_encode(hash_hmac("sha256", $PrependString, $SecretAccessKey, True));
        $Signature = urlencode($Signature);

        $BaseUrl = "http://webservices.amazon.com/onca/xml?";
        $SignedRequest = $BaseUrl . $String . "&Signature=" . $Signature;

        return $SignedRequest;

    }
    private function prepare_request()
    {
        $base_url = $this->url; //"http://ecs.amazonaws.com/onca/xml?";
        /*
         * $url_params = array('Operation'=>"ItemSearch",'Service'=>"AWSECommerceService",
        'AWSAccessKeyId'=>$AWS_ACCESS_KEY_ID,'AssociateTag'=>"yourtag-10",
        'Version'=>"2006-09-11",'Availability'=>"Available",'Condition'=>"All",
        'ItemPage'=>"1",'ResponseGroup'=>"Images,ItemAttributes,EditorialReview",
        'Keywords'=>"Amazon");
        */
        $url_params = $this->default_param;

        // Add the Timestamp
        $url_params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());

        // Sort the URL parameters
        $url_parts = array();
        foreach(array_keys($url_params) as $key)
        $url_parts[] = $key."=".$url_params[$key];
        sort($url_parts);

        // Construct the string to sign
        $string_to_sign = "GET\necs.amazonaws.com\n/onca/xml\n".implode("&",$url_parts);
        $string_to_sign = str_replace('+','%20',$string_to_sign);
        $string_to_sign = str_replace(':','%3A',$string_to_sign);
        $string_to_sign = str_replace(';',urlencode(';'),$string_to_sign);

        // Sign the request
        $signature = hash_hmac("sha256",$string_to_sign,$_ENV['AWS_ACCESS_SECRET'],TRUE);

        // Base64 encode the signature and make it URL safe
        $signature = base64_encode($signature);
        $signature = str_replace('+','%2B',$signature);
        $signature = str_replace('=','%3D',$signature);

        $url_string = implode("&",$url_parts);
        $url = $base_url.$url_string."&Signature=".$signature;
        return $url;
    }//prepare_request

}//AmazonProduct