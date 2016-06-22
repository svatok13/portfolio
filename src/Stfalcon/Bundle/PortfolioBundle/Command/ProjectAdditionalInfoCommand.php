<?php

namespace Stfalcon\Bundle\PortfolioBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectAdditionalInfoCommand extends ContainerAwareCommand
{
    /**
    * {@inheritDoc}
    */
    protected function configure()
    {
        $this
            ->setName('portfolio:projects:fill-additional-info')
            ->setDescription('Fill additional info for projects, where it is empty')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> fill additional info for projects, where it is empty.
HELP
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $projects = $em->getRepository('StfalconPortfolioBundle:Project')->findBy(['additionalInfo' => '']);

        foreach ($projects as $project) {
            $additionalInfo = <<<INFO
<h1>{$project->getName()}</h1></br>
<a href="{$project->getUrl()}" rel="nofollow" target="_blank" class="project-link">{$project->getUrl()}</a>
INFO;
            $project->setAdditionalInfo($additionalInfo);
        }

        $em->flush();
    }
}