<?php
/**
 * This file is part of the gcloud-manager package.
 *
 * (c) Mátyás Somfai <somfai.matyas@gmail.com>
 * Created at 2016.06.04.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smatyas\GCloudManager\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var \Google_Service_Compute
     */
    protected $computeService;

    /**
     * @return string
     *
     * TODO: this should be read from the config json, but they are not set by the Google_Client::setAuthConfig.
     */
    protected function getProject()
    {
        $project = 'hackathon-1328';
        return $project;
    }

    /**
     * @return string
     *
     * TODO: this should be read from the config json, but they are not set by the Google_Client::setAuthConfig.
     */
    protected function getZone()
    {
        $zone = 'europe-west1-d';
        return $zone;
    }

    /**
     * @return \Google_Client
     */
    protected function getGoogleClient()
    {
        $client = new \Google_Client([]);
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');
        $client->setAuthConfig('credentials/hackathon-c87ef1f28578.json');
        return $client;
    }

    /**
     * @param $client
     * @return \Google_Service_Compute
     */
    protected function getGoogleComputeService($client = null)
    {
        if (null !== $this->computeService) {
            return $this->computeService;
        }

        if (null === $client) {
            $client = $this->getGoogleClient();
        }
        $this->computeService = new \Google_Service_Compute($client);
        
        return $this->computeService;
    }

    /**
     * @param OutputInterface $output
     * @param $operations
     */
    protected function waitForZoneOperations(OutputInterface $output, $operations)
    {
        $operationsRunning = true;
        while ($operationsRunning) {
            sleep(1);
            for ($i = 0; $i < count($operations); $i++) {
                $output->write("\033[1A"); // step back to the first progress line
            }
            $operationsRunning = false;
            foreach ($operations as $operationName => $progressBar) {
                /** @var ProgressBar $progressBar */
                try {
                    /** @var \Google_Service_Compute_Operation $operation */
                    $operation = $this->getGoogleComputeService()->zoneOperations->get(
                        $this->getProject(),
                        $this->getZone(),
                        $operationName
                    );

                    $status = $operation->getStatus();
                    $progressBar->setMessage($status, 'status');
                    if ('DONE' === $status) {
                        $progressBar->finish();
                    } else {
                        $progressBar->setMessage($operation->getProgress(), 'percent');
                        $progressBar->setProgress($operation->getProgress());
                        $operationsRunning = true;
                    }
                    $output->writeln('');
                } catch (\Google_Service_Exception $e) {
                    // The operation may be not accessible yet.
                    $output->writeln($e->getMessage());
                    $operationsRunning = true;
                }
            }
        }
    }
}
