<?php

namespace TenteBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\DrawingPart;
use TenteBundle\Entity\Tente;
use TenteBundle\Entity\TenteModel;
use TenteBundle\Form\AddTentesType;
use TenteBundle\Form\TenteModelNameType;
use TenteBundle\Form\TenteModelType;

/**
 * Class TenteModelController
 * @package TenteBundle\Controller
 * @Route("/tente-model")
 */
class TenteModelController extends Controller
{
    /**
     * @Route("/view/{id}", name="tente.tente_model.view")
     */
    public function viewAction(TenteModel $model) {

        return $this->render('@Tente/tente_model/view_tente_model.html.twig', [
            'model' => $model,
            'form' => $this->createForm(TenteModelNameType::class, $model)->createView()
        ]);
    }

    /**
     * @Route("/add-tentes/{id}", name="tente.tente_model.add_tentes")
     * @param TenteModel $model
     * @param Request $request
     */
    public function addTentesModalAction(TenteModel $model, Request $request) {

        $form = $this->createForm(AddTentesType::class, ['tentes' => '']);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $tentes = explode(' ', $form->getData()['tentes']);
            foreach($tentes as $name) {
                $tente = new Tente();
                $tente->setModel($model);
                $tente->setNumero($name);
                $em->persist($tente);
            }

            $em->flush();
            $this->addFlash('success', count($tentes) . " tentes ajoutées!");
            return Modal::refresh();
        }

        return $this->render('@Tente/tente_model/add_tentes.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

    /**
     * @Route("/add", name="tente.tente_model.add")
     */
    public function addAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $form = $this->createForm(TenteModelType::class, new TenteModel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var TenteModel $model */
            $model = $form->getData();
            $dirtyParts = $model->getDrawingParts();
            $parts = [];
            foreach($dirtyParts as $drawingPart) {
                /** @var UploadedFile $file */
                $file = $drawingPart->getImage();

                if ($file === null) continue;

                $movePath = __DIR__ . '/../../../web/tentes/uploads';
                $filename = uniqid() . '_drawing_part.' . $file->getClientOriginalExtension();

                try {
                    $file->move($movePath, $filename);
                    $cleanPart = new DrawingPart();
                    $cleanPart->setTenteModel($model);
                    $cleanPart->setImage($filename);
                    $cleanPart->setNom($file->getClientOriginalName());
                    $em->persist($cleanPart);
                    $parts[] = $cleanPart;
                } catch (\Exception $e) {
                    dump($e);
                    continue;
                }
            }

            $model->removeDrawingParts();
            foreach($parts as $p) $model->addDrawingPart($p);

            $this->addFlash('success', 'Modèle de tente enregistré');
            $em->persist($model);
            $em->flush();
            return $this->redirectToRoute('tente.dashboard');
        }

        return $this->render('@Tente/tente_model/add_tente_model.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
