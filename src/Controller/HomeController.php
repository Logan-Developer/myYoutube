<?php

namespace App\Controller;

use App\Entity\Youtube;
use App\Form\YoutubeType;
use App\Repository\YoutubeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const ERROR_MSG = 'Une erreur est survenue, veuillez réessayer plus tard.';

    /**
     * @Route("/", name="app_home")
     * @param YoutubeRepository $repository
     * @param Request $request
     * @param EntityManager $entityManager
     * @return Response
     */
    public function index(YoutubeRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $youtube = new Youtube();

        $youtubeForm = $this->createForm(YoutubeType::class, $youtube);
        $youtubeForm->handleRequest($request);

        if ($youtubeForm->isSubmitted()) {

            if ($youtubeForm->isValid()) {
                try {
                    $entityManager->persist($youtube);
                    $entityManager->flush();
                    $this->addFlash('success', 'Vidéo ajoutée avec succès!');

                } catch (Exception $e) {
                    $this->addFlash('danger', self::ERROR_MSG);
                }
            } else {
                $this->addFlash('danger', self::ERROR_MSG);
            }
        }

        return $this->render('home/index.html.twig', [
            'youtube_form' => $youtubeForm->createView(),
            'youtubeVideos' => $repository->findAll()
        ]);
    }

    /**
     * @Route("/video/{id}", name="app_video")
     * @param YoutubeRepository $repository
     * @param $id
     * @return Response
     */
    public function watchVideo(YoutubeRepository $repository, $id): Response {

        return $this->render('home/video.html.twig', [
            'youtubeVideo' => $repository->findOneBy(['id'=>$id])
        ]);
    }

    /**
     * @Route("/video/{id}/modify", name="app_video_modify", methods={"GET","PUT"})
     * @param YoutubeRepository $repository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function modifyVideo(YoutubeRepository $repository, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $youtubeVideo = $repository->findOneBy(['id' => $id]);


        $youtubeForm = $this->createForm(YoutubeType::class, $youtubeVideo, ['method'=>'PUT']);
        $youtubeForm->handleRequest($request);

        if ($youtubeForm->isSubmitted()) {

            if ($youtubeForm->isValid()) {
                try {
                    $entityManager->persist($youtubeVideo);
                    $entityManager->flush();
                    $this->addFlash('success', 'Vidéo modifiée avec succès!');

                } catch (Exception $e) {
                    $this->addFlash('danger', self::ERROR_MSG);
                }
            } else {
                $this->addFlash('danger', self::ERROR_MSG);
            }
        }

        return $this->render('home/modify.html.twig', [
            'youtube_form' => $youtubeForm->createView(),
            'youtubeVideo' => $youtubeVideo
        ]);
    }

    /**
     * @Route("/video/{id}/delete", name="app_video_delete", methods={"DELETE"})
     * @param YoutubeRepository $repository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function deleteVideo(YoutubeRepository $repository, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $youtubeVideo = $repository->findOneBy(['id'=>$id]);

        if ($youtubeVideo !== null && $this->isCsrfTokenValid('DELETE' . $id, $request->get('_token'))) {
            try {
                $entityManager->remove($youtubeVideo);
                $entityManager->flush();
                $this->addFlash('success', 'Vidéo supprimée avec succès!');

            } catch (Exception $e) {
                $this->addFlash('danger', self::ERROR_MSG);
            }
        } else {
            $this->addFlash('danger', self::ERROR_MSG);
        }

        return $this->redirectToRoute('app_home');
    }
}
