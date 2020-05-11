<?php

namespace Progrupa\FacebookAudienceBundle\Command;

use Progrupa\FacebookAudienceBundle\Exception\ProgrupaFacebookAudienceException;
use Progrupa\FacebookAudienceBundle\Exporter\AudienceExporter;
use Progrupa\FacebookAudienceBundle\Exporter\EmailLoaderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractAudienceExportCommand extends \Symfony\Component\Console\Command\Command
{
    /** @var AudienceExporter */
    private $exporter;
    /** @var EmailLoaderInterface */
    private $loader;
    /** @var LoggerInterface */
    private $logger;

    /**
     * AbstractAudienceExportCommand constructor.
     * @param AudienceExporter $exporter
     * @param EmailLoaderInterface $loader
     * @param LoggerInterface $logger
     */
    public function __construct(AudienceExporter $exporter, EmailLoaderInterface $loader, LoggerInterface $logger)
    {
        $this->exporter = $exporter;
        $this->loader = $loader;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Export email list as bussines audience to Facebook')
            ->addOption('audience', 'a', InputOption::VALUE_REQUIRED, 'Exported audience ID')
        ;
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->logger->notice("Starting...");

        $audience = $input->getOption('audience');
        $emails = $this->loader->loadEmails($audience);
        $this->logger->notice(sprintf("Found %d emails to export", count($emails)));

        try {
            $this->exporter->exportAudience($audience, $emails);
            $this->logger->notice(sprintf("Exported %d emails to %s audience", count($emails), $audience));

        } catch (ProgrupaFacebookAudienceException $exception) {
            $this->logger->error(sprintf("Audience export failed: %s", $exception->getMessage()));
        }

        $this->logger->notice("Done!");
    }
}