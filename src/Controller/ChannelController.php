<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Entity\Message;
use App\Form\ChannelType;
use App\Form\MessageType;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ChannelController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(ChannelRepository $channelRepository): Response
    {
        $firstChannel = $channelRepository->findOneBy([]);
        if ($firstChannel) {
            return $this->redirectToRoute('app_channel_show', ['id' => $firstChannel->getId()]);
        }

        return $this->redirectToRoute('app_channel_index');
    }

    #[Route('/channel', name: 'app_channel_index', methods: ['GET'])]
    public function index(ChannelRepository $channelRepository): Response
    {
        $channels = $channelRepository->findAll();

        return $this->render('channel/index.html.twig', [
            'channels' => $channels,
        ]);
    }

    #[Route('/channel/new', name: 'app_channel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($channel);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Le salon "#%s" a été créé avec succès !', $channel->getName()));

            return $this->redirectToRoute('app_channel_show', ['id' => $channel->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('channel/new.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/channel/{id}', name: 'app_channel_show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(
        Channel $channel,
        ChannelRepository $channelRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setChannel($channel);
            $message->setUser($this->getUser());
            $message->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_channel_show', ['id' => $channel->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('channel/show.html.twig', [
            'channel' => $channel,
            'channels' => $channelRepository->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/channel/{id}/edit', name: 'app_channel_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', sprintf('Le salon "#%s" a été mis à jour !', $channel->getName()));

            return $this->redirectToRoute('app_channel_show', ['id' => $channel->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('channel/edit.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/channel/{id}/delete', name: 'app_channel_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$channel->getId(), $request->request->get('_token'))) {
            $channelName = $channel->getName();
            $entityManager->remove($channel);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Le salon "#%s" a été supprimé.', $channelName));
        }

        return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
    }
}