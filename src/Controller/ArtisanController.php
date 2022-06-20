<?php

namespace App\Controller;

use App\Entity\Artisan;
use App\Form\ArtisanFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ArtisanRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    //Ajout d'un nouveau artisan
    
    #[Route('/artisan/new',name:'app_new_artisan')]
    public function newArtisan(Request $request, ArtisanRepository $artisanRepository) :Response
    {
        $artisan = new Artisan();

        $form = $this->createForm(ArtisanFormType::class, $artisan);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 

            $artisanRepository->add($artisan, true);

            $this->addFlash(
               'success',
               'Votre magazine a bien été ajouté !'
            );

            //$magazine = new Magazine();
            //$form = $this->createForm(MagazineFormType::class, $magazine);

            return $this->redirectToRoute('app_artisan');
            
        }

        return $this->render('artisan/newArtisan.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/artisan/edit/{id}', name: 'app_edit_artisan', requirements:["id"=>"\d+"])]
    public function edit (Artisan $artisan, ArtisanRepository $artisanRepository, Request $request, int $id )
    {


	    $form=$this->createForm(ArtisanFormType::class, $artisan);
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

    #[Route('/artisan/delet/{id}', name:'app_delete_artisan', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    
    public function remove(Artisan $artisan): Response
    
    {
        // utilisation du voter nommée ArticleVoter
        $this->denyAccessUnlessGranted('ARTISAN_DELETE', $artisan);
        return $this->render('article/delete.html.twig', [
            'article' => $artisan
            
        ]);

        return $this->redirectToRoute('app_artisan');
    }
}
