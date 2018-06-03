<?php

namespace SauvabelinBundle\Service;

use GDM\ISPConfig\SoapClient;
use NetBS\CoreBundle\Utils\StrUtil;

class ISPConfigManager
{
    /**
     * @var SoapClient
     */
    private $client;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $clientId;

    /**
     * @var int
     */
    private $serverId;

    /**
     * Paramètre de mailbox
     * @var integer
     */
    private $uid;

    /**
     * Paramètre de mailbox
     * @var integer
     */
    private $gid;

    /**
     * Paramètre de mailbox
     * @var string
     */
    private $baseMailDir;

    /**
     * Paramètre de mailbox
     * @var string
     */
    private $homeDir;

    public function __construct($host, $username, $password, $mailDir, $homeDir, $clientId, $serverId, $uid, $gid)
    {
        $this->host         = $host;
        $this->username     = $username;
        $this->password     = $password;
        $this->clientId     = $clientId;
        $this->serverId     = $serverId;
        $this->baseMailDir  = $mailDir;
        $this->homeDir      = $homeDir;
        $this->uid          = $uid;
        $this->gid          = $gid;
    }

    public function getMailbox($email) {

        $mailbox    = $this->getClient()->mailUserGet(['email' => $email]);

        return count($mailbox) ? $mailbox[0] : null;
    }

    public function createMailbox($id, $name, $username, $password, $email) {

        if($this->getMailbox($email))
            return false;

        $maildir    = intval($id) . "." . StrUtil::slugify($username);

        return $this->getClient()->mailUserAdd($this->clientId, [
            'server_id'     => $this->serverId,
            'email'         => $email,
            'login'         => $email,
            'password'      => $password,
            'name'          => $name,
            'quota'         => 524288000,
            'autoresponder' => 'n',
            'move_junk'     => 'y',
            'postfix'       => 'y',
            'access'        => 'n',
            'uid'           => $this->uid,
            'gid'           => $this->gid,
            'maildir'       => $this->baseMailDir . $maildir,
            'homedir'       => $this->homeDir
        ]);
    }

    public function getMailingList($from) {

        $mailingListes = $this->getClient()->mailForwardGet(['source' => $from]);

        return count($mailingListes) ? $mailingListes[0] : null;
    }

    public function createMailingList($from, array $to) {

        if($this->getMailingList($from)) return false;

        return false !== $this->getClient()->mailForwardAdd($this->clientId, [
            'source'        => $from,
            'destination'   => $this->parseDestination($to),
            'server_id'     => $this->serverId,
            'type'          => 'forward',
            'active'        => 'y'
        ]);
    }

    public function updateMailingList($from, array $to) {

        $remote = $this->getMailingList($from);

        if(!$remote)
            return $this->createMailingList($from, $to); //Create list as doesnt exist

        $remote['destination'] = $this->parseDestination($to);

        return false !== $this->getClient()->mailForwardUpdate($this->clientId, $remote['forwarding_id'], $remote);
    }

    public function deleteMailingList($from) {

        $remote    = $this->getMailingList($from);

        if(!$remote)
            return false;

        return false !== $this->getClient()->mailForwardDelete($remote['forwarding_id']);
    }

    private function parseDestination($to) {

        return implode("\n", $to);
    }

    private function getClient() {

        if(!$this->client)
            $this->client = new SoapClient($this->host, $this->username, $this->password, stream_context_create([
                'http'  => [ 'method' => 'GET' ],
                'ssl'   => [ 'verify_peer' => false, 'allow_self_signed'=> true ]
            ]));

        return $this->client;
    }
}