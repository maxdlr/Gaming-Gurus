<?php

namespace App\Twig\Components;

use App\Repository\VideoRepository;
use App\Repository\ViewedRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
final class VideoGalleryComponent
{
    public ?string $title = null;
    public int $count;
    public ?string $source = null;

    public ?int $videoPlayedId = null;

    private VideoRepository $videoRepository;
    private ViewedRepository $viewedRepository;

    public string $galleryId;
    public string $subtitle;
    private ?Collection $tags = null;
    private ?string $mainTag = null;
    private array $videos;

    public function __construct(
        VideoRepository $videoRepository,
        ViewedRepository $viewedRepository
    ) {
        $this->videoRepository = $videoRepository;
        $this->viewedRepository = $viewedRepository;
    }

    public function mount(
        ?int $videoPlayedId = null,
        string $source = null,
    ): void {
        $this->source = $source;

        switch ($source) {
            case 'latest':
                $this->mountLatestVideos();
                break;

            case 'most-viewed':
                $this->mountMostViewedVideos();
                break;

            case 'video-id':
                $this->mountVideosByVideoId($videoPlayedId);
                break;
        }

        $this->galleryId = $source;
    }

    private function mountLatestVideos(): void
    {
        $this->title = 'Les plus récentes !';
        $videoCollection = $this->videoRepository->findLatestVideos();
        $this->updateVideoProperties($videoCollection);
    }

    private function mountMostViewedVideos(): void
    {
        $this->title = 'Les plus vues !';
        $videosFromVieweds = $this->viewedRepository->findMostViewed(15);
        $videoCollection = [];
        foreach ($videosFromVieweds as $videoFromViewed) {
            $videoCollection[] = $this->videoRepository->findOneBy(['id' => $videoFromViewed['id']]);
        }
        $this->updateVideoProperties($videoCollection);
    }

    private function mountVideosByVideoId(?int $videoPlayedId): void
    {
        $video = $this->videoRepository->findOneBy(['id' => $videoPlayedId]);
        $tagCollection = $video->getTag();
        $firstTag = $tagCollection[0]->getName();
        $videoCollection = [];
        foreach ($tagCollection[0]->getVideos() as $video) {
            $videoCollection[] = $video;
        }
        $this->updateVideoProperties($videoCollection, $tagCollection, $firstTag);
    }

    private function updateVideoProperties(
        array $videoCollection,
        ?Collection $tagCollection = null,
        ?string $firstTag = null
    ): void {
        $this->subtitle = count($videoCollection) . ' videos';
        $this->tags = $tagCollection;
        $this->mainTag = $firstTag;
        $this->videos = $videoCollection;
    }

    public function getMainTag(): string
    {
        return $this->mainTag;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getVideos(): array
    {
        return $this->videos;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getGalleryId(): string
    {
        return $this->galleryId;
    }
}
