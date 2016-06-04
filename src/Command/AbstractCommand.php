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
     */
    protected function getProject()
    {
        $credentials = $this->getCredentialsArray();
        $project = $credentials['project_id'];
        return $project;
    }

    /**
     * @return string
     */
    protected function getZone()
    {
        $zone = getenv('GOOGLE_CLOUD_ZONE');
        if (false === $zone) {
            throw new \Exception(
                'Google cloud zone must be set through the GOOGLE_CLOUD_ZONE environment variable.'
            );
        }
        return $zone;
    }

    /**
     * @return \Google_Client
     */
    protected function getGoogleClient()
    {
        $client = new \Google_Client([]);
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');

        $credentialsFile = $this->getCredentialsFile();
        $client->setAuthConfig($credentialsFile);
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
                    if ($progressBar->getProgress() == 100) {
                        $progressBar->finish();
                        $output->writeln('');
                        continue;
                    }

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

    /**
     * @return string
     * @throws \Exception
     */
    protected function getCredentialsFile()
    {
        $credentialsFile = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        if (false === $credentialsFile) {
            throw new \Exception(
                'Credentials file must be set through the GOOGLE_APPLICATION_CREDENTIALS environment variable.'
            );
        }
        return $credentialsFile;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getCredentialsArray()
    {
        $file = $this->getCredentialsFile();
        $contents = file_get_contents($file);
        $credentials = json_decode($contents, true);

        return $credentials;
    }
}
