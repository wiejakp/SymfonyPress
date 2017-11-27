<?php

namespace AppBundle\Controller;

use Psr\Log\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WordPressController
 *
 * @package AppBundle\Controller
 *
 * @Route("/wordpress")
 */
class WordPressController extends Controller
{
    /**
     * @Route("/", name="wordpress_root")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rootAction(Request $request)
    {
        $optionsManager = $this->get('ekino.wordpress.manager.option');
        $permalink = $optionsManager->findOneByOptionName('permalink_structure');

        return $this->render('AppBundle:wordpress:root.html.twig', [
            'permalink' => $permalink
        ]);
    }

    /**
     * @Route("/rewrite/change/{type}", name="wordpress_rewrite_change")
     *
     * @param Request $request
     * @param int     $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeRewrite(Request $request, int $type)
    {
        $types = [
            0 => '/%year%/%monthnum%/%day%/%postname%/',
            1 => '/%postname%/',
        ];

        if (!array_key_exists($type, $types)) {
            throw new \InvalidArgumentException();
        }

        $optionsManager = $this->get('ekino.wordpress.manager.option');

        $permalink = $optionsManager->findOneByOptionName('permalink_structure');
        $permalink->setOptionValue($types[$type]);

        $optionsManager->save($permalink, true);

        return $this->redirectToRoute('wordpress_root');
    }

    /**
     * @Route("/rewrite/clear", name="wordpress_rewrite_refresh")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function refreshRewrite()
    {
        $optionsManager = $this->get('ekino.wordpress.manager.option');

        $rewrites = $optionsManager->findOneByOptionName('rewrite_rules');
        $rewrites->setOptionValue('');

        $optionsManager->save($rewrites, true);

        return $this->redirectToRoute('wordpress_root');
    }
}
