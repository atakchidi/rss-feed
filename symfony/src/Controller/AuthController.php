<?php

namespace App\Controller;

use App\DTO\Credentials;
use App\Entity\ClaimedUser;
use App\Entity\User;
use App\Repository\ClaimedUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthController
 * @package App\Controller
 */
class AuthController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RouterInterface */
    private $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $r)
    {
        $this->entityManager = $em;
        $this->router = $r;
    }

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(Credentials $credentials, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {

        if ($this->repository()->isMailTaken($credentials->email)) {
            return new JsonResponse('Email is already taken', 400);
        };

        $user = new ClaimedUser();
        $user->setEmail($credentials->email)
            ->setPassword($encoder->encodePassword(new User(), $credentials->password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $mailer->send($this->messageFor($credentials->email));

        return new JsonResponse('User successfully created');
    }

    /**
     * @Route("/validate/mail", methods={"POST"})
     */
    public function validate(Request $request)
    {
        return new JsonResponse(
            null,
            !$this->repository()->isMailTaken($request->request->get('email')) ? 200 : 400
        );
    }

    /**
     * @Route("/confirm", name="email_confirm", methods={"GET"})
     */
    public function confirm(Request $request)
    {
        $email = $request->query->get('email');
        if (!$email) {
            return new Response('Email is required', 400);
        }

        $claimedUser = $this->repository()->findOneBy(['email' => $email]);

        if (!$claimedUser) {
            return new Response("Email $email not found", 400);
        }

        $user = (new User())
            ->setEmail($claimedUser->getEmail())
            ->setPassword($claimedUser->getPassword());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('You have been successfully registered for the news feed!');
    }


    private function messageFor($email)
    {
        $url = $this->router->generate('email_confirm', ['email' => $email], UrlGeneratorInterface::ABSOLUTE_URL);

        return (new Swift_Message())
            ->setSubject('Registration confirmation from altak-rss-feed')
            ->setFrom(['altakrssapp@gmail.com'])
            ->setTo([$email])
            ->setBody('You requested to register for altak-rss-feed. If that\'s true please follow the link below to confirm: ' . $url);

    }

    /**
     * @return ClaimedUserRepository
     */
    private function repository()
    {
        return $this->entityManager->getRepository(ClaimedUser::class);
    }
}
