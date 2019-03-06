<?php

namespace Ovesco\FacturationBundle\Controller;

use Genkgo\Camt\Config;
use Genkgo\Camt\DTO\EntryTransactionDetail;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Entity\Paiement;
use Ovesco\FacturationBundle\Model\ParsedBVR;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CamtController
 * @package Ovesco\FacturationBundle\Controller
 * @Route("/camt")
 */
class CamtController extends Controller
{
    /**
     * @param Request $request
     * @Route("/import", name="ovesco.facturation.camt.import")
     */
    public function importAction(Request $request) {

        $parsedBVR = null;
        $em = $this->get('doctrine.orm.entity_manager');
        $form = $this->createFormBuilder([])->add('file', FileType::class, ['label' => 'Fichier BVR'])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $parsedBVR = $this->parseBVRFile($data['file']);
                $em->flush();
                return $this->render('@OvescoFacturation/camt/result.html.twig', [
                    'result' => $parsedBVR,
                ]);
            } catch (\Exception $e) {
                // throw $e;
                $this->addFlash('error', "Fichier illisible: " . $e->getMessage());
                return $this->redirectToRoute('ovesco.facturation.camt.import');
            }
        }
        return $this->render('@OvescoFacturation/camt/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param UploadedFile $file
     * @return ParsedBVR
     * @throws \Exception
     */
    private function parseBVRFile(UploadedFile $file) {
        $em = $this->get('doctrine.orm.entity_manager');
        $parsedBVR = new ParsedBVR();
        $reader = new \Genkgo\Camt\Reader(Config::getDefault());
        $data = $reader->readFile($file);
        $statements = $data->getRecords();
        $factureRepo = $this->get('doctrine.orm.entity_manager')->getRepository('OvescoFacturationBundle:Facture');

        foreach($statements as $statement) {
            foreach($statement->getEntries() as $entry) {

                $query = $em->getRepository('OvescoFacturationBundle:Compte')->createQueryBuilder('c');
                $compte = $query->where("REPLACE(c.ccp, '-', '') = :ccp")->setParameter('ccp', $entry->getReference())->getQuery()->getResult();
                if (count($compte) !== 1) throw new \Exception("Aucun compte enregistré pour le CCP " . $entry->getReference());
                foreach ($entry->getTransactionDetails() as $transactionDetail) {

                    /** @var Facture $facture */
                    $facture = $factureRepo->findByFactureId($this->getFactureId($transactionDetail));
                    $paiement = $this->transactionToPaiement($transactionDetail);
                    $paiement->setCompte($compte[0]);

                    if ($facture) {
                        $paid = false;
                        foreach ($facture->getPaiements() as $p) {
                            if ($p->getMontant() === $paiement->getMontant() && $p->getDateEffectivePaiement()->getTimestamp() === $paiement->getDateEffectivePaiement()->getTimestamp()) {
                                $paid = true;
                                break;
                            }
                        }

                        if (!$paid) {
                            $em->persist($paiement);
                            $facture->addPaiement($paiement);
                            $parsedBVR->addFacture($facture);
                        }
                        else
                            $parsedBVR->addAlreadyPaid($facture);
                    }
                    else $parsedBVR->addOrphanPaiement($paiement);
                }
            }
        }

        return $parsedBVR;
    }

    private function getFactureId(EntryTransactionDetail $entryTransactionDetail) {

        $refNumber  = $entryTransactionDetail->getRemittanceInformation()->getCreditorReferenceInformation()->getRef(); //Get reference number
        $refNumber  = ltrim($refNumber, 0); //Enlever tous les 0 de remplissage
        $refNumber  = substr($refNumber, 0, -1); //Enlever la somme de contrôle
        return intval($refNumber);
    }

    protected function transactionToPaiement(EntryTransactionDetail $transactionDetail) {

        $paiement   = new Paiement();
        $paiement
            ->setMontant($transactionDetail->getAmount()->getAmount()->getAmount() / 100)
            ->setDate($transactionDetail->getRelatedDates()->getAcceptanceDateTime())
            ->setTransactionDetails($transactionDetail);

        return $paiement;
    }
}
