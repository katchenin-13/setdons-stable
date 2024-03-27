<?php

namespace App\Controller\Gestion;

use App\Controller\BaseController;
use App\Entity\Agenda;
use App\Form\AgendaType;
use App\Repository\AgendaRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestion/agenda')]
class AgendaController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_gestion_agenda_index';
    #[Route('/', name: 'app_gestion_agenda_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
        ->add('libelle', TextColumn::class, ['label' => 'Nom et Libéllé'])
        ->add('description', TextColumn::class, ['label' => 'Description'])
        ->add('start', DateTimeColumn::class, [
            'label' => 'Date de debut',
            "format" => 'Y-m-d'
        ])
        ->add('end', DateTimeColumn::class, [
            'label' => 'Date de fin',
            "format" => 'Y-m-d'
        ])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Agenda::class,
        ])
        ->setName('dt_app_gestion_agenda');
        if ($permission != null) {
            $renders = [
                'edit' =>  new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'RUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    } else {
                        return true;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'RUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    } else {
                        return true;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'RUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    } else {
                        return true;
                    }
                    return true;
                }),

            ];



            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Agenda $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'show' => [
                                    'url' => $this->generateUrl('app_gestion_agenda_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-success'], 'render' => $renders['show']
                                ],
                                'edit' => [
                                    'url' => $this->generateUrl('app_gestion_agenda_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_gestion_agenda_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-danger'],  'render' => $renders['delete']
                                ]
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }

        }
     
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('gestion/agenda/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }
    
   #[Route('/new', name: 'app_gestion_agenda_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AgendaRepository $agendaRepository, FormError $formError): Response
    {
        $agenda = new Agenda();
        $form = $this->createForm(AgendaType::class, $agenda, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_gestion_agenda_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_gestion_agenda_index');




            if ($form->isValid()) {

                $agendaRepository->save($agenda, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->renderForm('gestion/agenda/new.html.twig', [
            'agenda' => $agenda,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_gestion_agenda_show', methods: ['GET'])]
    public function show(Agenda $agenda): Response
    {
        return $this->render('gestion/agenda/show.html.twig', [
            'agenda' => $agenda,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestion_agenda_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agenda $agenda, AgendaRepository $agendaRepository, FormError $formError): Response
    {

        $form = $this->createForm(AgendaType::class, $agenda, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_gestion_agenda_edit', [
                    'id' =>  $agenda->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_gestion_agenda_index');


            if ($form->isValid()) {

                $agendaRepository->save($agenda, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('gestion/agenda/edit.html.twig', [
            'agenda' => $agenda,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_gestion_agenda_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Agenda $agenda, AgendaRepository $agendaRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_gestion_agenda_delete'
                ,   [
                        'id' => $agenda->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $agendaRepository->remove($agenda, true);

            $redirect = $this->generateUrl('app_gestion_agenda_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('gestion/agenda/delete.html.twig', [
            'agenda' => $agenda,
            'form' => $form,
        ]);
    }
}
