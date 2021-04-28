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

    function removeElementWithValue($array, $key, $value){
        foreach($array as $subKey => $subArray){
            if($subArray[$key] == $value){
                unset($array[$subKey]);
            }
        }
        return $array;
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

            $message = (new \Swift_Message('Serivce Rendez-vous '))
                ->setFrom('rendez-vous@amb-afg.fr')
                ->setTo($this->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'page/mail.html.twig',
                        [
                            'service' => $service,
                            'date'=> $date,
                            '$heure'=> $heure
                        ]
                    ),
                    'text/html'
                );

          $mailer->send($message);

        }


        $response = new Response($service);
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
}