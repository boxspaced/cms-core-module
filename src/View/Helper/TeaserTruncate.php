<?php
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class TeaserTruncate extends AbstractHelper
{

    /**
     * @param string $string
     * @param int $chars
     * @return string
     */
    public function __invoke($string, $chars = 80)
    {
        $string = trim(strip_tags($string));
        $string = wordwrap($string, $chars, PHP_EOL);

        $firstBreakPosition = strpos($string, PHP_EOL);

        if (false !== $firstBreakPosition) {
            return substr($string, 0, $firstBreakPosition);
        }

        return $string;
    }

}
