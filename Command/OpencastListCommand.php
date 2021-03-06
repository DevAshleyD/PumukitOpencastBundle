<?php

namespace Pumukit\OpencastBundle\Command;

use Pumukit\SchemaBundle\Document\MultimediaObject;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OpencastListCommand extends ContainerAwareCommand
{
    private $clientService;
    private $dm;

    protected function configure()
    {
        $this
            ->setName('pumukit:opencast:list')
            ->setDescription('List imported or not mediapackages on PuMuKIT')
            ->setHelp(
                <<<'EOT'

            Show not imported mediaPackages on PuMuKIT

            Example:
            php app/console pumukit:opencast:list
EOT
            )
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->clientService = $this->getContainer()->get('pumukit_opencast.client');
        $this->dm = $this->getContainer()->get('doctrine_mongodb.odm.document_manager');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$total, $mediaPackages] = $this->clientService->getMediaPackages([], 0, 0);

        $output->writeln('Total - '.$total);

        foreach ($mediaPackages as $mediaPackage) {
            $multimediaObject = $this->dm->getRepository(MultimediaObject::class)->findOneBy([
                'properties.opencast' => $mediaPackage['id'],
            ]);

            if (!$multimediaObject) {
                $output->writeln('MediaPackage - <info>'.$mediaPackage['id'].'</info>');
            }
        }
    }
}
