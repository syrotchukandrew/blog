<?php


namespace AppBundle\Twig;

use Symfony\Component\Intl\Intl;
use Symfony\Component\DependencyInjection\ContainerInterface;


class AppExtension extends \Twig_Extension
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dots3', array($this, 'dots3'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('facebookButton', array($this, 'getFacebookLikeButton'), array('is_safe' => array('html'))),
        );
    }

    // https://developers.facebook.com/docs/reference/plugins/like/
    public function getFacebookLikeButton($parameters = array())
    {
        // default values, you can override the values by setting them
        $parameters = $parameters + array(
                'url' => null,
                'locale' => 'uk',
                'send' => false,
                'width' => 300,
                'showFaces' => false,
                'layout' => 'button_count',
            );

        return $this->container->get('app.socialBarHelper')->facebookButton($parameters);
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
        $words[($lim - 1)] .= '...<em>Read More</em>...';
        $strResult = '';
        for ($i = 0; $i < $lim; $i++) {
            $strResult .= $words[$i] . ' ';
        }

        return $strResult;
    }

    public function getName()
    {
        return 'app_extension';
    }
}