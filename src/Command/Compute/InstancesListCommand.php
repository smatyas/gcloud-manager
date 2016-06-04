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
        $output->writeln('TODO');
    }
}
