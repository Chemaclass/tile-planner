<?php

namespace TilePlanner\Controller;

use Assert\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TilePlanner\Form\TilePlannerType;
use TilePlanner\Shared\StringToFloatConverter;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerFacadeInterface;

class TilePlannerController extends AbstractController
{
    public function __construct(
        private TilePlannerFacadeInterface $tilePlannerFacade,
        private StringToFloatConverter $stringToFloatConverter,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TilePlannerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $formData = $form->getData();
                $tileInput = new TilePlanInput(
                    Room::create(
                        $this->stringToFloatConverter->toFloat($formData['room_width']),
                        $this->stringToFloatConverter->toFloat($formData['room_depth']),
                    ),
                    Tile::create(
                        $this->stringToFloatConverter->toFloat($formData['tile_width']),
                        $this->stringToFloatConverter->toFloat($formData['tile_length']),
                    ),
                    new LayingOptions(
                        $this->stringToFloatConverter->toFloat($formData['min_tile_length'])
                    )
                );
            } catch (InvalidArgumentException $exception) {
                return $this->render(
                    'index.twig',
                    [
                        'tilePlannerInputForm' => $form->createView(),
                        'error' => 'Invalid input: '.$exception->getMessage(),
                    ]
                );
            }

            $plan = $this->tilePlannerFacade
                ->createPlan($tileInput)
            ;
        }

        return $this->render(
            'index.twig',
            [
                'tilePlannerInputForm' => $form->createView(),
                'tilePlannerInputData' => $tileInput ?? null,
                'plan' => $plan ?? null,
            ]
        );
    }
}
