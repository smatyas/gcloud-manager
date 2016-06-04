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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstancesStopCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('compute:instances:stop')
            ->setDescription('Stops the compute instances.')
            ->addArgument(
                'instances',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The name of the instances to stop.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getProject();
        $zone = $this->getZone();
        $computeService = $this->getGoogleComputeService();

        $instances = $input->getArgument('instances');
        $operations = [];
        foreach ($instances as $instance) {
            $operation = $computeService->instances->stop($project, $zone, $instance);
            $progressBar = new ProgressBar($output, 100);
            $progressBar->setFormat("%operation% [%bar%] %percent:3s%% %status%");
            $progressBar->setMessage($operation->getName(), 'operation');
            $progressBar->setMessage('Starting operation...', 'status');
            $progressBar->start();
            $output->writeln('');
            $operations[$operation->getName()] = $progressBar;
        }

        $this->waitForZoneOperations($output, $operations);
    }
}
