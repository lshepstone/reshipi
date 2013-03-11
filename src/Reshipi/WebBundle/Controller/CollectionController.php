<?php

namespace Reshipi\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Reshipi\WebBundle\Entity\CollectionEntity;
use Reshipi\WebBundle\Model\Exception\EntityAlreadyExistsException;
use Reshipi\WebBundle\Http\Exception\ConflictHttpException;

class CollectionController extends Controller
{
    /**
     * @Route("/")
     * @Method({"GET"})
     * @Template()
     */
    public function allAction()
    {
        return array(
            'collections' => $this->get('reshipi_web.model_collection')->getAll()
        );
    }

    /**
     * @Route("/collections/{slug}")
     * @Method({"GET"})
     * @Template()
     */
    public function getAction($slug)
    {
        try {
            $collection = $this->get('reshipi_web.model_collection')->getOneBySlug($slug);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException("Collection '{$slug}' was not found'");
        }

        return array(
            'collection' => $collection,
            'recipes' => $collection->getRecipes()
        );
    }

    /**
     * @Route("/collections")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $collection = new CollectionEntity();
        $collection->setName($request->get('name'));

        try {
            $this->get('reshipi_web.model_collection')->add($collection);
        } catch (EntityAlreadyExistsException $exception) {
            throw new ConflictHttpException("Collection '{$collection->getName()}' already exists");
        }

        return $this->redirect($this->generateUrl('reshipi_web_collection_all'));
    }
}
