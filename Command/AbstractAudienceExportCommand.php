<?php

namespace Progrupa\FacebookAudienceBundle\Command;

use Progrupa\FacebookAudienceBundle\Exception\ProgrupaFacebookAudienceException;
use Progrupa\FacebookAudienceBundle\Exporter\AudienceExporter;
use Progrupa\FacebookAudienceBundle\Exporter\DataLoaderInterface;
use Progrupa\FacebookAudienceBundle\Exporter\EmailLoaderInterface;
use Progrupa\FacebookAudienceBundle\Exporter\MultiDataLoaderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractAudienceExportCommand extends \Symfony\Component\Console\Command\Command
{
    /** @var AudienceExporter */
    private $exporter;
    /** @var DataLoaderInterface */
    private $loader;
    /** @var LoggerInterface */
    private $logger;

    /**
     * AbstractAudienceExportCommand constructor.
     * @param AudienceExporter $exporter
     * @param DataLoaderInterface $loader
     * @param LoggerInterface $logger
     */
    public function __construct(AudienceExporter $exporter, DataLoaderInterface $loader, LoggerInterface $logger)
    {
        $this->exporter = $exporter;
        $this->loader = $loader;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Export user list as bussines audience to Facebook')
            ->addOption('audience', 'a', InputOption::VALUE_REQUIRED, 'Exported audience ID')
        ;
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->logger->notice("Starting...");

        $audience = $input->getOption('audience');

        $data = $this->loader->loadData($audience);
        $type = $this->loader->getType();

        $this->logger->notice(sprintf("Found %d users to export", count($data)));

        try {
            $this->exporter->exportAudience($audience, $data, $type);
            $this->logger->notice(sprintf("Exported %d users to %s audience", count($data), $audience));

        } catch (ProgrupaFacebookAudienceException $exception) {
            $this->logger->error(sprintf("Audience export failed: %s", $exception->getMessage()));
        }

        $this->logger->notice("Done!");
    }
}