<?php
namespace Bravo3\OrmBundle\Entity;

interface SessionInterface
{
    /**
     * Get Id
     *
     * @return string
     */
    public function getId();

    /**
     * Set Id
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Get Data
     *
     * @return string
     */
    public function getData();

    /**
     * Set Data
     *
     * @param string $data
     */
    public function setData($data);
}
