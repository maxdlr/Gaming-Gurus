<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Viewed;
use App\Repository\CommentRepository;
use App\Repository\TagRepository;
use App\Repository\VideoRepository;
use App\Repository\ViewedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/video', name: 'video_')]
class VideoController extends AbstractController
{
    #[Route('/show/{id<^[0-9]+$>}', methods: ['GET'], name: 'show')]
    public function show(
        Video $video,
        VideoRepository $videoRepository,
        TagRepository $tagRepository,
        CommentRepository $commentRepository
    ): Response {
        $latestVideos = $videoRepository->findLatestVideos();
        $mobaVideos = $videoRepository->findLatestVideos();
        $comments = $commentRepository->findLatestComments($video);
        $tags = $tagRepository->findAll();

        return $this->render('video/show.html.twig', [
            'videoPlayed' => $video,
            'latestVideos' => $latestVideos,
            'mobaVideos' => $mobaVideos,
            'tags' => $tags,
            'comments' => $comments,
        ]);
    }

    #[Route('/add/{videoId<^[0-9]+$>}', name: 'add')]
    #[ParamConverter('video', class: 'App\Entity\Video', options: ['id' => 'videoId'])]
    public function add(Video $video, ViewedRepository $viewedRepository): Response
    {
        $viewed = new Viewed();
        $user = $this->getUser();
        $viewed->setUser($user)->setVideo($video);

        $viewedRepository->save($viewed, true);

        return new Response(status: 200);
    }
}
