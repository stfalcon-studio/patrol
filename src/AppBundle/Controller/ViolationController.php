<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Violation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/admin/violation/{violationId}/edit", name="admin_edit_violation")
     *
     * @return Response
     */
    public function adminUpdateAction(Request $request, $violationId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Violation $violation */
        $violation = $em->getRepository('AppBundle:Violation')->find($violationId);

        $approved = (bool) $request->request->get('approved');
        $carNumber = $request->request->get('carNumber');

        if ($approved || $carNumber) {
            $violation->setApproved($approved);
            $violation->setCarNumber($carNumber);

            $em->persist($violation);
            $em->flush();

            $this->get('session')
                ->getFlashBag()
                ->add('sonata_flash_success', 'Порушення відредаговано');
        }

        return $this->redirectToRoute('admin_app_violation_list');
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