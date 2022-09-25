<?php

declare(strict_types=1);

namespace App\Command;

use App\Form\TilePlannerType;
use App\TilePlanner\TilePlannerFacade;
use App\TilePlanner\Models\TilePlanInput;
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

        print json_encode($tilePlan);

        return self::SUCCESS;
    }
}