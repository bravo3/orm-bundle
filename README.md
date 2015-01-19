Bravo3 ORM Bundle for Symfony 2
===============================
This bundle will add the following support to a Symfony 2 application:

* ORM entity manager service
* A user provider using the entity manager
* Services to help with user authentication (login, logout, etc)
* A session handler to use the entity manager to store sessions

Configuration
-------------
First register the bundle in your AppKernel.php:

    new Bravo3\OrmBundle\OrmBundle(),
    
And then add some config to your `config.yml` file:

    # Bravo3 ORM Configuration
    orm:
        params:
            host: %db_host%
            port: %db_port%
            database: %db_database%
        user_class: AppBundle\Entity\User
        auth_firewall: main
        user_roles: [ ROLE_USER, ROLE_ADMIN, ROLE_SUPERADMIN ]

Also add some default parameters to your `parameters.yml.dist` file:

    parameters:
        db_host: localhost
        db_port: 6379
        db_database: 0
    
You're now set to use the entity manager via the service 'orm.em', but you'll need to configure a bit more for sessions
and user management.

### Sessions
To enable the ORM session handler, just set the server in your `config.yml`:

    framework:
        session:
            handler_id: orm.session_handler
            gc_probability: 0

Because the entity manager expires sessions via a TTL on the entity, you do not need to run the garbage collector to
clean up old sessions.

By default, sessions expire after 3600 seconds (1 hr) - to change the session TTL, add session parameters to the orm
configuration:

    orm:
        session:
            ttl: 60 # 1 minute

You can also change the entity class used by the entity manager, if you have reason to do so:

    orm:
        session:
            ttl: 3600
            entity: SomeNamespace\Session

Your entity class must implement the Bravo3\OrmBundle\Entity\SessionInterface interface.

### User Authentication
For user authentication to work, you need to set the orm.user_provider in the secuity.yml file and set your encoder.
Below is an example security.yml file:

    security:
        encoders:
            Bravo3\OrmBundle\Entity\User: sha512
    
        providers:
            main:
                id: orm.user_provider
    
        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false
    
            main:
                anonymous: ~
                pattern:   ^/
                form_login:
                    login_path: /login
    
        access_control:
            - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, requires_channel: https }
            - { path: ^/account, roles: ROLE_USER, requires_channel: https }

Remember that your security token is set in your current firewall, so it might be easier to set a global, anonymous
firewall with access control parameters.

Although the security.yml file may specify the Bravo3\OrmBundle\Entity\User class as an encoder, you don't need to use
this class as your user class. Your user class should extend the OrmBundle User class, however. You can change the 
user class in the ORM config:

    orm:
        user_class: SomeNamespace\User

User Authentication
-------------------
By default if you use Symfony's firewall to intercept form logins, the security token will contain the user entity
and get serialised in the users session. Most ORM's don't return the actual user entity, however, but will return a
proxy of that entity. This means that the serialised object in your session is now a proxy.

This creates a bit of overhead, now you've got a lot of serialised information in a session but you also need to
deserialise a proxy. Proxy classes aren't PSR compliant, and in fact, there might not even be a copy of the proxy on
the filesystem. To deserialise the entity, you need to bootstrap another autoloader to either load the proxy cache or
regenerate the proxy. This is what the Doctrine bundle to handle serialised user entities.

The ORM bundle does not include a bootstrapped autoloader. Instead, it offers a security manager service that will
create security tokens for you and log users in and out. When it creates the token it stores only the username, not
the full user entity. This makes your session a lot less cumbersome and does not require a second autoloader.

To test credentials, get the token, user object or to login/logout a user, use the `orm.security_manager` service.

User Commands
-------------
Console commands are included to create & delete users, along with change their password and roles. These commands are
registered in the 'user:' namespace. 

    app/console user:create
    
The list of user roles the commands offer you is configurable in the orm.user_roles configuration:

    orm:
        user_roles: [ ROLE_USER, ROLE_AWESOME ]
        