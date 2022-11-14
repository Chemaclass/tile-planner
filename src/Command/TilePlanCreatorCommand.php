<?php

declare(strict_types=1);

namespace TilePlanner\Command;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\TilePlannerFacade;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use Gacela\Framework\DocBlockResolverAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method TilePlannerFacade getFacade()
 */
final class TilePlanCreatorCommand extends Command
{
    use DocBlockResolverAwareTrait;

    protected static $defaultName = 'app:create-layer-plan';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formHelper = $this->getHelper('form');
        $formData = $formHelper->interactUsingForm(TilePlannerType::class, $input, $output);
        $tileInput = TilePlanInput::fromData($formData);

        $tilePlan = $this->getFacade()->createPlan($tileInput);

        $fileContent = json_encode($tilePlan, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $output->writeln($fileContent);

        return self::SUCCESS;
    }
}