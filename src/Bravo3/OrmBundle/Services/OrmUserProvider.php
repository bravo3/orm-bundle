<?php
namespace Bravo3\OrmBundle\Services;

use Bravo3\Orm\Exceptions\NotFoundException;
use Bravo3\Orm\Services\EntityManager;
use Bravo3\OrmBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OrmUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entity_manager;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoder_factory;

    /**
     * @var string
     */
    protected $user_class;

    /**
     * @var string[]
     */
    protected $available_roles;

    public function __construct(
        EntityManager $entity_manager,
        EncoderFactoryInterface $encoder_factory,
        $user_class,
        array $available_roles
    ) {
        $this->entity_manager  = $entity_manager;
        $this->encoder_factory = $encoder_factory;
        $this->user_class      = $user_class;
        $this->available_roles = $available_roles;
    }

    /**
     * Loads the user for the given username.
     *
     * @param string $username The username
     * @return UserInterface
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        try {
            return $this->entity_manager->retrieve($this->user_class, $username);
        } catch (NotFoundException $e) {
            throw new UsernameNotFoundException("User '".$username."' not found");
        }
    }

    /**
     * Refreshes the user for the account interface.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!($user instanceof User)) {
            throw new UnsupportedUserException("User class not supported");
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return ($class instanceof User);
    }

    /**
     * Update the users password and/or salt
     *
     * @param User   $user
     * @param string $password New password
     * @param string $salt     If Ommitted, the salt will not be updated
     * @param bool   $flush    Flush the entity manager on completion
     */
    public function updateUserCredentials(User $user, $password, $salt = null, $flush = true)
    {
        if (!$salt) {
            $salt = $user->getSalt() ?: $this->generateSalt();
        }

        $encoder  = $this->encoder_factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $salt);

        $user->setPassword($password);
        $user->setSalt($salt);

        $this->entity_manager->persist($user);
        if ($flush) {
            $this->entity_manager->flush();
        }
    }

    /**
     * Generate a salt
     *
     * @return string
     */
    public function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * Create a new user and persist it to the database
     *
     * @param string   $username
     * @param string   $password
     * @param string[] $roles
     * @return User
     */
    public function createUser($username, $password, array $roles = ['ROLE_USER'])
    {
        $class = $this->user_class;
        /** @var User $user */
        $user = new $class();
        $user->setUsername($username);
        $user->setRoles($roles);
        $this->updateUserCredentials($user, $password);
        return $user;
    }

    /**
     * Get the default list of available user roles
     *
     * @return string[]
     */
    public function getAvailableRoles()
    {
        return $this->available_roles;
    }
}
