<?php

namespace Ovesco\WhatsappBundle\Service;

use Ovesco\WhatsappBundle\Model\ScenarioInterface;

class ScenarioManager
{
    private $scenarios = [];

    public function registerScenario(ScenarioInterface $scenario) {
        $this->scenarios[] = $scenario;
    }

    /**
     * @return ScenarioInterface[]
     */
    public function getScenarios() {
        $scenarios = $this->scenarios;
        usort($scenarios, function(ScenarioInterface $s1, ScenarioInterface $s2) {
            return $s1::getPoids() > $s2::getPoids() ? 1 : -1;
        });
        return $scenarios;
    }
}
