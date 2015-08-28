<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Violation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ViolationController
 */
class ViolationController extends Controller
{
    /**
     * @param Request $request
     * @param int     $violationId
     * @Route("/admin/violation/{violationId}/edit", name="admin_edit_violation", options={"expose"=true})
     *
     * @return Response
     */
    public function adminUpdateAction(Request $request, $violationId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Violation $violation */
        $violation = $em->getRepository('AppBundle:Violation')->find($violationId);

        $approved = (bool) $request->query->get('approved');
        $carNumber = $request->query->get('carNumber');
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

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
                ->add('sonata_flash_success', 'Помилка! Порушення не відредаговано');
        }

        return new JsonResponse(['status' => 'ok']);
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
}