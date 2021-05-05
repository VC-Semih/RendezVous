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
use DateTime;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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

            $this->addFlash(
                'notice',
                'L\'utilisateur '.$user->getUsername().' a été ajouté !'
            );

            return $this->redirectToRoute('adminAddRdv');

        }

        return $this->render('admin/adduser.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function admin_mailer(Request $request, RendezVousRepository $rendezVousRepository, Swift_Mailer $mailer): Response
    {
        $id = $request->get('id');
        if ($id >= 0) {
            $rdv = $rendezVousRepository->findOneBy(array('id' => $id));
            if ($rdv) {
                $username = $rdv->getUser()->getUsername();
                $service = $rdv->getService();
                $date = $rdv->getDate();
                $heure = $rdv->getHoraire();

                $this->addFlash(
                    'notice',
                    'Un email de rappel a été envoyé à '.$username.' à l\'adresse mail: '.$rdv->getUser()->getEmail()
                );

                $message = (new Swift_Message('Rappel Rendez-vous '))
                    ->setFrom('rendez-vous@amb-afg.fr')
                    ->setTo($rdv->getUser()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'rendez_vous/rappel.html.twig',
                            [
                                'username' => $username,
                                'service' => $service,
                                'date' => $date,
                                'heure' => $heure
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
     * @Route("/addRdv" , name="adminAddRdv",methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function rdvadminPage(Request $request, UserRepository $repository)
    {
        return $this->render("admin/addrdv.html.twig", array(
            'users' => $repository->findAll()
        ));
    }


    /**
     * @Route("/rdvadmin" ,name="rdvadmin",methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function adminrdv(Request $request, SerializerInterface $serializer, HoraireRepository $horaireRepository, UserRepository $userRepository, Swift_Mailer $mailer): Response
    {
        $userid = $request->get('getUser');
        $service = $request->get('getService');
        $date = $request->get('getDate');
        $heure = $request->get('getHeure');


        $data = ["userid" => $userid];
        $heureObject = $horaireRepository->findOneBy(array('heure' => $heure));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->find($userid);

        $userinfo = $userRepository->userInfo($userid);

        $username = $userinfo[0]['username'];
        $userEmail = $userinfo[0]['email'];


        if (!empty($service) or !empty($date) or !empty($heureObject)) {
            $rdv = new RendezVous();
            $rdv->setDate(DateTime::createFromFormat('Y-m-d', $date));
            $rdv->setUser($user);
            $rdv->setService($service);
            $rdv->setHoraire($heureObject);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rdv);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le rendez-vous pour l\'utilisateur: ' . $username . ' le service: ' . $service . ' à ' . $heure . ' à été pris !'
            );

            $message = (new Swift_Message('Serivce Rendez-vous '))
                ->setFrom('rendez-vous@amb-afg.fr')
                ->setTo($userEmail)
                ->setBody(
                    $this->renderView(
                        'rendez_vous/mail.html.twig',
                        [
                            'username' => $username,
                            'service' => $service,
                            'date' => $date,
                            'heure' => $heure
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

        }

        $response = new JsonResponse($data);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/delete/{id}",name="delete_rdv")
     */
    public function delete_rdv(Request $request, RendezVousRepository $repository): Response
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $rdv = $em->getRepository('App:RendezVous')->find($id);
        $em->remove($rdv);
        $em->flush();

        $this->addFlash(
            'notice',
            'Le rendez-vous n°'.$id.' à été supprimé'
        );

        return $this->redirectToRoute("rendez_vous_index");
    }

    /**
     * @Route("/detail_rdv/{id}", name="rdv_show")
     */
    public function show(int $id, RendezVousRepository $rendezVousRepository): Response
    {
        $rdv = $rendezVousRepository->find($id);

        return $this->render("rendez_vous/detailrdv.html.twig", array(
            "rendezvous" => $rdv
        ));
    }

    /**
     * @Route("/edit/{id}",name="edit_rdv",requirements={"id" = "\d+"})
     */
    public function edit_rdv(int $id, Request $request, RendezVous $rendezVous, RendezVousRepository $rendezVousRepository): Response
    {

        $rdv = $rendezVousRepository->find($id);

        return $this->render('rendez_vous/edit.html.twig', array(
            "rendez_vous" => $rdv,
        ));
    }

    /**
     * @Route("/modifrdvadmin" ,name="modifrdvadmin",methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function modification_rdv(Request $request, HoraireRepository $horaireRepository, Swift_Mailer $mailer)
    {
        $userid = $request->get('getUser');
        $service = $request->get('getService');
        $date = $request->get('getDate');
        $heure = $request->get('getHeure');
        $idRdv = $request->get('getRdvId');

        $data = ["userid" => $userid];
        $heureObject = $horaireRepository->findOneBy(array('heure' => $heure));

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository('App:User')->find($userid);

        $rdv = $entityManager->getRepository(RendezVous::class)->find($idRdv);
        if (!empty($service) or !empty($date) or !empty($heureObject)) {
            if (!$rdv) {
                throw $this->createNotFoundException(
                    'No product found for id ' . $idRdv
                );
            }

            $rdv->setUser($user);
            $rdv->setService($service);
            $rdv->setHoraire($heureObject);
            $rdv->setDate(DateTime::createFromFormat('Y-m-d', $date));
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le rendez-vous pour l\'utilisateur: ' . $rdv->getUser()->getUsername() . ' le service: ' . $service . ' à ' . $heure . ' à été pris !'
            );

            $message = (new Swift_Message('Serivce Rendez-vous '))
                ->setFrom('rendez-vous@amb-afg.fr')
                ->setTo($rdv->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'page/modification_mail.html.twig',
                        [
                            'username' => $rdv->getUser()->getUsername(),
                            'service' => $service,
                            'date' => $date,
                            'heure' => $heure
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);
        }
        $response = new JsonResponse($data);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


}
