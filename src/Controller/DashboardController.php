<?php

namespace OSW3\Manager\Controller;

use OSW3\Manager\Service\EntityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(EntityService $entityService): Response
    {
        // dump( $entityService->getAll() );
        // dd( $entityService->getNav() );

        return $this->render('@manager/dashboard.html.twig', [
        ]);
    }
}
