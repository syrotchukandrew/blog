<?php
/**
 * Created by PhpStorm.
 * User: acer
 * Date: 28/01/16
 * Time: 16:14
 */

namespace AppBundle\Twig;


class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('dots3', array($this, 'dots3'))
            );
    }

    public function dots3($content, $limit = 25)
    {
        $words = explode(' ', (trim($content)));
        $countWords = count($words);
        if ($countWords < $limit) {
            $lim = $countWords;
        } else {
            $lim = $limit;
        }
        $words[($lim-1)] .= '...<em>Read More</em>...';
        $strResult = '';
        for ($i = 0; $i < $lim; $i++) {
            $strResult .= $words[$i].' ';
        }

        return $strResult;
    }


    public function getName()
    {
        return 'app_extension';
    }
}