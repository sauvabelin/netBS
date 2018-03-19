<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Entity\ExportConfiguration;
use NetBS\CoreBundle\Exporter\ExportBlob;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use NetBS\CoreBundle\Model\ExporterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExportController
 * @Route("/export")
 */
class ExportController extends Controller
{
    /**
     * @Route("/export/selected", name="netbs.core.export.export_selected")
     * @param Request $request
     * @return Response
     */
    public function generateExportBlobAction(Request $request) {

        $session    = $this->get('session');
        $blob       = new ExportBlob($request);
        $exporter   = $this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias());

        if(!$exporter instanceof ConfigurableExporterInterface) {

            $session->set($blob->getKey(), serialize($blob));
            return $this->generateExportAction($blob->getKey());
        }

        else {

            $configs = $this->getUserConfigurations($exporter);
            $blob->setConfigId($configs[0]->getId());
            $session->set($blob->getKey(), serialize($blob));
        }

        return $this->redirectToRoute('netbs.core.export.check_settings', array('blobKey' => $blob->getKey()));
    }

    /**
     * @Route("/switch-config/{blobKey}/{configId}", name="netbs.core.export.switch_config")
     * @param $blobKey
     * @param $configId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function switchConfigAction($blobKey, $configId) {

        /** @var ExportBlob $blob */
        $blob   = unserialize($this->get('session')->get($blobKey));
        $em     = $this->get('doctrine.orm.entity_manager');
        $config = null;

        if($configId === 'new')
            $config = $this->getNewConfig($this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias()));

        else
            $config = $em->getRepository('NetBSCoreBundle:ExportConfiguration')->findOneBy([
                'user'  => $this->getUser(),
                'id'    => $configId
            ]);

        if(!$config)
            throw $this->createNotFoundException("Unknown exportation configuration");

        $blob->setConfigId($config->getId());
        $this->get('session')->set($blob->getKey(), serialize($blob));
        return $this->redirectToRoute('netbs.core.export.check_settings', ['blobKey' => $blob->getKey()]);
    }

    /**
     * @Route("/remove-config/{blobKey}/{configId}", name="netbs.core.export.remove_config")
     * @param $blobKey
     * @param $configId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeConfigAction($blobKey, $configId) {

        /** @var ExportBlob $blob */
        $blob   = unserialize($this->get('session')->get($blobKey));
        $export = $this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias());
        $em     = $this->get('doctrine.orm.entity_manager');
        $config = $em->getRepository('NetBSCoreBundle:ExportConfiguration')->findOneBy([
            'user'  => $this->getUser(),
            'id'    => $configId
        ]);

        if(!$config)
            throw $this->createAccessDeniedException();

        $em->remove($config);
        $em->flush();

        $configs    = $this->getUserConfigurations($export);
        $config     = $configs[0];

        $blob->setConfigId($config->getId());
        $this->get('session')->set($blob->getKey(), serialize($blob));

        return $this->redirectToRoute('netbs.core.export.check_settings', ['blobKey' => $blob->getKey()]);
    }

    /**
     * @Route("/check-settings/{blobKey}", name="netbs.core.export.check_settings")
     * @param Request $request
     * @param $blobKey
     * @return Response
     */
    public function exportSettingsViewAction(Request $request, $blobKey) {

        /** @var ExportBlob $blob */
        /** @var ConfigurableExporterInterface $exporter */
        $blob               = unserialize($this->get('session')->get($blobKey));
        $em                 = $this->getDoctrine()->getManager();
        $exporter           = $this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias());
        $configs            = $this->getUserConfigurations($exporter);
        $configContainer    = $em->find('NetBSCoreBundle:ExportConfiguration', $blob->getConfigId());

        $form               = $this->createForm($exporter->getConfigFormClass(), $configContainer->getConfiguration());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $config = $form->getData();
            $configContainer->setConfiguration($config);
            $em->persist($configContainer);
            $em->flush();

            $this->addFlash('success', 'Configuration enregistrée');
            return $this->redirectToRoute('netbs.core.export.check_settings', array('blobKey' => $blob->getKey()));
        }

        return $this->render('@NetBSCore/export/check_export.html.twig', [
            'form'      => $form->createView(),
            'configs'   => $configs,
            'blob'      => $blob
        ]);
    }

    /**
     * @param $blobKey
     * @return Response
     * @throws \Exception
     * @Route("/preview/{blobKey}", name="netbs.core.export.preview")
     */
    public function previewExportAction($blobKey) {

        $session    = $this->get('session');
        $blob       = unserialize($session->get($blobKey));
        $items      = $this->getItems($blob);
        $exporter   = $this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias());

        if(!$exporter instanceof ConfigurableExporterInterface)
            throw new \Exception("Cant preview file, exporter doesnt support previewing");

        if(!$exporter->getPreviewer())
            return new Response();

        $this->configureExporter($exporter, $blob);
        $previewer  = $this->get('netbs.core.previewer_manager')->getPreviewer($exporter->getPreviewer());
        return $previewer->preview($items, $exporter);
    }


    /**
     * @param $blobKey
     * @return Response
     * @Route("/generate-export/{blobKey}", name="netbs.core.export.generate")
     */
    public function generateExportAction($blobKey) {

        /** @var ExportBlob $blob */
        $session        = $this->get('session');
        $blob           = unserialize($session->get($blobKey));

        $items          = $this->getItems($blob);
        $exporter       = $this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias());

        $this->configureExporter($exporter, $blob);

        return $exporter->export($items);
    }

    /**
     * @param ExporterInterface $exporter
     * @param ExportBlob $blob
     */
    protected function configureExporter(ExporterInterface $exporter, ExportBlob $blob) {

        $em = $this->get('doctrine.orm.entity_manager');

        if($exporter instanceof ConfigurableExporterInterface) {
            $config    = $em->find('NetBSCoreBundle:ExportConfiguration', $blob->getConfigId());
            $exporter->setConfig($config->getConfiguration());
        }
    }

    /**
     * @param ExportBlob $blob
     * @return array
     */
    protected function getItems(ExportBlob $blob) {

        $listItems      = array_map(function($val) {return intval($val);}, $blob->getIds());
        $em             = $this->get('doctrine.orm.entity_manager');
        $exporter       = $this->get('netbs.core.exporter_manager')->getExporterByAlias($blob->getExporterAlias());

        $query          = $em->createQueryBuilder();
        $elements       = $query->select('x')
            ->from($blob->getItemsClass(), 'x')
            ->where($query->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $listItems)
            ->getQuery()
            ->execute();

        return $this->get('netbs.core.bridge_manager')->convertItems($elements, $exporter->getExportableClass());
    }

    /**
     * @param ConfigurableExporterInterface $exporter
     * @return ExportConfiguration[]
     */
    protected function getUserConfigurations(ConfigurableExporterInterface $exporter) {

        $user   = $this->getUser();
        $em     = $this->get('doctrine.orm.default_entity_manager');
        $repo   = $em->getRepository('NetBSCoreBundle:ExportConfiguration');

        $configs= $repo->findBy(array(
            'user'          => $user,
            'exporterAlias' => $exporter->getAlias()
        ));

        if(count($configs) == 0)
            $configs[] = $this->getNewConfig($exporter);

        return $configs;
    }

    /**
     * @param ConfigurableExporterInterface $exporter
     * @return ExportConfiguration
     */
    protected function getNewConfig(ConfigurableExporterInterface $exporter) {

        $em     = $this->get('doctrine.orm.entity_manager');
        $config = new ExportConfiguration();
        $name   = $exporter->getConfigClass();

        $config->setUser($this->getUser())
            ->setExporterAlias($exporter->getAlias())
            ->setConfiguration(new $name())
            ->setNom("Config. de base");

        $em->persist($config);
        $em->flush();

        return $config;
    }
}