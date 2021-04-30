<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RendezVousType;
use App\Repository\HoraireRepository;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use App\Security\Authenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/rdv")
 */
class RendezVousController extends AbstractController
{
    /**
     * @Route("/", name="rendez_vous_index", methods={"GET","POST"})
     */
    public function index(RendezVousRepository $rendezVousRepository): Response
    {

        return $this->render('rendez_vous/index.html.twig', [
            'rendez_vouses' => $rendezVousRepository->toutRdv()
        ]);
    }

    /**
     * @Route("/addUser", name="rendez_vous_useradd", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param Authenticator $authenticator
     * @return Response
     */
    public function addUserForRdv(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, Authenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsVerified(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('rendez_vous_new');

        }

        return $this->render('admin/adduser.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function admin_mailer(Request $request,RendezVousRepository $rendezVousRepository, \Swift_Mailer $mailer): Response
    {
        $id = $request->get('id');
        if($id >= 0){
            $rdv = $rendezVousRepository->findOneBy(array('id' => $id));
            if($rdv){
                $username = $rdv->getUser()->getUsername();
                $service = $rdv->getService();
                $date = $rdv->getDate();
                $heure = $rdv->getHoraire();
                $message = (new \Swift_Message('Rappel Rendez-vous '))
                    ->setFrom('rendez-vous@amb-afg.fr')
                    ->setTo($rdv->getUser()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'rendez_vous/rappel.html.twig',
                            [
                                'username' => $username,
                                'service' => $service,
                                'date'=> $date,
                                'heure'=> $heure
                            ]
                        ),
                        'text/html'
                    );

                $mailer->send($message);
            }
        }
        return $this->redirectToRoute('rendez_vous_index');
    }

    /**
     * @Route("/admin/addRdv" ,name="/adminAddRdv")
     * @param Request $request
     * @return Response
     */
    public function rdvadminPage(Request $request,UserRepository $repository)
    {
        return $this->render("admin/addrdv.html.twig",array(
            'users' => $repository->findAll()
        ));
    }


    /**
     * @Route("/rdvadmin" ,name="/rdvadmin")
     * @param Request $request
     * @return Response
     */
    public function adminrdv(Request  $request,  SerializerInterface $serializer, UserInterface $user,HoraireRepository $horaireRepository,\Swift_Mailer $mailer): Response
    {

//        $service = $request->get('service');
//        $date = $request->get('date');
//        $heure = $request->get('heure');

//        $heureObject = $horaireRepository->findOneBy(array('heure' => $heure)); //Gets the heureObject by value





        $response = new Response("hello");
        $response->headers->set('Content-Type','application/json');
        return $response;


    }













    /**
     * @Route("/new", name="rendez_vous_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $rendezVous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            return $this->redirectToRoute('rendez_vous_index');
        }

        return $this->render('rendez_vous/new.html.twig', [
            'rendez_vou' => $rendezVous,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rendez_vous_show", methods={"GET"})
     */
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez_vou' => $rendezVous,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="rendez_vous_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RendezVous $rendezVous): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('rendez_vous_index');
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rendez_vou' => $rendezVous,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rendez_vous_delete", methods={"POST"})
     */
    public function delete(Request $request, RendezVous $rendezVous): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVous->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rendezVous);
            $entityManager->flush();
        }

        return $this->redirectToRoute('rendez_vous_index');
    }

}
