<?php

namespace App\Controller;

use App\Entity\Artisan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ArtisanRepository;
use Symfony\Component\HttpFoundation\Request;

class ArtisanController extends AbstractController
{
    #[Route('/artisan', name: 'app_artisan')]
    public function index(ArtisanRepository $artisanRepository, PaginatorInterface $paginatorInterface, Request $request ): Response
    {
        $artisans = $paginatorInterface->paginate(
            $artisanRepository->findAll(), //Requête SQL/DQL
            $request->query->getInt('page', 1), //Numéritation des pages 
            $request->query->getInt('numbers', 5) //Nombre d'enregistrement par page
        );
        return $this->render('artisan/index.html.twig', [
            'artisans' => $artisans,
            'details' => $artisanRepository->find(5)
        ]);
    }

    #[Route('/artisan/edit', name: 'app_edit_artisan', requirements:["id"=>"\d+"])]
    public function edit (Artisan $artisan, ArtisanRepository $artisanRepository, Request $request, int $id )
    {


	    $form=$this->createdForm(ArtisanFormType::class, $artisan);
	    $form->handleRequest($request); 
	    if ($form->isSubmitted() && $form->isValid()) {

           	$artisanRepository->add($artisan, true);

            $this->addFlash('success', 'Votre categorie a bien été modifié !');

           	return $this->redirectToRoute('app_artisan');
        }

        	return $this->render('artisan/edit.html.twig', [
           	 'form'=> $form->createView()
        ]);
    }

    #[Route('/artisan/delet/{id}', name:'app_delete_artisan', requirements: ['id' => '\d+'], methods: ['POST'])]
    
    public function remove(Artisan $artisan, Request $request, ArtisanRepository $artisanRepository): RedirectResponse
    
    {
        $tokenCsrf = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-artisan-'. $categorie->getId(), $tokenCsrf)) 
        {
            $categorieRepository->remove($categorie, true);

            $this->addFlash('success', 'La catégorie à bien été supprimée');
        }

        return $this->redirectToRoute('app_artisan');
    }
}
