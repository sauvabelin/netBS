<?php

namespace Sysmoh\DBTaskBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sysmoh\DBTaskBundle\Entity\Task;
use Sysmoh\DBTaskBundle\Model\TaskInterface;

class TaskManager
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var TaskInterface[]
     */
    protected $tasks = [];

    public function __construct(EntityManager $manager)
    {
        $this->manager  = $manager;
    }

    public function registerTask(TaskInterface $task) {

        $this->tasks[$task->getName()] = $task;
    }

    /**
     * Register given task for execution
     * @param string $name
     * @param array $params
     */
    public function schedule($name, array $params) {

        $task   = $this->tasks[$name];
        $params = $this->resolve($task, $params);

        $dbTask = new Task();
        $dbTask->setName($task->getName());
        $dbTask->setParams($params);

        $this->manager->persist($dbTask);
        $this->manager->flush();
    }

    /**
     * @param $name
     * @return TaskInterface
     * @throws \Exception
     */
    public function getTask($name) {

        if(!isset($this->tasks[$name]))
            throw new \Exception("No tasks registered with name $name");

        return $this->tasks[$name];
    }

    /**
     * @param string $name
     * @param int $amount
     * @param string $status
     * @return Task[]
     */
    public function getDBTasks($name = null, $amount = 10, $status = null) {

        $status = $status ? $status : Task::WAITING;
        $query  = $this->manager->createQueryBuilder()
            ->select('t')
            ->from('SysmohDBTaskBundle:Task', 't');

        if($name)
            $query->where('t.name = :c')->setParameter('c', $name);

        return $query->where('t.status = :s')
            ->setParameter('s', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($amount)
            ->getQuery()
            ->execute();
    }

    private function resolve(TaskInterface $task, array $params) {

        $resolver   = new OptionsResolver();
        $task->configureOptions($resolver);
        return $resolver->resolve($params);
    }
}