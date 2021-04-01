<?php


namespace App\Controller;


use App\Entity\Horaire;
use App\Entity\RendezVous;
use App\Repository\HoraireRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function getHoraire(Request  $request,  SerializerInterface $serializer)
    {
       $date = $request->get('date');
//        $date= str_replace('.','-',$date);
//       $date = DateTime::createFromFormat('d-m-y H:i',$date);
        $em = $this->getDoctrine()->getManager();

        $horaires = $em->getRepository(Horaire::class)->findAll();
//        $listeRdv= $em->getRepository(RendezVous::class)->findBy(array('date'=>$date));
        $listeRdv= $em->getRepository(RendezVous::class)->findByDate($date);

        $freeHoraire= [];

        if ($listeRdv !== null && !empty($listeRdv)){

        foreach ($listeRdv as $rdv) {
            foreach ($horaires as $horaire) {
                if ($horaire->getId() !== $rdv->getHoraire()->getId()) {
                    $freeHoraire[] = $horaire;
                }
            }

        }}else{

                $freeHoraire=$horaires;
        }
        $data = $serializer->serialize($freeHoraire,'json');

        $response = new Response($data);
        $response->headers->set('Content-Type','application/json');
        return $response;

    }
}