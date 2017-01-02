<?php
namespace Core\View\Helper;

use Exception as PhpException;
use DateTime;
use Zend\View\Helper\AbstractHelper;

class Date extends AbstractHelper
{

    /**
     * @param mixed $input
     * @param string $format
     * @return string
     */
    public function __invoke($input, $format)
    {
        if ($input instanceof DateTime) {
            return $input->format($format);
        }

        try {

            $date = new DateTime($input);
            return $date->format($format);

        } catch (PhpException $e) {
            return 'invalid date';
        }
    }

}
