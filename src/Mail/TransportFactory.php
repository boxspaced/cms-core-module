<?php
namespace Core\Mail;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\TransportInterface;

class TransportFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return TransportInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $transport = new $requestedName();

        if (Smtp::class === $requestedName) {

            $config = $container->get('config');
            $options = isset($config['mail']['transport'][$requestedName]['options']) ? $config['mail']['transport'][$requestedName]['options'] : [];

            $transport->setOptions(new SmtpOptions($options));
        }

        return $transport;
    }

}
