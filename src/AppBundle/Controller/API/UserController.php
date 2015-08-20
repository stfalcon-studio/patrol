<?php

namespace AppBundle\Controller\API;


use AppBundle\Entity\User;
use AppBundle\Entity\Violation;
use FOS\UserBundle\Model\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @ApiDoc(
     *  statusCodes={
     *         201="Returned when user successful created",
     *         400="Returned when the user data incorrect or not valid",
     *     },
     *  description="User registration",
     *  parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="user email"},
     *  }
     * )
     *
     * @return Response
     */
    public function postRegisterAction(Request $request)
    {
        $email = $request->request->get('email');

        if (!\Swift_Validate::email($email)) {
            return new JsonResponse('Електронна пошта не валідна', 400);
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
            return new JsonResponse(['user-id'=>$user->getId()], 200);
        }

        $userManager->updateUser($user);

        return new JsonResponse([
            'user' => [
                'id'    => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ], 201);
    }

    /**
     * Create violation
     *
     * @param Request $request
     * @param User    $user
     *
     * @ApiDoc(
     *  statusCodes={
     *         201="Returned when violation successful created",
     *         400="Returned when the photo data is incorrect",
     *         404="Returned when the user is not found"
     *     },
     *  description="Create violation by user",
     *  parameters={
     *      {"name"="photo", "dataType"="file", "required"=true, "description"="violation photo"},
     *  }
     * )
     *
     * @Route("/api/{user}/violation/create")
     * @Method({"POST"})
     *
     * @return JsonResponse
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
            $converter = $this->get('app.geocoordinates_converter');
            $convertedData = $converter->convert($info);
            if ($convertedData) {
                $lat = $convertedData['latitude'];
                $lng = $convertedData['longitude'];
            } else {
                return new JsonResponse('Файл без геокоординат', 400);
            }
        } else {
            return new JsonResponse('Не валідний файл', 400);
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

        return new JsonResponse([
            'violation' => [
                'latitude'   => $violation->getLatitude(),
                'longitude'  => $violation->getLongitude(),
                'image_path' => $violation->getWebPath(),
                'author'     => $user->getId(),
            ],
        ], 201);
    }
}