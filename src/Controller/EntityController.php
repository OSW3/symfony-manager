<?php

namespace OSW3\Manager\Controller;

use OSW3\Manager\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/entity/{path}', name: 'entity:')]
final class EntityController extends AbstractController
{
    private string $currentPath;
    private string $classname;
    private string $name;
    private object $repository;

    public function __construct(
        private EntityService $entityService,
        private RequestStack $requestStack,
    )
    {
        $request           = $this->requestStack->getCurrentRequest();
        $currentRoute      = $request->attributes->get('_route');
        $this->currentPath = $request->attributes->get('path');
        $this->classname   = $entityService->getEntityClassnameByPath($this->currentPath);
        $this->repository  = $entityService->getRepository($this->classname);
        $this->name        = $currentRoute === 'manager:entity:index' 
                            ? $entityService->getName($this->classname)
                            : $entityService->getSingularName($this->classname);
    }


    #[Route('', name: 'index')]
    public function index(): Response
    {
        $count    = $this->repository->count();
        $entities = $this->repository->findAll();

        return $this->render('@manager/entity/index.html.twig', [
            'classname' => $this->classname,
            'name'      => $this->name,
            'count'     => $count,
            'entities'  => $entities,
            // 'path'      => $this->currentPath,
        ]);
    }
    
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity   = new $this->classname;
        $formType = $this->entityService->getFormType($this->classname);

        $form = $this->createForm($formType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();
            
            $options = $this->entityService->getEntityOptions($this->classname);
            $route   = match($options['create']['redirect']) {
                'index' => "manager:entity:index",
                'edit'  => "manager:entity:update",
                default => "manager:entity:read"
            };

            return $this->redirectToRoute($route, [
                'path' => $this->currentPath,
                'id'   => $entity->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@manager/entity/create.html.twig', [
            'name'      => $this->name,
            'classname' => $this->classname,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}', name: 'read')]
    public function read($id): Response
    {
        $entity     = $this->repository->find($id);
        $attributes = $this->entityService->getAttributes($entity);

        return $this->render('@manager/entity/read.html.twig', [
            'name'       => $this->name,
            'classname'  => $this->classname,
            'id'         => $id,
            'entity'     => $entity,
            'attributes' => $attributes,
            // 'path'       => $this->currentPath,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'update')]
    public function update($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity   = $this->repository->find($id);
        $formType = $this->entityService->getFormType($this->classname);

        $form = $this->createForm($formType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $options = $this->entityService->getEntityOptions($this->classname);
            $route   = match($options['create']['redirect']) {
                'index' => "manager:entity:index",
                'edit'  => "manager:entity:update",
                default => "manager:entity:read"
            };
            
            return $this->redirectToRoute($route, [
                'path' => $this->currentPath,
                'id'   => $entity->getId()
            ], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('@manager/entity/edit.html.twig', [
            'name'      => $this->name,
            'classname' => $this->classname,
            'id'        => $id,
            'entity'    => $entity,
            'form'      => $form,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'delete')]
    public function delete(): Response
    {
        
        return $this->render('@manager/entity/delete.html.twig', [
        ]);
    }
}
