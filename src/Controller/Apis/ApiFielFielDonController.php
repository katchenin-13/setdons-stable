<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Fieldon;

use App\Repository\FieldonRepository;
use App\Repository\ModuleGroupePermitionRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\String\toString;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api/fieldon')]
class ApiFielDonController extends ApiInterface
{
    #[Route('/', name: 'api_fieldon', methods: ['GET'])]
    /**
     * Affiche toutes les FielFielDon.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Fieldon::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Fieldon")
     * @Security(name="Bearer")
     */
    public function getAll(FieldonRepository $fieldonRepository,Request $request): Response
    {
        try {
            $fieldons = $fieldonRepository->findAll();
            $total_items =$fieldonRepository->countItems();
            $tabaFielDon = [];
      
            $item = [];
            $i = 0;
            $donEnNature = [];
            $donEnEspece = [];
            $i = 0;
           

       
            foreach ($fieldons as $key => $value) {
               
                        $item[$i]['id'] = $value->getId();
                        $item[$i]['dateremise'] = $value->getDon()->getDateremise();
                        $item[$i]['communaute'] = $value->getDon()->getCommunaute()->getLibelle();
                        $item[$i]['recipiandaire'] = $value->getDon()->getNom();
                        $item[$i]['remise'] = $value->getDon()->getRemispar();
                        $item[$i]['nature'] = $value->getNaturedon();
                        $item[$i]['quantite'] = $value->getQte();
                        $item[$i]['motif'] = $value->getMotifdon();
                        $item[$i]['valeur'] = $value->getMontantdon();
                
                        $item[$i]['typedonsfiel'] = $value->getTypedonsfiel() ;
              
                // if ($value->getTypedonsfiel() == "en_nature")
                //         $donEnNature[$i]['id'] = $value->getId();
                //         $donEnNature[$i]['dateremise'] = $value->getDon()->getDateremise();
                //         $donEnNature[$i]['communaute'] = $value->getDon()->getCommunaute()->getLibelle();
                //         $donEnNature[$i]['recipiandaire'] = $value->getDon()->getNom();
                //         $donEnNature[$i]['remise'] = $value->getDon()->getRemispar();
                //         $donEnNature[$i]['nature'] = $value->getNaturedon();
                //         $donEnNature[$i]['quantite'] = $value->getQte();
                //         $donEnNature[$i]['motif'] = $value->getMotifdon();
                //         $donEnNature[$i]['valeur'] = $value->getMontantdon();
                //         $donEnNature[$i]['valeur'] = $value->getMontantdon();
                      
                // if ($value->getTypedonsfiel()== "en_espece")
                //         $donEnEspece[$i]['id'] = $value->getId();
                //         $donEnEspece[$i]['dateremise'] = $value->getDon()->getDateremise();
                //         $donEnEspece[$i]['communaute'] = $value->getDon()->getCommunaute()->getLibelle();
                //         $donEnEspece[$i]['recipiandaire'] = $value->getDon()->getNom();
                //         $donEnEspece[$i]['remise'] = $value->getDon()->getRemispar();
                //         // $donEnEspece[$i]['nature'] = $value->getNaturedon();
                //         // $donEnEspece[$i]['quantite'] = $value->getQte();
                //         $donEnEspece[$i]['motif'] = $value->getMotifdon();
                //         $donEnEspece[$i]['valeur'] = $value->getMontantdon();


              
                $i++;
                
            }
            $tabaFielPromesse = [
                'total_count' => $total_items,
                'items' => $item,

            ];
            $response = $this->response($tabaFielDon);
            //dd($fieldons);
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }

        // On envoie la réponse

        return $response;
    }


    // #[Route('/getOne/{id}', name: 'api_fieldon_get_one', methods: ['GET'])]
    // /**
    //  * Affiche une civilte en offrant un identifiant.
    //  * @OA\Tag(name="FielDon")
    //  * @Security(name="Bearer")
    //  */
    // public function getOne(?Fieldon $fieldon)
    // {
    //     /*  $fieldon = $fieldonRepository->find($id);*/
    //     try {
    //         if ($fieldon) {
    //             $response = $this->response($fieldon);
    //         } else {
    //             $this->setMessage('Cette ressource est inexistante');
    //             $this->setStatusCode(300);
    //             $response = $this->response($fieldon);
    //         }
    //     } catch (\Exception $exception) {
    //         $this->setMessage($exception->getMessage());
    //         $response = $this->response(null);
    //     }


    //     return $response;
    // }


    // #[Route('/create', name: 'api_fieldon_create', methods: ['POST'])]
    // /**
    //  * Permet de créer une FielDon.
    //  *
    //  * @OA\Tag(name="FielDon")
    //  * @Security(name="Bearer")
    //  */
    // public function create(Request $request, FielDonRepository $fieldonRepository)
    // {
    //     try {
    //         $data = json_decode($request->getContent());

