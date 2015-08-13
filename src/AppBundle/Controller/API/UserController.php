<?php

namespace AppBundle\Controller\API;


use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * Register user
     *
     * @param Request $request
     *
     * @Route("/api/register")
     * @Method({"POST"})
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $email = $request->request->get('email');

        if (!\Swift_Validate::email($email)) {
            return new Response('Емейл не валідний', '400');
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->findUserByEmail($email);

        $fromAddress = $this->container->getParameter('mailer_from');
        $fromName = $this->container->getParameter('mailer_name');
        $message = \Swift_Message::newInstance()
            ->setSubject('Дорожній патруль')
            ->setFrom(array($fromAddress => $fromName))
            ->setTo(array($email));

        if (!$user instanceof User) {
            $user = $userManager->createUser();
            $password = uniqid();
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setEnabled(true);

            $message->setBody(
                $this->renderView(
                    'AppBundle:Mail:email_password.html.twig',
                    array(
                        'user' => $user,
                        'password' => $password,
                    )
                ),
                'text/html'
            );

            $this->get('mailer')->send($message);
        } else {
            return new Response('Користувач з таким емейлом вже зареєстрований', '400');
        }

        $userManager->updateUser($user);

        return new Response('OK', 200);
    }
}