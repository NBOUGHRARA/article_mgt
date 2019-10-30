<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\ContactType;
use App\Entity\Contact;
use App\Repository\ContactRepository;

class ApiController extends AbstractController
{
    private $manager;
    private $contactRepository;
    
    public function __construct(ObjectManager $manager, ContactRepository $contactRepo)
    {
        $this->manager = $manager;
        $this->contactRepository = $contactRepo;
    }

    /**
     * @Route("/contact", name="contact_api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig');
    }

    /**
     * @Route("/contact/add", name="add_contact", methods={"POST", "PUT"})
     */
    public function addContact(Request $request)
    {
        try {
            $data = $request->getContent();
            parse_str($data, $data_arr);

            if ($data_arr["id"]) {
                $contact = $this->contactRepository->findOneById($data_arr["id"]);
            } else {
                $contact = new Contact();
            }

            $form = $this->createForm(ContactType::class, $contact);
            $form->submit($data_arr);

            $this->manager->persist($contact);
            $this->manager->flush();

            return new JsonResponse([
                "id" => $contact->getId(),
                "nom" => $contact->getNom(),
                "prenom" => $contact->getPrenom(),
                "telephone" => $contact->getTelephone(),
                "email" => $contact->getEmail()
            ]);

        } catch (Exception $e) {
            echo "Exeption Reçu" . $e->getMessage() . "\n";
        }
    }

    /**
     * @Route("/contact/delete", name="delete_contact", methods={"DELETE"})
     */
    public function deleteContact(Request $request)
    {
        try {
            parse_str($request->getContent(), $data_arr);

            $contact = $this->contactRepository->findOneById($data_arr["id"]);

            $this->manager->remove($contact);
            $this->manager->flush();

            return new JsonResponse([]);

        } catch (Exception $e) {
            echo "Exeption Reçu" . $e->getMessage() . "\n";
        }
    }
}
