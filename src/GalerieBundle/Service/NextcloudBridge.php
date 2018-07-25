<?php

namespace GalerieBundle\Service;

use NetBS\CoreBundle\Service\ParameterManager;
use Sabre\DAV\Client;

class NextcloudBridge
{
    private $client;

    private $params;

    public function __construct(Client $client, ParameterManager $parameterManager)
    {
        $this->client   = $client;
        $this->params   = $parameterManager;
    }

    public function getInformation($path) {

        $data       = $this->client->request('PROPFIND', $path);
        $document   = new \DOMDocument();
        $document->loadXML($data['body']);

        /** @var \DOMElement $responseElement */
        foreach($document->getElementsByTagName("response") as $responseElement) {

            $href       = $responseElement->getElementsByTagName("href")->item(0)->textContent;
            $itemPath   = substr($href, strpos($href, $this->params->getValue('galerie', 'root_directory')));
            $itemPath   = $this->decodePath($itemPath);
            $mimeNode   = $responseElement->getElementsByTagName("getcontenttype")->item(0);
            $type       = $responseElement->getElementsByTagName('resourcetype');

            if($path === $itemPath)
                continue;

            if($type->length > 0 && $type->item(0)->firstChild && $type->item(0)->firstChild->tagName === "d:collection")
                $this->fullMapDirectory($itemPath, $io);

            else {

                $itemPath   = trim($itemPath, "/");
                $name       = explode("/", $itemPath);
                $name       = $name[count($name) - 1];
                $io->writeln("Mapping media : " . $itemPath);

                $ncnode = new NCNode([
                    'etag'      => str_replace('"', "", $responseElement->getElementsByTagName('getetag')->item(0)->textContent),
                    'name'      => $name,
                    'path'      => "files/" . $itemPath,
                    'size'      => $responseElement->getElementsByTagName('getcontentlength')->item(0)->textContent,
                    'mimetype'  => $mimeNode->textContent
                ]);

                $this->map($ncnode);
            }
        }

        return $data;
    }

    private function decodePath($path) {

        $realPath   = "";
        foreach(explode("/", $path) as $segment)
            $realPath .= rawurldecode($segment) . "/";

        return trim($realPath, "/") . "/";
    }
}