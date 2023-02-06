<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/wish', name: 'wish')]
class WishController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function list(
        WishRepository $wishRepository
    ): Response
    {
        $wishes = $wishRepository->findBy([], ["dateCreated" => "DESC"]);
        return $this->render(
            'wish/list.html.twig',
            compact('wishes'));
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function detail(
    int         $id,
    WishRepository $wishRepository
): Response
{
    $wish = $wishRepository->findOneBy(["id" => $id]);
    return $this->render('wish/details.html.twig',
    compact('wish')
        );
    }
    #[Route('/add', name: '_add')]
    public function add(
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);
        if ($wishForm->isSubmitted()) {
            try {
                $wish->setIsPublished(true);
                $wish->setDateCreated(new \DateTime());
                if ($wishForm->isValid()) {
                $em->persist($wish);
                }
            }catch(Exception $e){
                dd($e->getMessage());
            }
            $em->flush();
            $this->addFlash('success', 'Idée Créé!');
            return $this->redirectToRoute('wish_detail', ["id" =>$wish->getId()]);
        }
        return $this->render(
            'wish/add.html.twig',
            compact('wishForm')
        );
    }
}
