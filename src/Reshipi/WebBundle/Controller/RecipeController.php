<?php

namespace Reshipi\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Reshipi\WebBundle\Entity\RecipeEntity;
use Reshipi\WebBundle\Model\Exception\EntityAlreadyExistsException;
use Reshipi\WebBundle\Model\Exception\EntityNotFoundException;
use Reshipi\WebBundle\Http\Exception\ConflictHttpException;

class RecipeController extends Controller
{
    /**
     * @Route("/recipes")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $slug = $request->get('collection');
        $collections = $this->get('reshipi_web.model_collection');
        $recipes = $this->get('reshipi_web.model_recipe');

        try {
            $collection = $collections->getOneBySlug($slug);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException("Collection '{$slug}' was not found'");
        }

        $recipe = new RecipeEntity();
        $recipe->setName($request->get('name'))
            ->setUrl($request->get('url'));

        try {
            $recipes->add($recipe);
        } catch (EntityAlreadyExistsException $exception) {
            $recipe = $exception->getEntity();
        }

        $collection->addRecipe($recipe);
        $collections->update($collection);

        return $this->redirect($this->generateUrl('reshipi_web_collection_get', array(
            'slug' => $collection->getSlug())
        ));
    }
}
