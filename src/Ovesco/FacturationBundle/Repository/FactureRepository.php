<?php

namespace Ovesco\FacturationBundle\Repository;


use Doctrine\ORM\EntityRepository;

class FactureRepository extends EntityRepository
{
    public function findByFactureId($id) {

        $oldFichierFacture  = $this->findOneBy(array('oldFichierId' => $id));
        return $oldFichierFacture ? $oldFichierFacture : $this->find($id);
    }
}