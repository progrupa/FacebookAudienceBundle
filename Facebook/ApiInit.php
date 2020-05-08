<?php

namespace Progrupa\FacebookAudienceBundle\Facebook;


use FacebookAds\Api;

class ApiInit
{
    public function __construct($facebookClientId, $facebookClientSecret, $facebookMarketingToken)
    {
        Api::init($facebookClientId, $facebookClientSecret, $facebookMarketingToken);
    }

}