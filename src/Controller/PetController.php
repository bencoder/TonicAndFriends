<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Exception\InvalidInputException;
use App\Exception\PetNotFoundException;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pet Controller
 */
class PetController extends AbstractController
{
    private PetRepository $petRepository;

    private EntityManagerInterface $entityManager;

    private SerializerInterface $serializer;

    public function __construct(
      PetRepository $petRepository, 
      EntityManagerInterface $entityManager, 
      SerializerInterface $serializer
    ) {
      
      $this->petRepository = $petRepository;
      $this->entityManager = $entityManager;
      $this->serializer = $serializer;
    }

    /**
     * @Route("/pet", name="create", methods={"POST"})
     */
    public function createPet(Request $request): Response
    {
      $userId = 1; //TODO: this should come from an authentication solution
      $petType = $request->get('type');
      $petName = $request->get('name');
      if (!$petName) {
        throw new InvalidInputException("Pet name must be provided");
      }

      $petClass = "\\App\\Entity\\Pet\\$petType";
      if (!class_exists($petClass)) {
        throw new InvalidInputException("Pet type $petType is not supported");
      }
      $pet = new $petClass($userId, $petName);
      $this->entityManager->persist($pet);
      $this->entityManager->flush();
      
      return new Response($this->serializer->serialize($pet, 'json'));
    }

    /**
     * @Route("/pet", name="list", methods={"GET"})
     */
    public function listPets(): Response
    {
        $pets = $this->petRepository->findBy(['userId' => 1]);  //todo: get the user id from authentication

        return new Response(
          $this->serializer->serialize($pets, 'json')
        );
    }

    /**
     * @Route("/pet/{id}", name="get", methods={"GET"})
     */
    public function getPet(int $id): Response
    {
        $pet = $this->petRepository->findOneBy(['id' => $id]);
        if (!$pet) {
          throw new PetNotFoundException("Invalid Pet ID");
        }
        //TODO: check that the UserId on the Pet matches the current user
        return new Response(
          $this->serializer->serialize($pet, 'json')
        );
    }

    /**
     * @Route("/pet/{id}/feed", name="feed", methods={"POST"})
     */
    public function feedPet(int $id): Response
    {
        $pet = $this->petRepository->findOneBy(['id' => $id]);
        if (!$pet) {
          throw new PetNotFoundException("Invalid Pet ID");
        }
        //TODO: check that the UserId on the Pet matches the current user
        $pet->feed();
        $this->entityManager->flush();
        return new Response(
          $this->serializer->serialize($pet, 'json')
        );
    }

    /**
     * @Route("/pet/{id}/stroke", name="stroke", methods={"POST"})
     */
    public function strokePet(int $id): Response
    {
        $pet = $this->petRepository->findOneBy(['id' => $id]);
        if (!$pet) {
          throw new PetNotFoundException("Invalid Pet ID");
        }
        //TODO: check that the UserId on the Pet matches the current user
        $pet->stroke();
        $this->entityManager->flush();
        return new Response(
          $this->serializer->serialize($pet, 'json')
        );
    }
}
