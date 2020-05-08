<?php

namespace Ovesco\GalerieBundle\ApiController;

use Ovesco\GalerieBundle\Entity\DirectoryView;
use Ovesco\GalerieBundle\Model\Directory;
use Ovesco\GalerieBundle\Model\GalerieConfig;
use Ovesco\GalerieBundle\Model\Markdown;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GalerieAPIController
 * @package GalerieBundle\Controller
 */
class ApiGalerieController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/public/netBS/galerie/root-pictures", name="ovesco.galerie.public_api.root-pictures")
     */
    public function publicPicturesAction() {

        $config         = $this->get('ovesco.galerie.config');
        $realPath       = $config->getFullMappedDirectory() . '/';
        $directory      = new Directory($realPath, $config);
        $images         = array_map(function(Directory $directory) {
            return $directory->getThumbnail();
            }, $directory->getChildren());

        return new JsonResponse($this->get('serializer')->serialize($images, 'json'), 200, [], true);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/public/netBS/galerie/latest-change", name="ovesco.galerie.public_api.latest-change")
     */
    public function latestChangeAction() {

        $config = $this->get('ovesco.galerie.config');
        $host = $this->getParameter('database_host');
        $user = $this->getParameter('database_user');
        $pass = $this->getParameter('database_password');

        $pdo = new \PDO("mysql:dbname=stammbox;host=$host", $user, $pass);
        $query = $pdo->prepare("SELECT path, name, mtime FROM `oc_filecache` where storage = 313 and mimetype = 2 and parent != -1 order by mtime desc limit 70");
        $query->execute();
        $result = $query->fetchAll();
        $descriptionQuery = $pdo->prepare("SELECT path, mtime FROM `oc_filecache` where name = 'description.md' order by mtime desc");
        $descriptionQuery->execute();
        $descriptions = $descriptionQuery->fetchAll();

        $aggr = [];
        $res = [];

        foreach($result as $item) {
            if (!isset($aggr[$item["mtime"]]))
                $aggr[$item["mtime"]] = [];

            $aggr[$item["mtime"]][] = $item;
        }

        foreach($descriptions as $description)
            if(isset($aggr[$description['mtime']]))
                unset($aggr[$description['mtime']]);

        foreach($aggr as $items) {
            usort($items, function ($l1, $l2) {
                return strlen($l1['path']) > strlen($l2['path']) ? -1 : 1;
            });

            $res[] = [
                'hash' => (new Directory(utf8_encode($items[0]['path']), $config))->getHashPath(),
                'path' => utf8_encode($items[0]['path']),
                'name' => utf8_encode($items[0]['name']),
                'date' => date("d.m.Y", $items[0]['mtime'])
            ];
        }

        return new JsonResponse($res);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/public/netBS/galerie/directory", name="ovesco.galerie.public_api.directory")
     */
    public function publicAccessAction(Request $request) {

        $token          = $request->headers->get('x-authorization');
        $token          = str_replace("Bearer ", "", $token);
        $actualToken    = $this->get('netbs.params')->getValue('galerie', 'parent_token', false);
        if(!in_array($token, explode('|', $actualToken)))
            return new JsonResponse("access denied", 401);

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/netBS/galerie/directory", name="ovesco.galerie.api.directory")
     */
    public function getDirectoryAction(Request $request) {

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function generateDirectoryResponse(Request $request) {

        $config         = $this->get('ovesco.galerie.config');
        $realPath       = $this->getRealPath($config, $request->get('path'));

        if(!is_dir($realPath))
            throw $this->createNotFoundException("Directory with path $realPath not found");

        $directory      = new Directory($realPath, $config);
        $parser         = new Markdown($directory->getRelativePath());

        $data = [
            'name'          => $directory->getName(),
            'path'          => $directory->getRelativePath(),
            'hashPath'      => $directory->getHashPath(),
            'description'   => $parser->text($directory->getDescription()),
            'thumbnail'     => $directory->getThumbnail(),
            'children'      => $directory->getChildren(),
            'medias'        => $directory->getMedias()
        ];

        // $this->logDirectoryView($directory);
        return new JsonResponse($this->get('serializer')->serialize($data, 'json'), 200, [], true);
    }

    private function getRealPath(GalerieConfig $config, $hashPath) {
        $parts = explode('/', $hashPath);

        $currentRealPath = $config->getFullMappedDirectory();
        $currentPath = $config->getFullMappedDirectory();
        foreach($parts as $pathPart) {
            $directories = scandir($currentRealPath);
            foreach($directories as $possibleDir) {
                $possibleHash = $currentPath . "/" . Directory::hash2($possibleDir);
                if ($possibleHash === $currentPath . "/" . $pathPart) {
                    $currentPath .= "/" . $pathPart;
                    $currentRealPath .= "/" . $possibleDir;
                }
            }
        }

        return $currentRealPath;
    }

    /**
     * @param Directory $directory
     */
    private function logDirectoryView(Directory $directory) {
        $view = new DirectoryView();
        $detect = new \Mobile_Detect();
        $type = $detect->isMobile() ? DirectoryView::MOBILE
                : $detect->isTablet()
                    ? DirectoryView::TABLETTE
                    : DirectoryView::ORDINATEUR;
        $view->setDevice($type);
        $view->setPath($directory->getPath());
        $em = $this->getDoctrine()->getManager();
        $em->persist($view);
        $em->flush();
    }
}

