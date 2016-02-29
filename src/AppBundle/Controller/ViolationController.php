<?php

namespace AppBundle\Controller;

use AppBundle\DBAL\Types\VideoStatusType;
use AppBundle\Entity\User;
use AppBundle\Entity\Violation;
use AppBundle\Form\Model\ViolationModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ViolationController
 */
class ViolationController extends Controller
{
    /**
     * @param Request $request
     * @param int     $violationId
     *
     * @Route("violation/{violationId}/video", name="violation_video")
     *
     * @return Response
     */
    public function violationVideo(Request $request, $violationId)
    {
        $violation = $this->getDoctrine()->getRepository('AppBundle:Violation')->find($violationId);
        $session = $request->getSession();
        $session->set('referrer', $request->server->get('HTTP_REFERER'));

        return $this->render('video-admin.html.twig', [
            'violation' => $violation,
        ]);
    }

    /**
     * @param Request $request
     * @param int     $violationId
     * @Route("/admin/violation/{violationId}/edit", name="admin_edit_violation", options={"expose"=true})
     *
     * @return Response
     */
    public function adminUpdateAction(Request $request, $violationId)
    {
        $user = $this->getUser();
        if (!$user || !$user->hasRole('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        /** @var Violation $violation */
        $violation = $em->getRepository('AppBundle:Violation')->find($violationId);

        $approved = (bool) $request->get('approved');
        $carNumber = $request->get('carNumber');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $date = $request->get('date');

        if (!is_null($approved) && $carNumber && $latitude && $longitude) {
            $violation->setApproved($approved);
            $violation->setCarNumber($carNumber);
            $violation->setLatitude($latitude);
            $violation->setLongitude($longitude);
            if ($date) {
                $violation->setDate(new \DateTime($date));
            }

            $em->persist($violation);
            $em->flush();

            $this->get('session')
                ->getFlashBag()
                ->add('sonata_flash_success', 'Порушення відредаговано');
        } else {
            $this->get('session')
                ->getFlashBag()
                ->add('sonata_flash_error', 'Помилка! Порушення не відредаговано');
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => 'ok']);
        } else {
            $session = $request->getSession();

            return $this->redirect($session->get('referrer'));
        }
    }

    /**
     * @param int $violationId
     * @Route("/admin/violation/{violationId}/photo-modal", name="admin_violation_photo_modal", options={"expose"=true})
     *
     * @return Response
     */
    public function adminPhotoModalAction($violationId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Violation $violation */
        $violation = $em->getRepository('AppBundle:Violation')->find($violationId);

        return $this->render(':admin:photo-modal.html.twig', [
            'object' => $violation,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/add-video-violation", name="add_video_violation")
     *
     * @return Response
     */
    public function createVideoViolationAction(Request $request)
    {
        $convertingTypes = [
            'video/x-msvideo',
            'video/msvideo',
            'video/x-msvideo',
            'video/3gpp',
            'video/quicktime',
        ];

        $form = $this->createForm('violation_video_form', new ViolationModel());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ViolationModel $violationModel */
            $violationModel = $form->getData();
            $violation      = new Violation();

            $violation->setApproved(false);
            $violation->setRecordingType($violationModel->getRecordingType());
            $violation->setStatus($violationModel->getStatus());
            $violation->setDate($violationModel->getDate());
            $violation->setLatitude($violationModel->getLatitude());
            $violation->setLongitude($violationModel->getLongitude());
            $violation->setVideo($violationModel->getVideo());
            $violation->setCarNumber($violationModel->getCarNumber());

            if (in_array($violation->getVideo()->getMimeType(), $convertingTypes)) {
                $violation->setStatus(VideoStatusType::WAITING);
            }

            $em          = $this->getDoctrine()->getManager();
            $authorEmail = $violationModel->getAuthorEmail();

            $userManager = $this->get('fos_user.user_manager');
            /** @var User $user */
            $user = $userManager->findUserByEmail($authorEmail);

            $fromAddress = $this->container->getParameter('mailer_from');
            $fromName = $this->container->getParameter('mailer_name');
            $message = \Swift_Message::newInstance()
                ->setSubject('Громадський патруль')
                ->setFrom(array($fromAddress => $fromName))
                ->setTo(array($authorEmail));

            if (!$user instanceof User) {
                $user = $userManager->createUser();
                $password = uniqid();
                $user->setEmail($authorEmail);
                $user->setPlainPassword($password);
                $user->setEnabled(true);

                $message->setBody(
                    $this->renderView('AppBundle:mail:registration_mail.html.twig'),
                    'text/html'
                );

                $this->get('mailer')->send($message);

                $em->persist($user);
            }

            $violation->setAuthor($user);


            $em->persist($violation);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Правопорушення успішно додано, та буде опубліковане після модерації!');

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('@App/violation/create_video_violation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
