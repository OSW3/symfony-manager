<?php

namespace OSW3\Manager\Controller;

use OSW3\Manager\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use OSW3\Manager\DependencyInjection\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/entity/{path}', name: 'entity:')]
final class EntityController extends AbstractController
{
    private string $currentPath;
    private string $classname;
    private string $entityName;
    private object $repository;

    public function __construct(
        private EntityService $entityService,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    )
    {
        $request           = $this->requestStack->getCurrentRequest();
        $currentRoute      = $request->attributes->get('_route');
        $this->currentPath = $request->attributes->get('path');
        $this->classname   = $entityService->getEntityClassnameByPath($this->currentPath);
        $this->repository  = $entityService->getRepository($this->classname);
        $this->entityName  = $currentRoute === 'manager:entity:index'
                            ? $entityService->getName($this->classname)
                            : $entityService->getSingularName($this->classname);
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $entities = $this->entityService->getPaged($this->repository, $this->classname);
        $total    = $this->entityService->count($this->repository, $this->classname);
        $pages    = $this->entityService->getPages($this->repository, $this->classname);

        return $this->render('@manager/entity/index.html.twig', [
            'entityName' => $this->entityName,
            'classname'  => $this->classname,
            'total'      => $total,
            'entities'   => $entities,
            'path'       => $this->currentPath,
            'pages'      => $pages,
        ]);
    }
    
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity   = new $this->classname;
        $formType = $this->entityService->getFormType($this->classname);

        $form = $this->createForm($formType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($entity);
                $entityManager->flush();
    
                $options    = $this->entityService->getEntityOptions($this->classname);
                $idGetter   = "get".ucfirst($options['id']);
                $nameGetter = "get".ucfirst($options['name']);
                $route      = match($options['create']['redirect']) {
                                'index'  => "manager:entity:index",
                                'create' => "manager:entity:create",
                                'edit'   => "manager:entity:update",
                                default  => "manager:entity:read"
                            };

                $this->addFlash('success', $this->translator->trans('flash.create_success', [
                    '%entityName%'   => strtolower($this->entityName),
                    '%propertyName%' => $entity->$nameGetter(),
                ], Configuration::DOMAIN));

                return $this->redirectToRoute($route, [
                    'path' => $this->currentPath,
                    'id'   => $entity->$idGetter()
                ], Response::HTTP_SEE_OTHER);
            }
            else {
                $this->addFlash('error', $this->translator->trans('flash.create_error', [], Configuration::DOMAIN));
            }
        }

        return $this->render('@manager/entity/create.html.twig', [
            'entityName' => $this->entityName,
            'classname'  => $this->classname,
            'form'       => $form,
        ]);
    }

    #[Route('/{id}', name: 'read')]
    public function read($id): Response
    {
        $entity     = $this->repository->find($id);
        $attributes = $this->entityService->getAttributes($entity);
        $options    = $this->entityService->getEntityOptions($this->classname);
        $nameGetter = "get".ucfirst($options['name']);

        return $this->render('@manager/entity/read.html.twig', [
            'entityName'   => $this->entityName,
            'propertyName' => $entity->$nameGetter(),
            'classname'    => $this->classname,
            'id'           => $id,
            'entity'       => $entity,
            'attributes'   => $attributes,
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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();

                $options  = $this->entityService->getEntityOptions($this->classname);
                $idGetter = "get".ucfirst($options['id']);
                $nameGetter = "get".ucfirst($options['name']);
                $route    = match($options['create']['redirect']) {
                    'index' => "manager:entity:index",
                    'edit'  => "manager:entity:update",
                    default => "manager:entity:read"
                };

                $this->addFlash('success', $this->translator->trans('flash.update_success', [
                    '%entityName%'   => strtolower($this->entityName),
                    '%propertyName%' => $entity->$nameGetter(),
                ], Configuration::DOMAIN));
    
                return $this->redirectToRoute($route, [
                    'path' => $this->currentPath,
                    'id'   => $entity->$idGetter()
                ], Response::HTTP_SEE_OTHER);
            } 
            else {
                $this->addFlash('error', $this->translator->trans('flash.update_error', [], Configuration::DOMAIN));
            }
        }
        
        return $this->render('@manager/entity/edit.html.twig', [
            'entityName' => $this->entityName,
            'classname'  => $this->classname,
            'id'         => $id,
            'entity'     => $entity,
            'form'       => $form,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'delete')]
    public function delete($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $options      = $this->entityService->getEntityOptions($this->classname);
        $idGetter     = "get".ucfirst($options['id']);
        $nameGetter   = "get".ucfirst($options['name']);
        $entity       = $this->repository->find($id);
        $propertyName = $entity->$nameGetter();

        if ($this->isCsrfTokenValid(
            'delete'.$entity->$idGetter(), 
            $request->getPayload()->getString('_token'))
        ){
            $entityManager->remove($entity);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('flash.delete_success', [
                '%entityName%'   => strtolower($this->entityName),
                '%propertyName%' => $entity->$nameGetter(),
            ], Configuration::DOMAIN));

            return $this->redirectToRoute('manager:entity:index', [
                'path' => $options['index']['path'],
            ], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('@manager/entity/delete.html.twig', [
            'entityName'   => $this->entityName,
            'propertyName' => $propertyName,
            'classname'    => $this->classname,
            'id'           => $id,
        ]);
    }
}
