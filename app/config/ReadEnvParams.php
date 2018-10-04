<?php

$vcapServices = getenv('VCAP_SERVICES');
$vcapServices = $vcapServices ? json_decode($vcapServices) : null;

$container->setParameter('database_driver', 'pdo_mysql');

$container->setParameter('database_host', getenv('DB_HOST'));
$container->setParameter('database_port', getenv('DB_PORT'));
$container->setParameter('database_name', getenv('DB_NAME'));
$container->setParameter('database_user', getenv('DB_USER'));
$container->setParameter('database_password', getenv('DB_PASSWORD'));
$container->setParameter('mailer_transport', getenv('MAILER_TRANSPORT'));
$container->setParameter('mailer_host', getenv('MAILER_HOST'));
$container->setParameter('mailer_user', getenv('MAILER_USER'));
$container->setParameter('mailer_password', getenv('MAILER_PASSWORD'));
$container->setParameter('web_path', getenv('WEB_PATH'));
$container->setParameter('secret', getenv('SECRET'));
$container->setParameter('netbs_secure_user_class', getenv('NETBS_SECURE_USER_CLASS'));
$container->setParameter('ispconfig_host', getenv('ISPCONFIG_HOST'));
$container->setParameter('ispconfig_user', getenv('ISPCONFIG_USER'));
$container->setParameter('ispconfig_password', getenv('ISPCONFIG_PASSWORD'));
$container->setParameter('ispconfig_server_id', getenv('ISPCONFIG_SERVER_ID'));
$container->setParameter('ispconfig_client_id', getenv('ISPCONFIG_CLIENT_ID'));
$container->setParameter('ispconfig_maildir', getenv('ISPCONFIG_MAILDIR'));
$container->setParameter('ispconfig_homedir', getenv('ISPCONFIG_HOMEDIR'));
$container->setParameter('ispconfig_uid', getenv('ISPCONFIG_UID'));
$container->setParameter('ispconfig_gid', getenv('ISPCONFIG_GID'));
$container->setParameter('nextcloud_webdav_uri', getenv('NEXTCLOUD_WEBDAV_URI'));
$container->setParameter('nextcloud_ocs_uri', getenv('NEXTCLOUD_OCS_URI'));
$container->setParameter('nextcloud_username', getenv('NEXTCLOUD_USER'));
$container->setParameter('nextcloud_password', getenv('NEXTCLOUD_PASSWORD'));
$container->setParameter('jwt_private_key_path', getenv('JWT_PRIVATE_KEY_PATH'));
$container->setParameter('jwt_public_key_path', getenv('JWT_PUBLIC_KEY_PATH'));
$container->setParameter('jwt_key_pass_phrase', getenv('JWT_KEY_PASS_PHRASE'));
$container->setParameter('jwt_token_ttl', getenv('JWT_TOKEN_TTL'));

if($vcapServices) {

    $db = $vcapServices->{getenv('FOUNDRY_DB_NAME')}[0]->credentials;

    $container->setParameter('database_host', $db->host);
    $container->setParameter('database_port', $db->port);
    $container->setParameter('database_name', $db->name);
    $container->setParameter('database_user', $db->username);
    $container->setParameter('database_password', $db->password);
}