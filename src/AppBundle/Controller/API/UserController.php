<?php

namespace AppBundle\Controller\API;


use AppBundle\Entity\User;
use AppBundle\Entity\Violation;
use FOS\UserBundle\Model\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function postRegisterAction(Request $request)
    {
        $email = $request->request->get('email');

        if (!\Swift_Validate::email($email)) {
            return new Response('Емейл не валідний', 400);
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
            return new Response('Користувач з таким емейлом вже зареєстрований', 400);
        }

        $userManager->updateUser($user);

        return new Response('OK', 201);
    }

    /**
     * Create violation
     *
     * @param Request $request
     * @param User    $user
     * @return Response
     * @Route("/api/{user}/violation/create")
     * @Method({"POST"})
     *
     */
    public function postViolationAction(Request $request, User $user)
    {
        $violation = new Violation();
        $file = $request->files->get('photo');

        $data = [
            'photo' => $file,
        ];

        if (is_file($file)) {
            $info = exif_read_data($file);
            if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
                isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
                in_array($info['GPSLatitudeRef'], array('E', 'W', 'N', 'S')) &&
                in_array($info['GPSLongitudeRef'], array('E', 'W', 'N', 'S'))
            ) {
                $GPSLatitudeRef = strtolower(trim($info['GPSLatitudeRef']));
                $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

                $latDegreesA = explode('/', $info['GPSLatitude'][0]);
                $latMinutesA = explode('/', $info['GPSLatitude'][1]);
                $latSecondsA = explode('/', $info['GPSLatitude'][2]);
                $lngDegreesA = explode('/', $info['GPSLongitude'][0]);
                $lngMinutesA = explode('/', $info['GPSLongitude'][1]);
                $lngSecondsA = explode('/', $info['GPSLongitude'][2]);

                $latDegrees = $latDegreesA[0] / $latDegreesA[1];
                $latMinutes = $latMinutesA[0] / $latMinutesA[1];
                $latSeconds = $latSecondsA[0] / $latSecondsA[1];
                $lngDegrees = $lngDegreesA[0] / $lngDegreesA[1];
                $lngMinutes = $lngMinutesA[0] / $lngMinutesA[1];
                $lngSeconds = $lngSecondsA[0] / $lngSecondsA[1];

                $lat = (float) $latDegrees + ((($latMinutes * 60) + ($latSeconds)) / 3600);
                $lng = (float) $lngDegrees + ((($lngMinutes * 60) + ($lngSeconds)) / 3600);

                //If the latitude is South, make it negative.
                //If the longitude is west, make it negative
                $GPSLatitudeRef == 's' ? $lat *= -1 : '';
                $GPSLongitudeRef == 'w' ? $lng *= -1 : '';
            } else {
                return new Response('Фото без геокоординат', 400);
            }
        } else {
            return new Response('Не валідний файл', 400);
        }


        $form = $this->createForm('violation_photo_form', $violation, array('csrf_protection' => false));
        $form->submit($data);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $violation->setAuthor($user);
            $violation->setApproved(false);
            $violation->setLatitude($lat);
            $violation->setLongitude($lng);

            $em->persist($violation);
            $em->flush();
        }

        return new Response('Порушення створено', 201);
    }
}