<?php

namespace NetBS\CoreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use NetBS\CoreBundle\Entity\DynamicList;

class PostLoadDynamicListListener
{
    public function postLoad(LifecycleEventArgs $args) {

        $dynamicList    = $args->getEntity();
        $em             = $args->getEntityManager();

        if(!$dynamicList instanceof DynamicList)
            return;

        $repository     = $em->getRepository($dynamicList->getItemsClass());
        $items          = [];
        $itemIds        = $dynamicList->_getItemIds();
        foreach($itemIds as $id)
            $items[] = $repository->find($id);

        $dynamicList->_setItems($items);
    }
}