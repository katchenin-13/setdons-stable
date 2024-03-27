<?php

namespace App\Controller\Gestion;

use App\Controller\BaseController;
use App\Entity\Agenda;
use App\Repository\AgendaRepository;
use App\Repository\CalendrierRepository;
use App\Repository\EventRepository;
use App\Service\StatsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/gestion/calendrier')]
class CalendrierController extends BaseController
{

      /***
     *  @[Route("/api/{id}/edit",name="api_event_edit",method='PUT')]
     */
    public function majEvent(?Agenda $agenda, Request $request, AgendaRepository $agendaRepository)
    {
       
        // On récupère les données
        $donnees = json_decode($request->getContent());
  
        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) 
            // isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            // isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            // isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if(!$agenda){
                // On instancie un rendez-vous
                $agenda = new Agenda;

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $agenda->setLibelle($donnees->title);
            $agenda->setDescription($donnees->description);
            $agenda->setStart(new DateTime($donnees->start));
            if($donnees->allDay){
                $agenda->setEnd(new DateTime($donnees->start));
            }else{
                $agenda->setEnd(new DateTime($donnees->end));
            }
            $agenda->setAllDay($donnees->allDay);
            $agenda->setBackgroundColor($donnees->backgroundColor);
            $agenda->setBorderColor($donnees->borderColor);
            $agenda->setTextColor($donnees->textColor);

            // $em = $this->getDoctrine()->getManager();
            $agendas = $agendaRepository->save($agenda,true);
             
            // $entityManager->persist($agenda);
            // $entityManager->flush();

            // On retourne le code
            // return new Response('Ok', $code);
            return $this->json([
                'code' => 200,
                'message' => 'ça marche bien',
                'agenda' => $agendas
                // 'mention' =>$parent->isMentions(),
            ], 200);
        }else{
            // Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }

        // $entityManager->persist($agenda);
        // $entityManager->flush();
        // return $this->json([
        //     'code' => 200,
        //     'message' => 'ça marche bien',
        //     'agenda' => $agendas
        //     // 'mention' =>$parent->isMentions(),
        // ], 200);

        // return $this->render('api/index.html.twig', [
        //     'controller_name' => 'ApiController',
        // ]);
    }
    #[Route('/gestion/calendrier', name: 'app_gestion_calendrier')]
    public function index(EventRepository $eventRepository,StatsService $statsService): Response
    {


        $events = $eventRepository->findAll();
        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'title' => $event->getNom(),
                // 'description' => $event->getDescription(),
                'start' => $event->getStartdate()->format('Y-m-d'),
                'end' => $event->getEnddate()->format('Y-m-d'),
            ];
        }
        $demandecefuture = $statsService->getCalendrier();
        $audiencesAvenir = [];
        foreach ($demandecefuture as $audiancefutures) {
            $audiencesAvenir[] = [
                'title' => $audiancefutures->getMotif(),
               
                'start' => $audiancefutures->getDaterencontre()->format('Y-m-d'),
                'end' => $audiancefutures->getDaterencontre()->format('Y-m-d'),
            ];
        }
        $datas = array_merge($data, $audiencesAvenir);
    
        $data = json_encode($datas);
        //dd($datas);
        return $this->render('gestion/calendrier/aganda.html.twig', [
            'data' => $data,
        ]);
    }
}
