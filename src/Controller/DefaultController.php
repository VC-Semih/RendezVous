<?php


namespace App\Controller;


use App\Entity\Horaire;
use App\Entity\RendezVous;
use App\Repository\HoraireRepository;
use App\Repository\RendezVousRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
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
     * @Route("/adduserRdv" ,name="adduserRdv")
     * @param Request $request
     * @return Response
     */
    public function addrdvuser()
    {
        return $this->render('page/rendez_vous.html.twig');
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


        $idToRemove = [];
        $idToKeep = [];
        foreach ($horaires as $horaire) {
            array_push($idToKeep, $horaire->getId());
        }
        if ($listeRdv !== null && !empty($listeRdv)){
        foreach ($listeRdv as $rdv) {
            foreach ($horaires as $horaire) {
                if ($horaire->getId() === $rdv->getHoraire()->getId()) {
                    array_push($idToRemove,$horaire->getId());
                }
            }
        }}
        $idToKeep = array_diff($idToKeep, $idToRemove);
        $freeHoraire = $em->getRepository(Horaire::class)->findBy(array('id' => $idToKeep));

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
    public function getinfo(Request  $request,  SerializerInterface $serializer, UserInterface $user,HoraireRepository $horaireRepository,\Swift_Mailer $mailer): Response
    {

        $service = $request->get('service');
        $date = $request->get('date');
        $heure = $request->get('heure');
        $data = ["service" => $service ];
        $heureObject = $horaireRepository->findOneBy(array('heure' => $heure)); //Gets the heureObject by value


        if(!empty($service) or !empty($date) or !empty($heureObject))
        {
            $rdv = new RendezVous();
            $rdv->setDate(\DateTime::createFromFormat('Y-m-d', $date));
            $rdv->setUser($this->getUser());
            $rdv->setService($service);
            $rdv->setHoraire($heureObject);



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rdv);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Votre rendez-vous pour le service '.$service.' le '.$date.' '.$heure.' a été pris !'
            );

            $message = (new \Swift_Message('Serivce Rendez-vous '))
                ->setFrom('rendez-vous@amb-afg.fr')
                ->setTo($this->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'page/mail.html.twig',
                        [
                            'service' => $service,
                            'date'=> $date,
                            'heure'=> $heure
                        ]
                    ),
                    'text/html'
                );

          $mailer->send($message);

        }


        $response = new JsonResponse($data);

        $response->headers->set('Content-Type','application/json');
        return $response;


    }
    public function mesrdv(RendezVousRepository $rendezVousRepository)
    {
        $user_id = $this->getUser()->getId();
        return $this->render("page/mesRdv.html.twig",array(
            'rdvs' => $rendezVousRepository->mesrdv($user_id)
        ));
    }
    /**
     * @Route("/annuler_rdv/{id}",name="delete_rdv_user")
     */
    public function delete_rdv_user(Request $request,RendezVousRepository $repository):Response
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $rdv = $em -> getRepository('App:RendezVous')->find($id);
        $rdvService = $rdv->getService();
        $rdvDate = $rdv->getDate()->format('d/m/Y');
        $rdvHeure = $rdv->getHoraire();
        $em -> remove($rdv);
        $em -> flush();

        $this->addFlash(
            'notice',
            'Votre rendez vous pour le service '.$rdvService.' le '.$rdvDate.' '.$rdvHeure.' a été supprimé'
        );

        return $this->redirectToRoute("mesrdv");
    }


}