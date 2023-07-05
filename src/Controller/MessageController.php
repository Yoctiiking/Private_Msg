<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    #[Route('/messages', name: 'app_message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    #[Route('/send', name: 'app_message_send')]
    public function send(Request $request): Response
    {
        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $message->setSender($this->getUser());
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->addFlash('message', 'Message envoyé avec succès.');
            return $this->redirectToRoute('app_message');
        }


        return $this->render('message/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/received', name: 'app_message_received')]
    public function received(): Response
    {
        return $this->render('message/received.html.twig', [
        ]);
    }

    #[Route('/sent', name: 'app_message_sent')]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig', [
        ]);
    }

    #[Route('/read/{id}', name: 'app_message_read')]
    public function read(Message $message): Response
    {
        $message->setIsRead(true);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->render('message/read.html.twig', [
            'message' => $message
        ]);
    }

    #[Route('/see/{id}', name: 'app_message_see')]
    public function see(Message $message): Response
    {
        return $this->render('message/read.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_message_delete')]
    public function delete(Message $message): Response
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_message_received');
    }
}
