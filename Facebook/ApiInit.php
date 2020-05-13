<?php

namespace Progrupa\FacebookAudienceBundle\Facebook;


use FacebookAds\Api;
use FacebookAds\CrashReporter;

class ApiInit
{
    public function __construct($facebookClientId, $facebookClientSecret, $facebookMarketingToken)
    {
        Api::init($facebookClientId, $facebookClientSecret, $facebookMarketingToken, false);
    }

}