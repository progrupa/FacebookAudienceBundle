<?php

namespace Progrupa\FacebookAudienceBundle\Exporter;


use FacebookAds\Cursor;
use FacebookAds\Exception\Exception;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdRuleFilters;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\AdRuleFiltersFields;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Fields\CustomAudienceMultikeySchemaFields;
use FacebookAds\Object\Values\AdRuleFiltersOperatorValues;
use FacebookAds\Object\Values\CustomAudienceCustomerFileSourceValues;
use FacebookAds\Object\Values\CustomAudienceSubtypeValues;
use FacebookAds\Object\Values\CustomAudienceTypes;
use Progrupa\FacebookAudienceBundle\Exception\ProgrupaFacebookAudienceException;
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

    /**
     * @param $audienceName
     * @param $emails
     * @param string|array $type
     * @throws ProgrupaFacebookAudienceException
     */
    public function exportAudience($audienceName, $emails, $type)
    {
        $this->account = new AdAccount('act_' . $this->businessId);
        $audience = $this->fetchAudience($audienceName);

        if (! $audience) {
            throw new ProgrupaFacebookAudienceException('Audience not found or could not be created, export aborted');
        }

        if (is_array($type)) {
            $audience->addUsersMultiKey($emails, $type);
        }
        else {
            $audience->addUsers($emails, $type);
        }
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
                /** @var Cursor $audiencesCursor */
                $audiencesCursor = $this->account->getCustomAudiences([CustomAudienceFields::NAME, CustomAudienceFields::ID]);
                $audiencesCursor->setUseImplicitFetch(true);

                while ($audiencesCursor->valid()) {
                    $audienceData = $audiencesCursor->current()->getData();
                    if ($audienceData[CustomAudienceFields::NAME] == $audienceName) {
                        $audience = $audiencesCursor->current();
                        break;
                    }

                    $audiencesCursor->next();
                }
            } catch (Exception $e) {
                //  Search failed, audience was probably deleted
            }
        }

        if (is_null($audience)) {
            try {
                $audience = $this->account->createCustomAudience(
                    [CustomAudienceFields::NAME, CustomAudienceFields::ID],
                    [
                        CustomAudienceFields::SUBTYPE => CustomAudienceSubtypeValues::CUSTOM,
                        CustomAudienceFields::NAME => $audienceName,
//                        CustomAudienceFields::DESCRIPTION => 'This is just a testing audience list, delete later',
                        CustomAudienceFields::CUSTOMER_FILE_SOURCE => CustomAudienceCustomerFileSourceValues::USER_PROVIDED_ONLY,
                    ]
                );

                $this->audiences[$audienceName] = $audience;
            } catch (Exception $e) {
                //  Fetch failed, audience was probably deleted
            }
        }

        return $audience;
    }
}