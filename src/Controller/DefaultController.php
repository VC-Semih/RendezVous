<?php


namespace App\Controller;


use App\Entity\Horaire;
use App\Entity\RendezVous;
use App\Repository\HoraireRepository;
use App\Repository\RendezVousRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
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

        $em = $this->getDoctrine()->getManager();

        $horaires = $em->getRepository(Horaire::class)->findAll();

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
    /**
     * @Route("/rdv" ,name="/rdv")
     * @param Request $request
     * @return Response
     */
    public function getinfo(Request  $request,  SerializerInterface $serializer, UserInterface $user,HoraireRepository $horaireRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $service = $request->get('service');
        $date = $request->get('date');
        $heure = $request->get('heure');

       $heures = $horaireRepository->getheureId($heure);

        $changeHeureTostring = implode("','",$heures[0]);




        if(!empty($service) or !empty($date) or !empty($heure))
        {
            $rdv = new RendezVous();
            $rdv->setDate(\DateTime::createFromFormat('Y-m-d', $date));
            $rdv->setUser($this->getUser());

            $rdv->setHoraire($changeHeureTostring);




            $entityManager->persist($rdv);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();



        }




        $response = new Response($service);
        $response->headers->set('Content-Type','application/json');
        return $response;


    }
}