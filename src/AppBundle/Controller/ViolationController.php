<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Violation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        if (!$user->hasRole('ROLE_SUPER_ADMIN')) {
            return $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        /** @var Violation $violation */
        $violation = $em->getRepository('AppBundle:Violation')->find($violationId);

        $approved = (bool) $request->get('approved');
        $carNumber = $request->get('carNumber');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        if (!is_null($approved) && $carNumber && $latitude && $longitude) {
            $violation->setApproved($approved);
            $violation->setCarNumber($carNumber);
            $violation->setLatitude($latitude);
            $violation->setLongitude($longitude);

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
        $form = $this->createForm('violation_video_form', new Violation());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Violation $violation */
            $violation = $form->getData();
            $violation->setApproved(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($violation);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Your item was added!');

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('@App/violation/create_video_violation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
