<?php

namespace AppBundle\Command;

use AppBundle\DBAL\Types\VideoRecordingType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RecordingTypeSetCommand
 */
class RecordingTypeSetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('violation:recording-type:set')
            ->setDescription('Set recording type to violation, without him');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em  = $this->getContainer()->get('doctrine.orm.entity_manager');

        $violations = $em->getRepository('AppBundle:Violation')->findBy(['recordingType' => null]);
        foreach ($violations as $violation) {
            if ($violation->getAuthor() !== null) {
                $violation->setRecordingType(VideoRecordingType::RECORDER);
            } else {
                $violation->setRecordingType(VideoRecordingType::UPLOAD);
            }
        }

        $em->flush();
    }
}
