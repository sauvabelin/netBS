<?php

namespace NetBS\CoreBundle\Controller;

use Doctrine\Common\Collections\Collection;
use NetBS\CoreBundle\Exceptions\UserConstraintException;
use NetBS\CoreBundle\Model\XEditable;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class XEditableController extends Controller
{
    /**
     * @Route("/utils/xeditable", name="netbs.core.xeditable.endpoint")
     * @param Request $request
     * @return JsonResponse
     */
    public function endpointAction(Request $request)
    {
        $xeditable  = new XEditable($request);
        $em         = $this->get('doctrine.orm.entity_manager');
        $accessor   = $this->get('property_accessor');
        $item       = $em->find(base64_decode($xeditable->getData('itemClass')), $xeditable->getId());
        $type       = $this->get('form.type.' . $xeditable->getBaseType());

        if(!$this->isGranted(CRUD::UPDATE, $item))
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cet élément.");

        $form       = $this->createFormBuilder($item, array('csrf_protection' => false))
            ->add($xeditable->getField(), get_class($type), $xeditable->getTypeOptions())
            ->getForm();

        $form->submit(array($xeditable->getField() => $xeditable->getFinalValue()));

        if($form->isValid()) {

            $item   = $form->getData();

            try {
                $em->persist($item);
                $em->flush();
            } catch(UserConstraintException $exception) {
                return new JsonResponse(['message' => $exception->getMessage()], 400);
            }

            $value  = $accessor->getValue($item, $xeditable->getField());

            if(is_object($value) && method_exists($value, '__toString'))
                $value = $value->__toString();

            elseif(is_array($value) || $value instanceof Collection) {

                $rv    = [];
                if(is_object($value[0]))
                    foreach($value as $item)
                        $rv[] = $item->getId();

                $value = implode(',', $rv);
            }

            return $this->json(['newValue' => $xeditable->getFinalValue(), 'newLabel' => $value]);
        }

        else {

            //return new JsonResponse($this->getErrorMessages($form), 400);

            $str = "";
            foreach($this->getErrorMessages($form) as $message)
                $str .= $message[0];

            return new JsonResponse(['message' => $str], 400);
        }

    }

    /**
     * Retournes les erreurs du formulaire
     * @param \Symfony\Component\Form\Form $form
     * @return array
     */
    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

}
