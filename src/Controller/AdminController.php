<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ActeurType;
use App\Form\GenreType;
use App\Form\FilmType;
use App\Entity\Acteur;
use App\Entity\Film;
use App\Entity\Genre;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(Request $request): Response
    {
        $genre= $request->request->get('genre');
        $film= $request->request->get('film');
        $em=$this->getDoctrine()->getRepository(Film::class)->findAll();
        $emG=$this->getDoctrine()->getRepository(Genre::class)->findAll();
        $FOBN=$this->getDoctrine()->getRepository(Film::class)->findOneBy(['titre' => $film]);

        return $this->render('home.html.twig', [
            'controller_name' => 'AdminController',
            'AllFilm' => $em,
            'AllGenre' => $emG,
            'genre' => $genre,
            'film' => $film,
            'res' => $FOBN
        ]);
    }

    /**
     * @Route("/Details/{id}", name="homeEdit")
     */
    public function homeEdit(Request $request): Response
    {
        $em=$this->getDoctrine()->getRepository(Film::class)->findAll();
        return $this->render('homeEdit.html.twig', [
            'controller_name' => 'AdminController',
            
        ]);
    }

    /**
     * @Route("/Acteur", name="Acteur")
     */
    public function Acteur(Request $request): Response
    {


        $acteur=new Acteur();
       /* $film=new Film();
        $film->setTitre('titre1');
        $film->setDuree(15);
        $date = "01-09-2015";
        $film->setDateSortie(\DateTime::createFromFormat('d-m-Y', $date));
        $film->setNote(26);
        $film->setAgeMinimal(24);*/
        $form=$this->createForm(ActeurType::class,$acteur);
        $emG=$this->getDoctrine()->getRepository('App:Film')->findAll();

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                //$film=$this->$request->request->get('filmX');
                $film=$request->request->get('FILM');
                $ResFilm=$this->getDoctrine()->getRepository('App:Film')->findOneBy(['titre' => $film]);
                if ($ResFilm === null) {
                    # code...
                    $em=$this->getDoctrine()->getManager();
                    $em->persist($acteur);
                
                    $em->flush();
                }else
                {
                    $em=$this->getDoctrine()->getManager();
                //$em->persist($film);
                $acteur->setFilm($ResFilm);
                
                $em->persist($acteur);
                
                $em->flush();
                }
                
                // perform some action...
                $this->addFlash('success', 'Acteur a était bien ajouté');
                return $this->redirectToRoute('Acteur');
            }
        }

        $em=$this->getDoctrine()->getRepository('App:Acteur')->findAll();
        return $this->render('admin/acteur.html.twig', [
            'controller_name' => 'AdminController',
            'form'  =>  $form->createView(),
            'acteur' => $em,
            'film'  => $emG,
            
        ]);
    }

    /**
     * @Route("/film", name="film")
     */
    public function film(Request $request): Response
    {
        $film=new film();
        
        $form=$this->createForm(FilmType::class,$film);
        $emG=$this->getDoctrine()->getRepository('App:genre')->findAll();
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
    
            if ($form->isSubmitted() && $form->isValid()) {
                $genre= $request->request->get('genre');
                $emG=$this->getDoctrine()->getRepository('App:genre')->findOneBy(['nom' => $genre]);
                
                $em=$this->getDoctrine()->getManager();
                
                $film->setGenre($emG);
                $em->persist($film);
                
                $em->flush();
                // perform some action...
                $this->addFlash('success', 'film a était bien ajouté');
                return $this->redirectToRoute('film');
            }
        }

        $em=$this->getDoctrine()->getRepository('App:film')->findAll();
        return $this->render('admin/film.html.twig', [
            'controller_name' => 'AdminController',
            'form'  =>  $form->createView(),
            'film' => $em,
            'nom' => $emG
        ]);
      
    }

    /**
     * @Route("/genre", name="genre")
     */
    public function genre(Request $request): Response
    {
        $genre=new genre();
    
        $form=$this->createForm(GenreType::class,$genre);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em=$this->getDoctrine()->getManager();
                
                $em->persist($genre);
                
                $em->flush();
                // perform some action...
                $this->addFlash('success', 'genre a était bien ajouté');
                return $this->redirectToRoute('genre');
            }
        }

        $em=$this->getDoctrine()->getRepository('App:genre')->findAll();
        return $this->render('admin/genre.html.twig', [
            'controller_name' => 'AdminController',
            'form'  =>  $form->createView(),
            'genre' => $em
        ]);
    }


    /**
     * @Route("/acteurDel/{id}", name="acteurDel")
     */
    public function acteurDel($id): Response
    {
        $em=$this->getDoctrine()->getManager();
        $res = $em->getRepository(Acteur::class);
        $Acteur=$res->find($id);
        $em->remove($Acteur);
        $em->flush(); 
        $this->addFlash('danger', 'Acteur a était bien supprimé');
        return $this->redirectToRoute('Acteur');
    }

    /**
     * @Route("/acteurEdit/{id}", name="acteurEdit")
     */
    public function acteurEdit(Acteur $acteur,Request $request): Response
    {
        $form=$this->createForm(ActeurType::class,$acteur);
        $emG=$this->getDoctrine()->getRepository('App:Film')->findAll();

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
    
            if ($form->isSubmitted() && $form->isValid()) {
                
                $film=$request->request->get('FILM');
                $ResFilm=$this->getDoctrine()->getRepository('App:Film')->findOneBy(['titre' => $film]);

                $em=$this->getDoctrine()->getManager();
                $acteur->setFilm($ResFilm);
                $em->flush();
                // perform some action...
                $this->addFlash('success', 'Acteur a était bien modifié');
                return $this->redirectToRoute('Acteur');
            }
        }
        return $this->render('admin/acteurEdit.html.twig', [
            'controller_name' => 'AdminController',
            'form'  =>  $form->createView(),
            'film'  =>  $emG
        ]);
    }

     /**
     * @Route("/GenreDel/{id}", name="GenreDel")
     */
    public function GenreDel($id): Response
    {
        $em=$this->getDoctrine()->getManager();
        $res = $em->getRepository(Genre::class);
        $Genre=$res->find($id);
        $em->remove($Genre);
        $em->flush(); 
        $this->addFlash('danger', 'Genre a était bien supprimé');
        return $this->redirectToRoute('genre');
    }

     /**
     * @Route("/GenreEdit/{id}", name="GenreEdit")
     */
    public function GenreEdit(Genre $genre,Request $request): Response
    {
        $form=$this->createForm(GenreType::class,$genre);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
    
            if ($form->isSubmitted() && $form->isValid()) {
                
       
                $em=$this->getDoctrine()->getManager();
                $em->flush();
                // perform some action...
                $this->addFlash('success', 'Genre a était bien modifié');
                return $this->redirectToRoute('genre');
            }
        }
        return $this->render('admin/GenreEdit.html.twig', [
            'controller_name' => 'AdminController',
            'form'  =>  $form->createView(),
        ]);
    }

       /**
     * @Route("/filmDel/{id}", name="filmDel")
     */
    public function filmDel($id): Response
    {
        $em=$this->getDoctrine()->getManager();
        $res = $em->getRepository(Film::class);
        $Film=$res->find($id);
        $em->remove($Film);
        $em->flush(); 
        $this->addFlash('danger', 'film a était bien supprimé');
        return $this->redirectToRoute('film');
    }
}
