<?php

namespace OSW3\Manager\Controller;

use OSW3\Manager\Service\EntityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/entity/{path}', name: 'entity:')]
final class EntityController extends AbstractController
{
    public function __construct(
        private EntityService $entityService
    )
    {
        // Get the entity classname
        // $classname = $entityService->getEntityClassnameByPath($path);
    }


    #[Route('', name: 'index')]
    public function index($path, EntityService $entityService): Response
    {
        // Get the entity classname
        $classname = $entityService->getEntityClassnameByPath($path);

        // Get label name
        $name = $entityService->getName($classname);


        // Repository
        // --

        $repository = $entityService->getRepository($classname);
        $count      = $repository->count();
        $entities      = $repository->findAll();

        return $this->render('@manager/entity/index.html.twig', [
            'path'      => $path,
            'classname' => $classname,
            'name'      => $name,
            'count'     => $count,
            'entities'  => $entities,
        ]);
    }
    
    #[Route('/create', name: 'create')]
    public function create($path, EntityService $entityService): Response
    {

        return $this->render('@manager/entity/create.html.twig', [
        ]);
    }

    #[Route('/{id}', name: 'read')]
    public function read($path, $id, EntityService $entityService): Response
    {
        // Get the entity classname
        $classname = $entityService->getEntityClassnameByPath($path);


        // Repository
        // --

        $repository = $entityService->getRepository($classname);
        $entity     = $repository->find($id);
        $attributes = $entityService->getAttributes($entity);

        return $this->render('@manager/entity/read.html.twig', [
            'path'       => $path,
            'classname'  => $classname,
            'entity'     => $entity,
            'id'         => $id,
            'attributes' => $attributes,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'update')]
    public function update($path, EntityService $entityService): Response
    {
        
        return $this->render('@manager/entity/update.html.twig', [
        ]);
    }
    
    #[Route('/{id}/delete', name: 'delete')]
    public function delete($path, EntityService $entityService): Response
    {
        
        return $this->render('@manager/entity/delete.html.twig', [
        ]);
    }
}
