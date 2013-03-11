<?php

namespace Reshipi\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ArchiveController extends Controller
{
    /**
     * @Route("/archive/{slug}")
     */
    public function getAction($slug)
    {
        try {
            $collection = $this->get('reshipi_web.model_collection')->getOneBySlug($slug);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException("Collection '{$slug}' was not found'");
        }

        $path = $this->get('reshipi_web.model_collection')->getArchiveForCollection($collection);
        $file = basename($path);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$file}\"");
        $response->setContent(file_get_contents($path));

        unlink($path);

        return $response;
    }
}
