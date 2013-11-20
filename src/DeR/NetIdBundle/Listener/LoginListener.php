<?php

namespace DeR\NetIdBundle\Listener;
 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use DeR\NetIdBundle\Entity\LoginLog;
 
/**
 * Custom login listener.
 */
class LoginListener
{
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(Doctrine $doctrine)
	{
		$this->em = $doctrine->getEntityManager();
	}
	
	/**
	 * Log login action.
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		$identity = $event->getAuthenticationToken()->getUser();
		$server = $event->getRequest()->server;
		$loginLog = new LoginLog();
		$loginLog->setIdentity($identity);
		$loginLog->setRoles($identity->getRoles());
		$loginLog->setUserAgent($server->get('HTTP_USER_AGENT'));
		$loginLog->setIp($server->get('REMOTE_ADDR'));
		$this->em->persist($loginLog);
		$this->em->flush();
	}
}