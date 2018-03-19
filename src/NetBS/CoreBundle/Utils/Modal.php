<?php

namespace NetBS\CoreBundle\Utils;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

class Modal
{
    public static function refresh($content = null) {

        return new Response($content, Response::HTTP_CREATED);
    }

    public static function renderModal(Form $form) {

        $code = $form->isSubmitted() && !$form->isValid() ? Response::HTTP_FORBIDDEN : Response::HTTP_OK;
        return new Response(null, $code);
    }
}