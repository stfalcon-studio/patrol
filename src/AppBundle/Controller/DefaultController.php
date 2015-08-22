<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $vioRepo = $em->getRepository('AppBundle:Violation');
        $violations = $vioRepo->findBy([
            'approved' => true,
            ], [
            'createdAt' => 'DESC',
            ]);

        return $this->render('default/index.html.twig', [
            'violations' => $violations,
        ]);
    }
}
