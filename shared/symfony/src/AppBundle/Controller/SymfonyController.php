<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/symfony")
 */
class SymfonyController extends Controller
{
    /**
     * @Route("/", name="symfony_root")
     */
    public function rootAction(Request $request)
    {
        $userManager = $this->get('ekino.wordpress.manager.user');
        $postManager = $this->get('ekino.wordpress.manager.post');

        $users = $userManager->findAll();

        $pages = $postManager->findBy([
            'postType'   => 'page',
            'postStatus' => 'publish',
        ]);

        $posts = $postManager->findBy([
            'postType'   => 'post',
            'postStatus' => 'publish',
        ]);

        return $this->render('AppBundle:symfony:symfony.html.twig', [
            'users' => $users,
            'pages' => $pages,
            'posts' => $posts,
        ]);
    }
}
