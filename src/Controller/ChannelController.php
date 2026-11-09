<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Channel;
use App\Form\ChannelType;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/channel')]
final class ChannelController extends AbstractController
{
    #[Route(name: 'app_channel_index', methods: ['GET'])]
    public function index(ChannelRepository $channelRepository): Response
    {
        return $this->render('channel/index.html.twig', [
            'channels' => $channelRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_channel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($channel);
            $entityManager->flush();

            return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('channel/new.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_channel_show', methods: ['GET', 'POST'])]
    public function show(Channel $channel, Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setChannel($channel);
            $message->setUser($this->getUser()); // Associe l'utilisateur connecté
            $message->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_channel_show', ['id' => $channel->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('channel/show.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_channel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('channel/edit.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_channel_delete', methods: ['POST'])]
    public function delete(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$channel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($channel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
    }
}

