<?php
namespace Bravo3\OrmBundle\Entity;

use Bravo3\Orm\Annotations\Column;
use Bravo3\Orm\Annotations\Entity;
use Bravo3\Orm\Annotations\Id;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @Entity()
 */
class User implements AdvancedUserInterface
{
    /**
     * @var string
     * @Id()
     * @Column(type="string")
     */
    protected $username;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $salt;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $password;

    /**
     * @var array
     * @Column(type="set")
     */
    protected $roles = [];

    /**
     * @var bool
     * @Column(type="bool")
     */
    protected $account_locked = false;

    /**
     * @var bool
     * @Column(type="bool")
     */
    protected $account_expired = false;

    /**
     * @var bool
     * @Column(type="bool")
     */
    protected $credentials_expired = false;

    /**
     * @var bool
     * @Column(type="bool")
     */
    protected $active = true;

    /**
     * Set username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set salt
     *
     * Do NOT call this function directly, instead use the OrmUserProvider to update user credentials.
     *
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Set the encoded password
     *
     * Do NOT use this function to set a plain text password. Instead use the OrmUserProvider to update a users
     * password.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set user roles
     *
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role
     *
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        // NB: MUST call getter & setter
        $roles   = $this->getRoles();
        $roles[] = $role;
        $this->setRoles($roles);
        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get AccountLocked
     *
     * @return boolean
     */
    public function getAccountLocked()
    {
        return $this->account_locked;
    }

    /**
     * Set AccountLocked
     *
     * @param boolean $account_locked
     * @return $this
     */
    public function setAccountLocked($account_locked)
    {
        $this->account_locked = (bool)$account_locked;
        return $this;
    }

    /**
     * Get AccountExpired
     *
     * @return boolean
     */
    public function getAccountExpired()
    {
        return $this->account_expired;
    }

    /**
     * Set AccountExpired
     *
     * @param boolean $account_expired
     * @return $this
     */
    public function setAccountExpired($account_expired)
    {
        $this->account_expired = (bool)$account_expired;
        return $this;
    }

    /**
     * Get CredentialsExpired
     *
     * @return boolean
     */
    public function getCredentialsExpired()
    {
        return $this->credentials_expired;
    }

    /**
     * Set CredentialsExpired
     *
     * @param boolean $credentials_expired
     * @return $this
     */
    public function setCredentialsExpired($credentials_expired)
    {
        $this->credentials_expired = (bool)$credentials_expired;
        return $this;
    }

    /**
     * Get Active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set Active
     *
     * @param boolean $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Return the username when requested as a string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->username;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(
            [
                $this->username,
                $this->password,
                $this->salt,
                $this->account_expired,
                $this->credentials_expired,
                $this->account_locked,
                $this->active
            ]
        );
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list($this->username,
            $this->password,
            $this->salt,
            $this->account_expired,
            $this->credentials_expired,
            $this->account_locked,
            $this->active) = $data;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return !$this->account_expired;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return !$this->account_locked;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return !$this->credentials_expired;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->active;
    }
}
