<?php
/**
 * @package        OpenCart
 * @author         Daniel Kerr
 * @copyright      Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 * @link           https://www.opencart.com
 */

/**
 * Session class
 */
class Session {
    protected object $adaptor;
    protected string $session_id = '';
    public array     $data = [];

    /**
     * Constructor
     *
     * @param    string    $adaptor
     * @param    object    $registry
     */
    public function __construct(string $adaptor, $registry = '') {
        $class = 'Session\\' . $adaptor;

        if (class_exists($class)) {
            if ($registry) {
                $this->adaptor = new $class($registry);
            } else {
                $this->adaptor = new $class();
            }

            register_shutdown_function([$this, 'close']);
        } else {
            trigger_error('Error: Could not load cache adaptor ' . $adaptor . ' session!');
            exit();
        }
    }

    /**
     * @return    string
     */
    public function getId(): string {
        return $this->session_id;
    }

    /**
     * @param    string    $session_id
     *
     * @return    string
     */
    public function start(string $session_id = ''): array|string {
        if (!$session_id) {
            if (function_exists('random_bytes')) {
                $session_id = substr(bin2hex(random_bytes(26)), 0, 26);
            } else {
                $session_id = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
            }
        }

        if (preg_match('/^[a-zA-Z0-9,\-]{22,52}$/', $session_id)) {
            $this->session_id = $session_id;
        } else {
            exit('Error: Invalid session ID!');
        }

        $this->data = $this->adaptor->read($session_id);

        return $session_id;
    }

    /**
     *
     */
    public function close(): void {
        $this->adaptor->write($this->session_id, $this->data);
    }

    /**
     *
     */
    public function destroy(): void {
        $this->adaptor->destroy($this->session_id);
    }
}