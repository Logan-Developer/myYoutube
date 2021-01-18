<?php

namespace App\Twig;

use RicardoFiorani\Matcher\VideoServiceMatcher;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class YoutubeExtension extends AbstractExtension
{
    private $youtubeThumbnailParser;

    public function __construct()
    {
        // init thumbnail parser
        $this->youtubeThumbnailParser = new VideoServiceMatcher();
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('youtubeThumbnailParser', [$this, 'parseThumbnail']),
            new TwigFilter('youtubeVideoPlayer', [$this, 'playYoutubeVideo']),
        ];
    }

    public function parseThumbnail($url)
    {
       $video = $this->youtubeThumbnailParser->parse($url);

       if ($video->isEmbeddable()) {

           return $video->getMediumThumbnail();
       }

       return null;
    }

    public function playYoutubeVideo($url)
    {
        $video = $this->youtubeThumbnailParser->parse($url);

        if ($video->isEmbeddable()) {

            return $video->getEmbedCode('100%', 500, false, true);
        }

        return null;
    }
}
