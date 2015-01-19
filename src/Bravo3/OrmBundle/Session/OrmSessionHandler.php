<?php
namespace Bravo3\OrmBundle\Session;

use Bravo3\Orm\Exceptions\NotFoundException;
use Bravo3\Orm\Services\EntityManager;
use Bravo3\OrmBundle\Entity\SessionInterface;

class OrmSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var EntityManager
     */
    protected $entity_manager;

    /**
     * @var string
     */
    protected $session_class;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * Create a session handler
     *
     * @param EntityManager $entity_manager Entity manager service
     * @param string        $session_class  Must implements Bravo3\OrmBundle\Entity\SessionInterface
     * @param int           $ttl            Time in seconds to store sessions
     */
    public function __construct(EntityManager $entity_manager, $session_class, $ttl)
    {
        $this->entity_manager = $entity_manager;
        $this->session_class  = $session_class;
        $this->ttl            = $ttl;
    }

    /**
     * Re-initializes existing session, or creates a new one.
     *
     * Does nothing.
     *
     * @param string $savePath    Save path
     * @param string $sessionName Session name, see http://php.net/function.session-name.php
     *
     * @return bool Always true
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Closes the current session.
     *
     * Does nothing.
     *
     * @return bool Always true
     */
    public function close()
    {
        return true;
    }

    /**
     * Reads the session data.
     *
     * @param string $sessionId Session ID, see http://php.net/function.session-id
     * @return string Same session data as passed in write() or empty string when non-existent or on failure
     */
    public function read($sessionId)
    {
        try {
            /** @var SessionInterface $entity */
            $entity = $this->entity_manager->retrieve($this->session_class, $sessionId);
            return $entity->getData();
        } catch (NotFoundException $e) {
            return '';
        }
    }

    /**
     * Writes the session data to the storage.
     *
     * @param string $sessionId Session ID , see http://php.net/function.session-id
     * @param string $data      Serialized session data to save
     * @return bool true on success, false on failure
     */
    public function write($sessionId, $data)
    {
        /** @var SessionInterface $session */
        $session = new $this->session_class();
        $session->setId($sessionId);
        $session->setData($data);
        $this->entity_manager->persist($session, $this->ttl)->flush();
    }

    /**
     * Destroys a session.
     *
     * If the session does not exist this function will return true anyway.
     *
     * @param string $sessionId Session ID, see http://php.net/function.session-id
     * @return bool true on success, false on failure
     */
    public function destroy($sessionId)
    {
        /** @var SessionInterface $session */
        $session = new $this->session_class();
        $session->setId($sessionId);
        $this->entity_manager->delete($session)->flush();
    }

    /**
     * Cleans up expired sessions (garbage collection).
     *
     * NB: This session handler will persist sessions with a native TTL, thus calling the gc() function will be
     *     ignored and no action taken.
     *
     * @param string|int $maxlifetime
     * @return bool Always true
     */
    public function gc($maxlifetime)
    {
        return true;
    }
}
