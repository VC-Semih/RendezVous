<?php


namespace App\Controller;


use App\Repository\HoraireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/heure" ,name="/heure")
     * @param Request $request
     * @return Response
     */
    public function getHoraire(Request  $request, HoraireRepository $repository) :Response
    {

        $horaire = $repository->findAll();

        $jsonData = array();
        $idx = 0;
        foreach($horaire as $heure) {
            $temp = array(
                'heure' => $heure->getHeure(),
            );
            $jsonData[$idx++] = $temp;

        }
            $response = new  JsonResponse($jsonData);
            return $response;

    }
}