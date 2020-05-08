<?php

namespace Progrupa\FacebookAudienceBundle\Exporter;


use FacebookAds\Exception\Exception;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceCustomerFileSourceValues;
use FacebookAds\Object\Values\CustomAudienceSubtypeValues;
use FacebookAds\Object\Values\CustomAudienceTypes;
use Progrupa\FacebookAudienceBundle\Facebook\ApiInit;
use Psr\Log\LoggerInterface;

class AudienceExporter
{
    /** @var ApiInit */
    private $apiInit;
    /** @var string */
    private $businessId;
    /** @var array */
    private $audiences = [];
    /** @var LoggerInterface */
    private $logger;
    /** @var AdAccount */
    private $account;

    /**
     * AudienceExporter constructor.
     * @param ApiInit $apiInit
     * @param string $businessId
     */
    public function __construct(ApiInit $apiInit, $businessId, LoggerInterface $logger)
    {
        $this->apiInit = $apiInit;
        $this->businessId = $businessId;
        $this->logger = $logger;
    }

    public function exportAudience($audienceName, $emails)
    {
        $this->account = new AdAccount('act_' . $this->businessId);
        $audience = $this->fetchAudience($audienceName);

        if (! $audience) {
            throw new \InvalidArgumentException('Audience not found or could not be created, export aborted');
        }

        $audience->addUsers($emails, CustomAudienceTypes::EMAIL);
    }

    /**
     * @param $audienceId
     * @param $account
     * @return CustomAudience|null
     */
    protected function fetchAudience($audienceName)
    {
        $audience = array_key_exists($audienceName, $this->audiences) ? $this->audiences[$audienceName] : null;

        if (is_null($audience)) {
            try {
                $audiences = $this->account->getCustomAudiences(
                    [CustomAudienceFields::NAME, CustomAudienceFields::ID],
                    [CustomAudienceFields::NAME => $audienceName]
                );
            } catch (Exception $e) {
                //  Search failed, audience was probably deleted
            }
        }

        if (is_null($audience)) {
            try {
                $audience = $this->account->createCustomAudience(
                    [],
                    [
                        CustomAudienceFields::SUBTYPE => CustomAudienceSubtypeValues::CUSTOM,
                        CustomAudienceFields::NAME => $audienceName,
//                        CustomAudienceFields::DESCRIPTION => 'This is just a testing audience list, delete later',
                        CustomAudienceFields::CUSTOMER_FILE_SOURCE => CustomAudienceCustomerFileSourceValues::USER_PROVIDED_ONLY,
                    ]
                );
            } catch (Exception $e) {
                //  Fetch failed, audience was probably deleted
            }
        }

        return $audience;
    }
}