<?php

namespace App\Controller;

use App\Entity\Partners;
use App\Repository\PartnersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartnersController extends AbstractController
{

    private ManagerRegistry $managerRegistry;
    private PartnersRepository $repo;
    private ObjectManager $objectManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->repo = $this->managerRegistry->getRepository(Partners::class);
        $this->objectManager = $this->managerRegistry->getManager();
    }
    #[Route('/partner/getAll')]
    public function getAll(): Response
    {return $this->json($this->repo->findAll());}

    #[Route('/partners/get/{id}')]
    public function get(int $id): Response
    {return $this->json($this->repo->find($id));}

    #[Route('/partner/add')]
    public function add(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $partner = new Partners($data['name'], $data['type'], $data['description'], $data['urlpage']);
        $this->objectManager->persist($partner);

        $this->objectManager->flush();

        return $this->json('success');
    }


    #[Route('/partner/delete/{id}')]
    public function delete(int $id): Response
    {
        $this->repo->remove($id);
        $this->objectManager->flush();
        return $this->json('success !!');
    }

    #[Route('photo/partner')]
    public function upload(Request $request): Response
    {
        $server = 'C:\Users\ARIDHI\Desktop\PFE\PFE-front\src\\';
        $file = $request->files->get('file');
        $fileName = $file->getClientOriginalName();

        try {$file->move($server.'assets\partnerPhoto\\', $fileName);}
        catch (FileException $e) {}

        return $this->json('assets\partnerPhoto\\'.$fileName);
    }

    #[Route('photo/partner/get/{id}')]
    public function getPhoto(int $id)
    {
        $server = 'C:\Users\ARIDHI\Desktop\PFE\PFE-front\src\\';
        $path = $server."assets\userPhoto\\";
        if (file_exists($path.$id.'.jpg'))
            return $this->json("assets/partnerPhoto/".$id.'.jpg');
        return $this->json('assets/partnerPhoto/default.jpg');
    }
}