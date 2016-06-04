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

namespace Smatyas\GCloudManager\Command\Compute;

use Knp\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstancesListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('compute:instances:list')
            ->setDescription('Lists the compute instances.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: these should be read from the config json, but they are not set by the Google_Client::setAuthConfig.
        $project = 'hackathon-1328';
        $zone = 'europe-west1-d';

        $client = new \Google_Client([]);
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');
        $client->setAuthConfig('credentials/hackathon-c87ef1f28578.json');

        $computeService = new \Google_Service_Compute($client);
        $instances = $computeService->instances->listInstances($project, $zone);

        $table = new Table($output);
        $table->setHeaders(['ID', 'Name', 'Status']);
        foreach ($instances as $instance) {
            if ($instance instanceof \Google_Service_Compute_Instance) {
                $table->addRow([
                    $instance->getId(),
                    $instance->getName(),
                    $instance->getStatus(),
                ]);
            } else {
                throw new \Exception('Unexpected instance: ' . get_class($instance));
            }
        }
        $table->render();
    }
}
