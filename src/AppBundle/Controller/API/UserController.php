<?php

namespace AppBundle\Controller\API;

use AppBundle\DBAL\Types\VideoRecordingType;
use AppBundle\DBAL\Types\VideoStatusType;
use AppBundle\Entity\User;
use AppBundle\Entity\Violation;
use AppBundle\Form\Model\ViolationModel;
use FOS\UserBundle\Model\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            return new JsonResponse([
                'message' => 'Електронна пошта не валідна',
            ], 400);
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->findUserByEmail($email);

        $fromAddress = $this->container->getParameter('mailer_from');
        $fromName = $this->container->getParameter('mailer_name');
        $message = \Swift_Message::newInstance()
            ->setSubject('Громадський патруль')
            ->setFrom(array($fromAddress => $fromName))
            ->setTo(array($email));

        if (!$user instanceof User) {
            $user = $userManager->createUser();
            $password = uniqid();
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setEnabled(true);

            $message->setBody(
                $this->renderView('AppBundle:mail:registration_mail.html.twig'),
                'text/html'
            );

            $this->get('mailer')->send($message);
        } else {
            return new JsonResponse([
                'id'    => $user->getId(),
                'email' => $user->getEmail(),
            ], 200);
        }

        $userManager->updateUser($user);

        return new JsonResponse([
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
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
     *      {"name"="latitude", "dataType"="float", "required"=true, "description"="photo latitude"},
     *      {"name"="longitude", "dataType"="float", "required"=true, "description"="photo longitude"},
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
            $longitude = $request->request->get('longitude');
            $latitude = $request->request->get('latitude');
            if (!$longitude || !$latitude) {
                return new JsonResponse([
                    'message' => 'Не вказано координати',
                ], 400);
            }
        } else {
            return new JsonResponse([
                'message' => 'Не валідний файл',
            ], 400);
        }


        $form = $this->createForm('violation_photo_form', $violation, array('csrf_protection' => false));
        $form->submit($data);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $violation->setAuthor($user);
            $violation->setApproved(false);
            $violation->setLatitude($latitude);
            $violation->setLongitude($longitude);

            $em->persist($violation);
            $em->flush();
        }

        return new JsonResponse([
            'latitude'   => $violation->getLatitude(),
            'longitude'  => $violation->getLongitude(),
            'image_path' => $violation->getWebPath(),
            'author'     => $user->getId(),
        ], 201);
    }

    /**
     * Create video violation
     *
     * @param Request $request
     * @param User    $user
     *
     * @ApiDoc(
     *  statusCodes={
     *         201="Returned when violation successful created",
     *         400="Returned when the data is incorrect",
     *         404="Returned when the user is not found"
     *     },
     *  description="Create violation video by user",
     *  parameters={
     *      {"name"="video", "dataType"="file", "required"=true, "description"="violation video"},
     *      {"name"="carNumber", "dataType"="string", "required"=false, "description"="car number of offender"},
     *      {"name"="date", "dataType"="date", "required"=false, "description"="date of violation"},
     *      {"name"="latitude", "dataType"="float", "required"=true, "description"="photo latitude"},
     *      {"name"="longitude", "dataType"="float", "required"=true, "description"="photo longitude"},
     *      {"name"="recordingType", "dataType"="string", "required"=true, "description"="video uploading type. allowable values: 'camera', 'recorder', 'upload' "},
     *  }
     * )
     *
     * @Route("/api/{user}/violation-video/create")
     * @Method({"POST"})
     *
     * @return JsonResponse
     */
    public function postViolationVideoAction(Request $request, User $user)
    {
        $logger = $this->get('logger');
        $violationModel = new ViolationModel();
        /** @var File $file */
        $file = $request->files->get('video');

        $data = [
            'video' => $file,
        ];

        if (!$user) {
            $logger->error('Даного користувача не існує');

            return new JsonResponse([
                    'message' => 'Даного користувача не існує',
            ], 404);
        }

        $data['longitude'] = $request->request->get('longitude');
        $data['latitude'] = $request->request->get('latitude');
        $data['date'] = $request->request->get('date');
        $data['carNumber'] = $request->request->get('carNumber');
        $data['recordingType'] = $request->get('recordingType');

        if (!$data['recordingType']) {
            $data['recordingType'] = VideoRecordingType::RECORDER;
        }

        if (!$data['longitude'] || !$data['latitude']) {
            $logger->error('Не вказано координати');

            return new JsonResponse([
                'message' => 'Не вказано координати',
            ], 400);
        }

        $form = $this->createForm('violation_video_form', $violationModel, array('csrf_protection' => false));
        $form->submit($data);

        if ($form->isValid()) {
            $em        = $this->getDoctrine()->getManager();
            $violation = new Violation();

            $violation->setApproved(false);
            $violation->setRecordingType($violationModel->getRecordingType());
            $violation->setDate($violationModel->getDate());
            $violation->setLatitude($violationModel->getLatitude());
            $violation->setLongitude($violationModel->getLongitude());
            $violation->setVideo($violationModel->getVideo());
            $violation->setCarNumber($violationModel->getCarNumber());
            $violation->setAuthor($user);

            if (in_array($file->getMimeType(), Violation::CONVERTING_TYPES)) {
                $violation->setStatus(VideoStatusType::WAITING);
            }

            $em->persist($violation);
            $em->flush();
        } else {
            $logger->error($form->getErrorsAsString());

            return new JsonResponse(['message' => $form->getErrorsAsString(), 400]);
        }

        return new JsonResponse([
                'latitude' => $violation->getLatitude(),
                'longitude' => $violation->getLongitude(),
                'video_path' => $violation->getVideoWebPath(),
                'author' => $user->getId(),
            ], 201);
    }
}
