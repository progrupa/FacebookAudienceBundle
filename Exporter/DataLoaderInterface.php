<?php


namespace Progrupa\FacebookAudienceBundle\Exporter;


interface DataLoaderInterface
{
    public function getType();
    
    /**
     * Loads data for an audience
     * @param string $audienceId
     * @return array
     */
    public function loadData(string $audienceId);
}