<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FeedIo\Feed;
use FeedIo\FeedIo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class RssController
{
    /**
     * @Route("/feed", methods={"GET"})
     */
    public function index(FeedIo $feedIo, UserInterface $user, EntityManagerInterface $em)
    {
        $url = 'https://www.delfi.lv/rss/?channel=delfi';
        /** @var User $user */
        $feed = $feedIo->read($url, new Feed(), $user->getLastReadAt())->getFeed();
        $resp = [];

        foreach ($feed as $item) {
            $resp[] = [
                'title' => $item->getTitle(),
                'link' => $item->getLink(),
                'description' => $item->getDescription(),
            ];
        }


        /** User $user */
        $user->setLastReadAt(new \DateTime("now", new \DateTimeZone("UTC")));
        $em->persist($user);
        $em->flush();

        return new JsonResponse($resp);
    }
}
