<?php


namespace Progrupa\FacebookAudienceBundle\Exporter;


interface EmailLoaderInterface
{
    /**
     * Loads emails for an audience
     * @param string $audienceId
     * @return array
     */
    public function loadEmails(string $audienceId);
}