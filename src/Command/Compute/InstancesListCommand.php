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

use Smatyas\GCloudManager\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstancesListCommand extends AbstractCommand
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
        $project = $this->getProject();
        $zone = $this->getZone();
        $computeService = $this->getGoogleComputeService();

        $instances = $computeService->instances->listInstances($project, $zone);

        $table = new Table($output);
        $table->setHeaders(['Name', 'Status']);
        foreach ($instances as $instance) {
            if ($instance instanceof \Google_Service_Compute_Instance) {
                $table->addRow([
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
