<?php
namespace Bravo3\OrmBundle\Services;

use Bravo3\OrmBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class OrmSecurityManager
{
    /**
     * @var OrmUserProvider
     */
    protected $user_provider;

    /**
     * @var EncoderFactory
     */
    protected $encoder_factory;

    /**
     * @var TokenStorageInterface
     */
    protected $token_storage;

    /**
     * @var EventDispatcher
     */
    protected $event_dispatcher;

    /**
     * @var string
     */
    protected $provider_key;

    public function __construct(
        OrmUserProvider $user_provider,
        EncoderFactory $encoder_factory,
        TokenStorageInterface $token_storage,
        $event_dispatcher,
        $provider_key
    ) {
        $this->user_provider    = $user_provider;
        $this->encoder_factory  = $encoder_factory;
        $this->token_storage    = $token_storage;
        $this->event_dispatcher = $event_dispatcher;
        $this->provider_key     = $provider_key;
    }

    /**
     * Get the security token
     *
     * @return TokenInterface
     */
    public function getToken()
    {
        return $this->token_storage->getToken();
    }

    /**
     * Get the user entity, if applicable
     *
     * @return User|null
     */
    public function getUser()
    {
        $token = $this->token_storage->getToken();
        if (!$token || !$token->getUsername()) {
            return null;
        }

        return $this->getUserByUsername($token->getUsername());
    }

    /**
     * Check if we have an authenticated, non-anonymous token
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        $token = $this->getToken();
        if (!$token || ($token instanceof AnonymousToken)) {
            return false;
        }

        return $token->isAuthenticated();
    }

    /**
     * Get a user by username
     *
     * @param $username
     * @return User
     */
    public function getUserByUsername($username)
    {
        return $this->user_provider->loadUserByUsername($username);
    }

    /**
     * Test a users password
     *
     * @param User   $user
     * @param string $password
     * @return bool
     */
    public function testLogin(User $user, $password)
    {
        $encoder = $this->encoder_factory->getEncoder($user);
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }

    /**
     * Log the user in
     *
     * An InteractiveLoginEvent will be fired if you include the request
     *
     * @param User    $user     User entity
     * @param string  $password Users password
     * @param Request $request  Optionally provide the Request object to dispatch an InteractiveLoginEvent
     */
    public function doLogin(User $user, $password, Request $request = null)
    {
        $token = new UsernamePasswordToken($user->getUsername(), $password, $this->provider_key, $user->getRoles());
        $this->token_storage->setToken($token);

        // Fire the login event
        if ($request) {
            $event = new InteractiveLoginEvent($request, $token);
            $this->event_dispatcher->dispatch("security.interactive_login", $event);
        }
    }

    /**
     * Remove current security token
     */
    public function doLogout()
    {
        $this->token_storage->setToken(null);
    }
}