    //         $fieldon = $fieldonRepository->findOneBy(array('code' => $data->code));
    //         if ($fieldon == null) {
    //             $fieldon = new FielDon();
    //             //$fieldon->setMotif($data->code);
    //             //$fieldon->setCommunaute($data->libelle);

    //             // On sauvegarde en base
    //             $fieldonRepository->add($fieldon, true);

    //             // On retourne la confirmation
    //             $response = $this->response($fieldon);
    //         } else {
    //             $this->setMessage("cette ressource existe deja en base");
    //             $this->setStatusCode(300);
    //             $response = $this->response(null);
    //         }
    //     } catch (\Exception $exception) {
    //         $this->setMessage($exception->getMessage());
    //         $response = $this->response(null);
    //     }


    //     return $response;
    // }


    // #[Route('/update/{id}', name: 'api_fieldon_update', methods: ['POST'])]
    // /**
    //  * Permet de mettre à jour une FielDon.
    //  *
    //  * @OA\Tag(name="FielDon")
    //  * @Security(name="Bearer")
    //  */
    // public function update(Request $request, FielDonRepository $fieldonRepository, $id)
    // {
    //     try {
    //         $data = json_decode($request->getContent());

    //         $fieldon = $fieldonRepository->find($id);
    //         if ($fieldon != null) {

    //            // $fieldon->setCode($data->code);
    //             //$fieldon->setLibelle($data->libelle);

    //             // On sauvegarde en base
    //             $fieldonRepository->add($fieldon, true);

    //             // On retourne la confirmation
    //             $response = $this->response($fieldon);
    //         } else {
    //             $this->setMessage("cette ressource est inexsitante");
    //             $this->setStatusCode(300);
    //             $response = $this->response(null);
    //         }
    //     } catch (\Exception $exception) {
    //         $this->setMessage($exception->getMessage());
    //         $response = $this->response(null);
    //     }
    //     return $response;
    // }


    // #[Route('/delete/{id}', name: 'api_fieldon_delete', methods: ['POST'])]
    // /**
    //  * permet de supprimer une FielDon en offrant un identifiant.
    //  *
    //  * @OA\Tag(name="FielDon")
    //  * @Security(name="Bearer")
    //  */
    // public function delete(Request $request, FielDonRepository $fieldonRepository, $id)
    // {
    //     try {
    //         $data = json_decode($request->getContent());

    //         $fieldon = $fieldonRepository->find($id);
    //         if ($fieldon != null) {

    //             $fieldonRepository->remove($fieldon, true);

    //             // On retourne la confirmation
    //             $response = $this->response($fieldon);
    //         } else {
    //             $this->setMessage("cette ressource est inexistante");
    //             $this->setStatusCode(300);
    //             $response = $this->response(null);
    //         }
    //     } catch (\Exception $exception) {
    //         $this->setMessage($exception->getMessage());
    //         $response = $this->response(null);
    //     }
    //     return $response;
    // }


    // #[Route('/active/{id}', name: 'api_fieldon_active', methods: ['GET'])]
    // /**
    //  * Permet d'activer une FielDon en offrant un identifiant.
    //  * @OA\Tag(name="FielDon")
    //  * @Security(name="Bearer")
    //  */
    // public function active(?Fieldon $fieldon, FielDonRepository $fieldonRepository)
    // {
    //     /*  $fieldon = $fieldonRepository->find($id);*/
    //     try {
    //         if ($fieldon) {

    //             //$fieldon->setCode("555"); //TO DO nous ajouter un champs active
    //             $fieldonRepository->add($fieldon, true);
    //             $response = $this->response($fieldon);
    //         } else {
    //             $this->setMessage('Cette ressource est inexistante');
    //             $this->setStatusCode(300);
    //             $response = $this->response(null);
    //         }
    //     } catch (\Exception $exception) {
    //         $this->setMessage($exception->getMessage());
    //         $response = $this->response(null);
    //     }


    //     return $response;
    // }


    // #[Route('/active/multiple', name: 'api_audeince_active_multiple', methods: ['POST'])]
    // /**
    //  * Permet de faire une desactivation multiple.
    //  *
    //  * @OA\Tag(name="FielDon")
    //  * @Security(name="Bearer")
    //  */
    // public function multipleActive(Request $request, FielDonRepository $fieldonRepository)
    // {
    //     try {
    //         $data = json_decode($request->getContent());

    //         $listefieldons = $fieldonRepository->findAllByListId($data->ids);
    //         foreach ($listefieldons as $listefieldon) {
    //             //$listeFielDon->setCode("555");  //TO DO nous ajouter un champs active
    //             $fieldonRepository->add($listefieldon, true);
    //         }

    //         $response = $this->response(null);
    //     } catch (\Exception $exception) {
    //         $this->setMessage($exception->getMessage());
    //         $response = $this->response(null);
    //     }
    //     return $response;
    // }
}
