<?php

namespace Ovesco\WhatsappBundle\Listener;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Event\PreRenderLayoutEvent;
use NetBS\SecureBundle\Mapping\BaseUser;
use Ovesco\WhatsappBundle\Util\UserSlug;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AccountLayoutListener
{
    protected $stack;

    protected $manager;

    protected $token;

    public function __construct(RequestStack $stack, EntityManager $manager, TokenStorage $storage)
    {
        $this->stack    = $stack;
        $this->manager  = $manager;
        $this->token    = $storage;
    }

    /**
     * @param PreRenderLayoutEvent $event
     * @throws \Exception
     */
    public function extendLayout(PreRenderLayoutEvent $event) {

        /** @var BaseUser $user */
        $user   = $this->token->getToken()->getUser();
        $route  = $this->stack->getCurrentRequest()->get('_route');

        if($route !== "netbs.secure.user.account_page")
            return;

        $config = $event->getConfigurator();

        $row    = $config->getRow(0);
        $firstCol = $row->getColumn(0);

        $accounts = $this->manager->getRepository('OvescoWhatsappBundle:WhatsappAccount')
            ->findBy(['user' => $user]);

        if (count($accounts) === 0) {
            $firstCol->addRow()->addColumn(0, 12)->setBlock(CardBlock::class, [
                'title'     => 'Connexion whatsapp',
                'template'  => '@OvescoWhatsapp/account/account_code.block.twig',
                'params'    => [
                    'user'  => $user,
                    'slug'  => UserSlug::getSlug($user->getUsername()),
                ]
            ]);
        }
    }
}
